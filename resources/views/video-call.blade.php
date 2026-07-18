<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call — FundBridge</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#061426; font-family:'Inter',sans-serif; color:#fff; height:100vh; display:flex; flex-direction:column; }
        header {
            padding:14px 24px;
            background:#0b1c34;
            border-bottom:1px solid rgba(255,255,255,.08);
            display:flex; align-items:center; justify-content:space-between;
        }
        .logo { font-size:18px; font-weight:900; }
        .logo span { color:#00d99c; }
        .call-info { font-size:14px; color:#9eacc2; }
        .call-info strong { color:#fff; }
        #jitsi-container { flex:1; }
        .end-btn {
            position:fixed; bottom:32px; left:50%; transform:translateX(-50%);
            padding:13px 32px; border-radius:50px; border:none;
            background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff;
            font-size:15px; font-weight:700; cursor:pointer;
            display:flex; align-items:center; gap:8px;
            box-shadow:0 4px 20px rgba(239,68,68,.4);
            z-index:999;
            transition:all .2s;
        }
        .end-btn:hover { background:linear-gradient(135deg,#dc2626,#b91c1c); }
    </style>
</head>
<body>

<header>
    <div class="logo"><span>Fund</span>Bridge</div>
    <div class="call-info">
        Video Call with <strong>{{ $partner->name }}</strong>
        <span style="margin-left:12px;font-size:11px;background:rgba(0,217,156,.15);color:#00d99c;padding:3px 10px;border-radius:50px;">
            <span id="callTimer">00:00</span>
        </span>
    </div>
    <button onclick="window.close()" style="padding:8px 16px;border-radius:50px;border:1px solid rgba(255,255,255,.15);background:transparent;color:#9eacc2;cursor:pointer;font-size:13px;">
        <i class="fa-solid fa-xmark"></i> Close
    </button>
</header>

<div id="jitsi-container"></div>

<button class="end-btn" onclick="endCall()">
    <i class="fa-solid fa-phone-slash"></i> End Call
</button>

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- Jitsi Meet External API --}}
<script src="https://meet.jit.si/external_api.js"></script>

<script>
const roomName = '{{ $roomName }}';
const myName   = '{{ Auth::user()->name }}';

const api = new JitsiMeetExternalAPI('meet.jit.si', {
    roomName:  roomName,
    width:     '100%',
    height:    '100%',
    parentNode: document.getElementById('jitsi-container'),
    userInfo: {
        displayName: myName,
    },
    configOverwrite: {
        startWithAudioMuted: false,
        startWithVideoMuted: false,
        disableDeepLinking:  true,
    },
    interfaceConfigOverwrite: {
        SHOW_JITSI_WATERMARK:      false,
        SHOW_WATERMARK_FOR_GUESTS: false,
        TOOLBAR_BUTTONS: [
            'microphone', 'camera', 'desktop', 'hangup',
            'chat', 'raisehand', 'videoquality', 'tileview'
        ],
    }
});

api.addEventListener('readyToClose', () => window.close());

// Call timer
let secs = 0;
setInterval(() => {
    secs++;
    const m = String(Math.floor(secs/60)).padStart(2,'0');
    const s = String(secs%60).padStart(2,'0');
    document.getElementById('callTimer').textContent = `${m}:${s}`;
}, 1000);

function endCall() {
    api.executeCommand('hangup');
    setTimeout(() => window.close(), 500);
}
</script>

</body>
</html>
