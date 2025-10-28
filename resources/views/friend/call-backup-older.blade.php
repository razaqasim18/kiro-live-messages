@extends('layouts.master')

@section('title', 'Video Call')

@section('css')
    <link href="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet">

    <style>
        /* 🎥 Player Container */
        .player {
            position: relative;
            width: 100%;
            height: 300px;
            background-color: #000;
            border-radius: 10px;
            overflow: hidden;
        }

        /* 👤 Avatar Placeholder (Centered Circle) */
        .player img.avatar-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            opacity: 0.85;
            transition: opacity 0.3s ease;
        }

        /* 🧍 Player Name Below Each Video */
        .player-name {
            text-align: center;
            margin-top: 8px;
            font-weight: 600;
            font-size: 15px;
        }

        /* 🎧 Mic Mute Status Icon (Overlay on Video) */
        .mic-status {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            border-radius: 50%;
            padding: 6px;
            font-size: 18px;
            display: none;
        }

        /* 🎛️ Control Buttons (Mic / Camera / Leave) */
        .control-btn {
            font-size: 18px;
            padding: 10px 16px;
            border-radius: 10px;
            margin: 0 5px;
        }

        .control-btn i {
            vertical-align: middle;
            margin-right: 6px;
        }

        /* 🧩 Remote Player Container */
        .remote-player-wrapper {
            display: inline-block;
            margin: 0px;
            width: 100%;
            text-align: center;
        }

        /* 🎥 Remote Video Group Layout */
        .video-group {
            margin-top: 20px;
            text-align: center;
        }

        /* 🌈 Transitions for smoother appearance */
        .player video,
        .player img.avatar-placeholder {
            transition: all 0.3s ease;
        }

        /* ensure Agora's video sits under the avatar */
        .player video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            position: relative;
        }

        /* keep avatar above video */
        .player img.avatar-placeholder {
            z-index: 2;
        }
    </style>

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Friends
        @endslot
    @endcomponent

    {{-- ✅ Hidden Agora credentials --}}
    <input type="hidden" id="appid" value="{{ $appId }}">
    <input type="hidden" id="joine_id" value="{{ $joine_id }}">
    <input type="hidden" id="channelname" value="{{ $channelname }}">
    <input type="hidden" id="token" value="{{ $token }}">

    <div class="alert alert-info">
        <strong>Channel Created:</strong> {{ $channelname }}<br>
        <strong>Join Link:</strong>
        <a href="{{ route('friends.call.join', [
            'id' => base64_encode($joine_id),
            'channelname' => $channelname,
        ]) }}"
            target="_blank">
            {{ route('friends.call.join', [
                'id' => base64_encode($joine_id),
                'channelname' => $channelname,
            ]) }}
        </a>
    </div>

    <div class="row video-group">
        <audio id="ringtone" loop>
            <source src="{{ asset('assets/sounds/ringtone.mp3') }}" type="audio/mpeg">
        </audio>
        {{-- ✅ Local Player --}}
        <div class="col-6">
            <p id="local-player-name" class="player-name"></p>
            <div id="local-player" class="player">
                <img id="local-avatar" class="avatar-placeholder"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                    alt="Your Avatar">
            </div>
        </div>

        {{-- ✅ Remote Players --}}
        {{-- ✅ Remote Placeholder (Before Join) --}}
        <div class="col-6">
            <div id="remote-playerlist">
                <div id="waiting-remote" class="remote-player-wrapper">
                    <p class="player-name">Calling {{ $friend->name ?? 'User' }}...</p>
                    <div class="player">
                        <img class="avatar-placeholder"
                            src="https://ui-avatars.com/api/?name={{ urlencode($friend->name ?? 'User') }}&background=random"
                            alt="Remote Avatar">
                        <div class="text-light text-center mt-2">📞 Ringing...</div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ✅ Control Buttons --}}
        <div class="col-12 mt-3 mb-3 text-center">
            <button id="btn-mic" class="btn btn-primary control-btn">
                <i id="mic-icon" class="mdi mdi-microphone"></i>
            </button>
            <button id="btn-camera" class="btn btn-secondary control-btn">
                <i id="camera-icon" class="mdi mdi-video"></i>
            </button>
            <button id="btn-leave" class="btn btn-danger control-btn">
                <i class="mdi mdi-logout"></i>
            </button>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>

    <script>
        const client = AgoraRTC.createClient({
            mode: "rtc",
            codec: "vp8"
        });
        const currentUser = {
            id: {{ auth()->id() }},
            name: "{{ auth()->user()->name }}"
        };

        let localTracks = {
            videoTrack: null,
            audioTrack: null
        };
        let remoteUsers = {};

        const options = {
            appid: document.getElementById("appid").value,
            channel: document.getElementById("channelname").value,
            token: document.getElementById("token").value || null
        };

        // ✅ Join Channel
        join();

        async function join() {
            try {
                document.getElementById("ringtone").play().catch(e => console.log("Autoplay blocked:", e));

                client.on("user-published", handleUserPublished);
                client.on("user-unpublished", handleUserUnpublished);
                client.on("user-left", handleUserLeft);

                const uid = await client.join(options.appid, options.channel, options.token, null);

                localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
                localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

                const localAvatar = document.getElementById("local-avatar");
                localAvatar.src =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=random`;
                localAvatar.style.display = "none";

                localTracks.videoTrack.play("local-player");
                document.getElementById("local-player-name").textContent = currentUser.name;

                await client.publish(Object.values(localTracks));
                console.log("✅ Joined & Published:", options.channel);
            } catch (err) {
                console.error("❌ Error joining:", err);
            }
        }

        // ✅ Handle Remote User Publish
        async function handleUserPublished(user, mediaType) {

            // Stop ringing sound & remove waiting placeholder
            document.getElementById("ringtone").pause();
            document.getElementById("waiting-remote")?.remove();

            remoteUsers[user.uid] = user;
            await client.subscribe(user, mediaType);

            const displayName = user.userName || `User ${user.uid}`;
            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=random`;

            let playerWrapper = document.getElementById(`player-wrapper-${user.uid}`);
            if (!playerWrapper) {
                playerWrapper = document.createElement("div");
                playerWrapper.id = `player-wrapper-${user.uid}`;
                playerWrapper.classList.add("remote-player-wrapper");
                playerWrapper.innerHTML = `
            <p class="player-name">${displayName}
                <span id="mute-icon-${user.uid}" class="text-danger ms-1" style="display:none;">
                    <i class="mdi mdi-microphone-off"></i>
                </span>
            </p>
            <div id="player-${user.uid}" class="player">
                <img id="avatar-${user.uid}" class="avatar-placeholder"
                     src="${avatarUrl}" alt="${displayName} Avatar">
            </div>
        `;
                document.getElementById("remote-playerlist").appendChild(playerWrapper);
            }

            if (mediaType === "video" && user.videoTrack) {
                user.videoTrack.play(`player-${user.uid}`);
                document.getElementById(`avatar-${user.uid}`).style.display = "none";

                user.videoTrack.on("track-ended", () => {
                    document.getElementById(`avatar-${user.uid}`).style.display = "block";
                });

                user.videoTrack.on("track-state-changed", (state) => {
                    const avatar = document.getElementById(`avatar-${user.uid}`);
                    if (avatar) avatar.style.display = state === "active" ? "none" : "block";
                });
            }

            if (mediaType === "audio" && user.audioTrack) {
                user.audioTrack.play();
                observeAudioMuteState(user);
            }
        }

        // ✅ Detect Remote Mic Mute (Safe)
        function observeAudioMuteState(user) {
            const muteIcon = document.getElementById(`mute-icon-${user.uid}`);

            // Listen to track state changes instead of getStats
            user.audioTrack.on("track-state-changed", (state) => {
                if (muteIcon) {
                    if (state === "active") {
                        muteIcon.style.display = "none";
                    } else {
                        muteIcon.style.display = "inline";
                    }
                }
            });
        }

        // ✅ Handle Unpublish
        function handleUserUnpublished(user, mediaType) {
            if (mediaType === "video") {
                const avatar = document.getElementById(`avatar-${user.uid}`);
                if (avatar) avatar.style.display = "block";
            }
            if (mediaType === "audio") {
                const muteIcon = document.getElementById(`mute-icon-${user.uid}`);
                if (muteIcon) muteIcon.style.display = "inline";
            }
        }

        // ✅ Handle Remote User Leave
        async function handleUserLeft(user) {
            delete remoteUsers[user.uid];
            document.getElementById(`player-wrapper-${user.uid}`)?.remove();
        }

        // ✅ Leave Button
        async function leave() {
            for (const track of Object.values(localTracks)) {
                if (track) {
                    track.stop();
                    track.close();
                }
            }
            remoteUsers = {};
            document.getElementById("remote-playerlist").innerHTML = "";
            await client.leave();

            document.getElementById("local-player-name").textContent = "";
            console.log("👋 Left channel");
            window.location.href = "/friends";
        }
        document.getElementById("btn-leave").addEventListener("click", leave);

        // ✅ Mic Toggle
        const micButton = document.getElementById("btn-mic");
        let micMuted = false;
        micButton.addEventListener("click", async () => {
            if (!localTracks.audioTrack) return;
            micMuted = !micMuted;
            await localTracks.audioTrack.setEnabled(!micMuted);
            micButton.innerHTML = micMuted ?
                `<i class="mdi mdi-microphone-off"></i>` :
                `<i class="mdi mdi-microphone"></i>`;
            micButton.classList.toggle("btn-secondary", micMuted);
            micButton.classList.toggle("btn-primary", !micMuted);
        });

        // ✅ Fixed Camera Toggle — keeps local avatar visible when camera is off
        const cameraButton = document.getElementById("btn-camera");
        let cameraOff = false;

        cameraButton.addEventListener("click", async () => {
            if (!localTracks.videoTrack) return;

            cameraOff = !cameraOff;
            const avatar = document.getElementById("local-avatar");
            const playerContainer = document.getElementById("local-player");

            // ensure avatar z-index so it sits above video
            avatar.style.zIndex = 2;

            // find the video element that Agora inserted (if any)
            const videoEl = playerContainer.querySelector('video');

            if (cameraOff) {
                // Turn camera off (stop sending)
                try {
                    await localTracks.videoTrack.setEnabled(false);
                } catch (e) {
                    console.warn('setEnabled(false) failed', e);
                }

                // hide the local video element if present
                if (videoEl) {
                    videoEl.style.display = 'none';
                }

                // ensure avatar is present and visible
                if (!playerContainer.contains(avatar)) {
                    playerContainer.appendChild(avatar);
                }
                avatar.style.display = 'block';
            } else {
                // Turn camera on
                avatar.style.display = 'none';

                try {
                    await localTracks.videoTrack.setEnabled(true);
                } catch (e) {
                    console.warn('setEnabled(true) failed', e);
                }

                // (Re)play the track to recreate / show the <video> element
                try {
                    await localTracks.videoTrack.play('local-player');
                } catch (e) {
                    console.warn('videoTrack.play error', e);
                }

                // ensure video element is visible
                const newVideo = playerContainer.querySelector('video');
                if (newVideo) newVideo.style.display = 'block';
            }

            // update button UI
            cameraButton.innerHTML = cameraOff ?
                `<i id="camera-icon" class="mdi mdi-video-off"></i>` :
                `<i id="camera-icon" class="mdi mdi-video"></i>`;
            cameraButton.classList.toggle("btn-danger", cameraOff);
            cameraButton.classList.toggle("btn-secondary", !cameraOff);
        });
    </script>


@endsection
