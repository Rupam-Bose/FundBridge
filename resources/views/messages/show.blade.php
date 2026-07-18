@extends('layouts.header')

@section('page-title', 'Chat — ' . $partner->name)
@section('page-subtitle', ucfirst($partner->role) . ($partner->company_name ? ' · ' . $partner->company_name : ''))

@section('content')

<div style="max-width:860px;margin:0 auto;display:flex;flex-direction:column;height:calc(100vh - 160px);">

    <!-- Chat Header -->
    <div class="panel-card" style="margin-bottom:16px;padding:16px 24px;display:flex;align-items:center;gap:14px;">
        <a href="{{ route('messages.index') }}"
           style="color:var(--muted);text-decoration:none;font-size:14px;transition:color .2s;"
           onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--muted)'">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <img src="{{ $partner->avatarUrl() }}" alt="{{ $partner->name }}"
            style="width:42px;height:42px;border-radius:50%;border:2px solid var(--green);">
        <div style="flex:1;min-width:0;">
            <div style="font-size:15px;font-weight:700;color:var(--text);">{{ $partner->name }}</div>
            <div style="font-size:12px;color:var(--muted);">
                <span class="badge badge-{{ $partner->role }}" style="padding:2px 7px;font-size:9px;">{{ ucfirst($partner->role) }}</span>
                @if ($partner->company_name) <span style="margin-left:5px;">{{ $partner->company_name }}</span> @endif
            </div>
        </div>

         <!-- Video Call Button  -->
        <a href="{{ route('video.call', $partner->id) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:50px;border:none;background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;font-weight:700;font-size:13px;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
            <i class="fa-solid fa-video"></i> Video Call
        </a>
    </div>

     <!-- Messages Area -->
    <div class="panel-card" style="flex:1;display:flex;flex-direction:column;min-height:0;padding:0;overflow:hidden;">

        <div id="chatBox"
             style="flex:1;overflow-y:auto;padding:20px 24px;display:flex;flex-direction:column;gap:12px;">

            @forelse ($messages as $msg)
            @php $mine = $msg->sender_id === Auth::id(); @endphp
            <div class="msg-row {{ $mine ? 'mine' : 'theirs' }}" data-id="{{ $msg->id }}">
                @if (!$mine)
                <img src="{{ $msg->sender->avatarUrl() }}" style="width:32px;height:32px;border-radius:50%;flex-shrink:0;align-self:flex-end;" alt="">
                @endif
                <div class="bubble {{ $mine ? 'bubble-mine' : 'bubble-theirs' }}">
                    {{ $msg->content }}
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:var(--muted);font-size:14px;margin:auto;">
                <i class="fa-solid fa-comment" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                Start the conversation!
            </div>
            @endforelse

        </div>

         <!-- Input Area -->
        <div style="padding:16px 24px;border-top:1px solid var(--border);">
            <form id="msgForm" method="POST" action="{{ route('messages.send', $partner->id) }}"
                  style="display:flex;gap:10px;align-items:flex-end;">
                @csrf
                <textarea name="content" id="msgInput" rows="1"
                    placeholder="Type a message... (Enter to send)"
                    style="flex:1;padding:12px 16px;border-radius:16px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;font-family:inherit;outline:none;resize:none;min-height:44px;max-height:140px;transition:border-color .2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--green)'"
                    onblur="this.style.borderColor='var(--border)'"
                    required></textarea>
                <button type="submit" id="sendBtn"
                    style="width:44px;height:44px;border-radius:50%;border:none;background:var(--green);color:#000;font-size:16px;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                    onmouseover="this.style.background='#00b982'" onmouseout="this.style.background='var(--green)'">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
{{-- Chat bubble styles loaded via resources/css/messages.css --}}
@endpush

@push('scripts')
<script>
const chatBox   = document.getElementById('chatBox');
const msgForm   = document.getElementById('msgForm');
const msgInput  = document.getElementById('msgInput');
const partnerId = {{ $partner->id }};
let lastId      = {{ $messages->last()?->id ?? 0 }};

// Scroll to bottom
function scrollBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}
scrollBottom();

// Auto-grow textarea
msgInput.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 140) + 'px';
});

// Send on Enter (Shift+Enter = new line)
msgInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// AJAX Send
msgForm.addEventListener('submit', function (e) {
    e.preventDefault();
    sendMessage();
});

async function sendMessage() {
    const content = msgInput.value.trim();
    if (!content) return;

    msgInput.value = '';
    msgInput.style.height = 'auto';

    // Optimistic render
    appendMessage({
        message: content,
        sender_id: {{ Auth::id() }},
        created_at: new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}),
        mine: true
    });
    scrollBottom();

    try {
        const response = await fetch('{{ route('messages.send', $partner->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content }),
        });
        const data = await response.json();
        if (data.id) lastId = data.id;
    } catch (err) {
        console.error('Send failed:', err);
    }
}

function appendMessage(msg) {
    const mine = msg.mine ?? msg.sender_id === {{ Auth::id() }};
    const row  = document.createElement('div');
    row.className = 'msg-row ' + (mine ? 'mine' : 'theirs');
    row.dataset.id = msg.id || 0;

    const bubble = document.createElement('div');
    bubble.className = 'bubble ' + (mine ? 'bubble-mine' : 'bubble-theirs');
    bubble.innerHTML = `${escHtml(msg.message)}<div class="bubble-time">${msg.created_at}</div>`;
    row.appendChild(bubble);
    chatBox.appendChild(row);
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Polling: check for new messages every 4 seconds
setInterval(async () => {
    try {
        const res  = await fetch(`{{ url('/messages/' . $partner->id . '/poll') }}?last_id=${lastId}`);
        const msgs = await res.json();
        msgs.forEach(msg => {
            if (!document.querySelector('[data-id="' + msg.id + '"]')) {
                appendMessage(msg);
                lastId = Math.max(lastId, msg.id);
            }
        });
        if (msgs.length) scrollBottom();
    } catch (e) {}
}, 4000);
</script>
@endpush

@endsection
