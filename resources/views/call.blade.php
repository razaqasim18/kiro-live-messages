<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call Room</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.17.0.js"></script>
    <style>
        #video-streams {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 10px;
        }

        .video-container {
            position: relative;
            width: 300px;
            height: 220px;
            background-color: #000;
            border: 2px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        .video-player {
            width: 100%;
            height: 100%;
        }

        .username-wrapper {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            text-align: center;
            color: #fff;
            font-size: 14px;
            padding: 2px 0;
        }

        #controls {
            text-align: center;
            margin-top: 10px;
        }

        #controls button {
            margin: 0 5px;
            padding: 10px 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div id="video-streams"></div>
    <div id="controls">
        <button id="start-btn">Start Call</button>
        <button id="camera-btn">Toggle Camera</button>
        <button id="mic-btn">Toggle Mic</button>
        <button id="leave-btn">Leave Call</button>
    </div>

    <script>
        const APP_ID = "4cdd28a9ccec4f39861f5bb47ba3f54d";
        const CHANNEL = new URLSearchParams(window.location.search).get('channel');
        const NAME = new URLSearchParams(window.location.search).get('name') || 'You';

        let UID;
        let TOKEN;

        const client = AgoraRTC.createClient({
            mode: 'rtc',
            codec: 'vp8'
        });
        let localTracks = {
            audioTrack: null,
            videoTrack: null
        };
        let remoteUsers = {};

        async function fetchTokenAndInit() {
            try {
                const response = await fetch(`/friend/get-token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        channel: CHANNEL
                    })
                });

                const data = await response.json();
                TOKEN = data.token;
                UID = data.uid;

                initializeCall();
            } catch (error) {
                console.error('Error fetching token or joining channel:', error);
            }
        }

        async function initializeCall() {
            try {
                UID = await client.join(APP_ID, CHANNEL, TOKEN, UID);
                console.log(`Joined channel ${CHANNEL} with UID: ${UID}`);

                localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
                localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

                displayLocalStream();
                await client.publish(Object.values(localTracks));

                client.on('user-published', handleUserJoined);
                client.on('user-left', handleUserLeft);

                document.getElementById('leave-btn').addEventListener('click', leaveAndRemoveLocalStream);
                document.getElementById('camera-btn').addEventListener('click', toggleCamera);
                document.getElementById('mic-btn').addEventListener('click', toggleMic);
            } catch (error) {
                console.error('Agora join error:', error);
            }
        }

        function displayLocalStream() {
            let player = `<div class="video-container" id="user-container-${UID}">
                <div class="video-player" id="user-${UID}"></div>
                <div class="username-wrapper"><span class="user-name">${NAME}</span></div>
            </div>`;
            document.getElementById('video-streams').insertAdjacentHTML('beforeend', player);
            localTracks.videoTrack.play(`user-${UID}`);
        }

        async function handleUserJoined(user, mediaType) {
            remoteUsers[user.uid] = user;
            await client.subscribe(user, mediaType);

            if (mediaType === 'video') {
                let existingPlayer = document.getElementById(`user-container-${user.uid}`);
                if (!existingPlayer) {
                    let player = `<div class="video-container" id="user-container-${user.uid}">
                <div class="video-player" id="user-${user.uid}"></div>
                <div class="username-wrapper"><span class="user-name">Remote</span></div>
            </div>`;
                    document.getElementById('video-streams').insertAdjacentHTML('beforeend', player);
                }
                user.videoTrack.play(`user-${user.uid}`);
            }

            if (mediaType === 'audio') {
                user.audioTrack.play();
            }
        }

        function handleUserLeft(user) {
            delete remoteUsers[user.uid];
            document.getElementById(`user-container-${user.uid}`)?.remove();

            // Remove the remote user's video container from the DOM
            const remoteContainer = document.getElementById(`user-container-${user.uid}`);
            if (remoteContainer) {
                remoteContainer.remove();
            }
        }

        async function leaveAndRemoveLocalStream() {
            for (let track of Object.values(localTracks)) {
                track.stop();
                track.close();
            }
            await client.leave();
            document.getElementById(`user-container-${UID}`)?.remove();
            // Disable controls, enable start
            document.getElementById('camera-btn').disabled = true;
            document.getElementById('mic-btn').disabled = true;
            document.getElementById('leave-btn').disabled = true;
            document.getElementById('start-btn').disabled = false;
        }

        async function toggleCamera(e) {
            if (localTracks.videoTrack) {
                const enabled = localTracks.videoTrack.enabled;
                await localTracks.videoTrack.setEnabled(!enabled);
                e.target.style.backgroundColor = enabled ? 'red' : 'white';
            }
        }

        async function toggleMic(e) {
            if (localTracks.audioTrack) {
                const enabled = localTracks.audioTrack.enabled;
                await localTracks.audioTrack.setEnabled(!enabled);
                e.target.style.backgroundColor = enabled ? 'red' : 'white';
            }
        }

        document.getElementById('start-btn').addEventListener('click', async () => {
            await fetchTokenAndInit();

            // Enable control buttons after joining
            document.getElementById('camera-btn').disabled = false;
            document.getElementById('mic-btn').disabled = false;
            document.getElementById('leave-btn').disabled = false;
            document.getElementById('start-btn').disabled = true;
        });
    </script>
</body>

</html>
