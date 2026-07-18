<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call — FundBridge</title>
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Video Call Styles --}}
    <link rel="stylesheet" href="{{ asset('css/video-call.css') }}">
</head>
<body>

<header class="video-header">
    <div class="video-logo"><span>Fund</span>Bridge</div>
    <div class="video-call-info">
        Video Call with <strong>{{ $partner->name }}</strong>
        <span class="video-call-timer">
            <span id="callTimer">00:00</span>
        </span>
    </div>
    <button onclick="window.close()" class="video-close-btn">
        <i class="fa-solid fa-xmark"></i> Close
    </button>
</header>

<div id="jitsi-container"></div>

<button class="end-btn" onclick="endCall()">
    <i class="fa-solid fa-phone-slash"></i> End Call
</button>

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
