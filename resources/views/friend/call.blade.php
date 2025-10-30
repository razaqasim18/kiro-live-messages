@extends('layouts.master')

@section('title', 'Video Call')

@section('css')
    <link href="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet">

    <style>
        /* 🎥 Common player box styling */
        .player {
            position: relative;
            width: 100%;
            height: 300px;
            background-color: #000;
            border-radius: 10px;
            overflow: hidden;
        }

        /* 👤 Avatar placeholder shown when camera is off */
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
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        /* 🎙️ Mic mute status icon (bottom right) */
        .mic-status {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            border-radius: 50%;
            padding: 6px;
            font-size: 18px;
            display: none;
            z-index: 3;
        }

        /* 🔔 Ringing animation overlay shown during call setup */
        .ringing-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 3;
        }

        /* 🌀 Pulsing ring animation for avatar */
        .ringing-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 6px solid rgba(255, 255, 255, 0.12);
            animation: ringPulse 1.4s infinite;
            background-size: cover;
            background-position: center;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.45);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 18px rgba(59, 130, 246, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        /* 🏷️ Display name below each player */
        .player-name {
            text-align: center;
            margin-top: 8px;
            font-weight: 600;
            font-size: 15px;
        }

        /* 🎛️ Call control buttons (mic, camera, leave) */
        .control-btn {
            font-size: 18px;
            padding: 10px 16px;
            border-radius: 10px;
            margin: 0 5px;
        }

        .video-group {
            margin-top: 20px;
        }

        /* 📺 Video should fully fill the box */
        .player video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            position: relative;
        }

        /* Remote avatar placeholder shown when remote camera is off */
        .remote-avatar-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            opacity: 0.95;
            z-index: 2;
            display: none;
            /* start hidden by default */
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

    <!-- 🔑 Hidden inputs to pass Agora parameters -->
    <input type="hidden" id="appid" value="{{ $appId }}">
    <input type="hidden" id="remote_user_id" value="{{ $remote_user->id }}">
    <input type="hidden" id="remote_user_name" value="{{ $remote_user->name }}">

    <input type="hidden" id="local_user_id" value="{{ $local_user->id }}">
    <input type="hidden" id="remote_user_name" value="{{ $local_user->name }}">


    <input type="hidden" id="channelname" value="{{ $channelname }}">
    <input type="hidden" id="token" value="{{ $token }}">

    {{-- <div class="alert alert-info">
        <strong>Channel Created:</strong> {{ $channelname }}<br>
        <strong>Join Link:</strong>
        <a href="{{ route('friends.call.join', [
            'id' => base64_encode($id),
            'channelname' => $channelname,
        ]) }}"
            target="_blank">
            {{ route('friends.call.join', [
                'id' => base64_encode($id),
                'channelname' => $channelname,
            ]) }}
        </a>
    </div> --}}

    <div class="row video-group">
        {{-- 🧍 Local Player --}}
        <div class="col-6 text-center">
            <p id="local-player-name" class="player-name"></p>
            <div id="local-player" class="player">
                <!-- Avatar shown when camera is off -->

                <img id="local-avatar" class="avatar-placeholder"
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                    alt="Your Avatar">

                <!-- Mic mute overlay -->
                <div id="local-mic-status" class="mic-status">
                    <i class="mdi mdi-microphone-off"></i>
                </div>
            </div>
        </div>

        {{-- 👥 Remote Player --}}
        <div class="col-6 text-center">
            <p class="player-name">Remote User</p>

            <div id="remote-playerlist">
                <!-- Default avatar shown before remote joins -->
                <div id="remote-ringing-placeholder" class="player">
                    <img class="avatar-placeholder" src="https://ui-avatars.com/api/?name=Remote+User&background=random"
                        alt="Remote Avatar">

                    <!-- Animated ringing overlay -->
                    <div id="ringing-overlay" class="ringing-overlay" style="display:none;">
                        <div id="ringing-avatar" class="ringing-avatar"
                            style="background-image: url('https://ui-avatars.com/api/?name=Remote+User&background=random')">
                        </div>
                    </div>
                </div>

                <!-- Where remote video will be rendered -->
                <div id="remote-player-container" style="display:none;"></div>

                <!-- Mic status for remote user -->
                <div id="remote-mic-status" class="mic-status">
                    <i class="mdi mdi-microphone-off"></i>
                </div>
            </div>
        </div>

        {{-- 🎛️ Controls Section --}}
        <div class="col-12 mt-3 text-center">
            <!-- Mic toggle -->
            <button id="btn-mic" class="btn btn-primary control-btn">
                <i id="mic-icon" class="mdi mdi-microphone"></i>
            </button>

            <!-- Camera toggle -->
            <button id="btn-camera" class="btn btn-secondary control-btn">
                <i id="camera-icon" class="mdi mdi-video"></i>
            </button>

            <!-- Leave button -->
            <button id="btn-leave" class="btn btn-danger control-btn">
                <i class="mdi mdi-logout"></i>
            </button>
        </div>
    </div>


@endsection


@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

    <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.20.0.js"></script>

    <script>
        $(document).ready(function() {
            const authUserId = {{ auth()->id() }};
            let channelname = document.getElementById("channelname").value;
            let local_user_id = document.getElementById("local_user_id").value;
            let remote_user_id = document.getElementById("remote_user_id").value;
            axios.get(`/friends/call/start/notification/${local_user_id}/${remote_user_id}/${channelname}`)
                .then(response => {
                    console.log("📴 Call notification manually:", response.data);
                })
                .catch(error => {
                    console.error("❌ Error notification call:", error.response ? error
                        .response.data : error);
                });



            Echo.private(`enduser.${authUserId}`)
                .listen('.call-ended', (e) => {
                    stopRinging();

                    const decliner = e.decliner; // from broadcast payload
                    console.log("enduser" + decliner);
                    Swal.fire({
                        title: `📴 Call Declined`,
                        html: `<strong>${decliner.name}</strong> declined your call.`,
                        icon: 'info',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        timer: 10000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = "{{ route('friends.index') }}";
                    });
                });
        });


        let ringing = false;
        let ringingTimer = null;
        let ringingInterval = null;
        let audioCtx = null;

        /* 🎬 Initialize Agora Client */
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
        let micMuted = false;

        /* Agora Config from Hidden Inputs */
        const options = {
            appid: document.getElementById("appid").value,
            channel: document.getElementById("channelname").value,
            token: document.getElementById("token").value || null
        };



        /* 🚀 Join Channel Immediately */
        join();

        /* 👇 Join Function */
        async function join() {
            // Set up event listeners for remote actions
            client.on("user-published", handleUserPublished);
            client.on("user-unpublished", handleUserUnpublished);
            client.on("user-left", handleUserLeft);

            // Join the channel
            const uid = await client.join(options.appid, options.channel, options.token, null);

            // Create mic + camera tracks
            localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();

            // Hide avatar and show local video
            document.getElementById("local-avatar").style.display = "none";
            await localTracks.videoTrack.play("local-player");
            document.getElementById("local-player-name").textContent = currentUser.name;

            // Publish local tracks
            await client.publish(Object.values(localTracks));

            // If no peers, trigger ringing
            checkForPeersAndStartRinging();
        }

        /* 🔍 Start ringing if no peers present */
        function checkForPeersAndStartRinging() {
            if (Object.keys(remoteUsers).length === 0) startRinging();
            else stopRinging();
        }

        /* 🧠 When Remote User Publishes a Stream */
        async function handleUserPublished(user, mediaType) {
            const id = user.uid;

            // Keep record of remote users
            remoteUsers[id] = user;

            // Stop ringing when someone appears
            stopRinging();

            // ✅ SweetAlert: Show "User Joined"
            Swal.fire({
                title: '🎥 Friend Joined!',
                text: 'Your friend has joined the call.',
                icon: 'success',
                timer: 2500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });

            // Remove the ringing placeholder
            const placeholder = document.getElementById("remote-ringing-placeholder");
            if (placeholder) placeholder.remove();

            // Prepare remote container
            const container = document.getElementById("remote-player-container");
            container.style.display = "block";
            container.innerHTML = `<div id="player-${id}" class="player"></div>`;

            // Subscribe to remote user
            await client.subscribe(user, mediaType);

            const playerDiv = document.getElementById(`player-${id}`);
            const displayName = user.userName || `User ${id}`;

            // ✅ Create fallback avatar
            const remoteAvatar = document.createElement("img");
            remoteAvatar.className = "remote-avatar-placeholder";
            remoteAvatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=random`;
            remoteAvatar.alt = displayName;
            playerDiv.appendChild(remoteAvatar);

            // Helper to find the Agora <video>
            const findVideo = () => playerDiv.querySelector("video");

            if (mediaType === "video" && user.videoTrack) {
                await user.videoTrack.play(`player-${id}`);

                // Try to show/hide avatar initially
                setTimeout(() => {
                    const v = findVideo();
                    if (v) {
                        v.style.display = "block";
                        remoteAvatar.style.display = "none";
                    } else {
                        remoteAvatar.style.display = "block";
                    }
                }, 500);

                // coins deduction for currentUser (caller)
                startCoinDeduction(document.getElementById("local_user_id").value);

                // ✅ Detect when remote turns off camera
                user.videoTrack.on("track-state-changed", (state) => {
                    const v = findVideo();
                    console.log("Remote camera state:", state);
                    if (state === "disabled") {
                        if (v) v.style.display = "none";
                        remoteAvatar.style.display = "block";
                    } else if (state === "enabled") {
                        if (v) v.style.display = "block";
                        remoteAvatar.style.display = "none";
                    }
                });

                // ✅ Detect if video element gets removed (e.g. remote disable camera fully)
                const observer = new MutationObserver(() => {
                    const v = findVideo();
                    if (!v) {
                        remoteAvatar.style.display = "block";
                    }
                });
                observer.observe(playerDiv, {
                    childList: true,
                    subtree: true
                });
            }

            if (mediaType === "audio" && user.audioTrack) {
                user.audioTrack.play();
            }

            // Mic status observe
            observeMicState(user);
        }




        /* 🎙️ Handle when remote mutes mic */
        function handleUserUnpublished(user, mediaType) {
            if (mediaType === "audio") {
                document.getElementById("remote-mic-status").style.display = "inline";
            }
        }

        /* 👂 Observe remote mic state and update icon */
        function observeMicState(user) {
            const micStatus = document.getElementById("remote-mic-status");
            if (!user.audioTrack) return;

            user.audioTrack.on("track-state-changed", state => {
                micStatus.style.display = (state === "active") ? "none" : "inline";
            });
        }

        /* 🚪 When remote leaves the call */
        async function handleUserLeft(user) {
            delete remoteUsers[user.uid];
        }

        /* 🔚 Leave Call + Cleanup */
        async function leaveCall(msg = null) {
            for (const track of Object.values(localTracks)) {
                if (track) {
                    track.stop();
                    track.close();
                }
            }
            try {
                await client.leave();
            } catch {}

            window.location.href = "{{ route('friends.index') }}";
            Swal.fire({
                title: `📴 Friend Left`,
                html: `Your friend has left the call.`,
                icon: 'info',
                showCancelButton: false,
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                timer: 3000,
                timerProgressBar: true
            }).then(result => {
                if (result.isConfirmed) {
                    // ✅ Build a join URL dynamically with params
                    window.location.href = "{{ route('friends.index') }}";
                }
            });
        }

        /* 🎛️ Button Controls */
        document.getElementById("btn-leave").addEventListener("click", () => leaveCall());

        /* Mic Toggle */
        document.getElementById("btn-mic").addEventListener("click", async () => {
            if (!localTracks.audioTrack) return;
            micMuted = !micMuted;
            await localTracks.audioTrack.setEnabled(!micMuted);
            document.getElementById("local-mic-status").style.display = micMuted ? "inline" : "none";
            const btn = document.getElementById("btn-mic");
            btn.innerHTML = micMuted ? `<i class="mdi mdi-microphone-off"></i>` :
                `<i class="mdi mdi-microphone"></i>`;
            btn.classList.toggle("btn-secondary", micMuted);
            btn.classList.toggle("btn-primary", !micMuted);
        });

        /* Camera Toggle */
        document.getElementById("btn-camera").addEventListener("click", async () => {
            if (!localTracks.videoTrack) return;
            const avatar = document.getElementById("local-avatar");
            const videoTrack = localTracks.videoTrack;
            const cameraOff = avatar.style.display === "block";
            avatar.style.display = cameraOff ? "none" : "block";
            await videoTrack.setEnabled(cameraOff);
        });

        function playTone(freq = 440, duration = 0.12) {
            if (!audioCtx) return;
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.frequency.value = freq;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.2, now);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start(now);
            osc.stop(now + duration);
        }

        /* 🔔 Start Ringing Function */
        function startRinging() {
            if (ringing) return;
            ringing = true;

            const overlay = document.getElementById('ringing-overlay');
            if (overlay) overlay.style.display = 'flex';

            if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();

            // Play alternating tones repeatedly
            ringingInterval = setInterval(() => {
                playTone(880, 0.12);
                setTimeout(() => playTone(660, 0.12), 180);
            }, 1000);

            // Auto cancel after 60 seconds if no one joins
            ringingTimer = setTimeout(() => {
                if (Object.keys(remoteUsers).length === 0) {
                    stopRinging();
                    Swal.fire({
                        title: `Call Cancelled`,
                        html: `No one join the call...`,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        timer: 60000,
                        timerProgressBar: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            // ✅ Build a join URL dynamically with params
                            window.location.href = "{{ route('friends.index') }}";
                        }
                    });
                }
            }, 60000);
        }

        /* 🔕 Stop Ringing Function */
        function stopRinging() {
            if (!ringing) return;
            ringing = false;

            const overlay = document.getElementById('ringing-overlay');
            if (overlay) overlay.style.display = 'none';

            clearInterval(ringingInterval);
            clearTimeout(ringingTimer);
        }

        let coinInterval;

        function startCoinDeduction(callerId) {
            coinInterval = setInterval(() => {
                console.log("startCoinDeduction");
                fetch(`/friends/call/deduct-coins/${callerId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'end') {
                            clearInterval(coinInterval);
                            leaveCall();
                            Swal.fire({
                                title: `Call Ended`,
                                html: `You have no coins left in your account.`,
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                            }).then(() => {
                                window.location.href = "{{ route('friends.index') }}";
                            });
                        }
                        $("#coinid").text("Coins: " + data.remaining);
                    })
                    .catch(console.error);
            }, 90000); // every 1.5 min
        }


        function stopCoinDeduction() {
            if (coinInterval) clearInterval(coinInterval);
        }
    </script>
@endsection
