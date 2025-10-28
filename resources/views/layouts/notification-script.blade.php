<script>
    $(document).ready(function() {
        /* 🔔 Ringing + Modal Control Variables */
        let ringing = false;
        let ringingTimer = null;
        let ringingInterval = null;
        let audioCtx = null;

        /* 🔊 Start Ringing */
        function callingStartRinging() {
            if (ringing) return;
            ringing = true;

            const overlay = document.getElementById('ringing-overlay');
            if (overlay) overlay.style.display = 'flex';

            if (!audioCtx) {
                audioCtx = new(window.AudioContext || window.webkitAudioContext)();
            }
        }

        /* 🛑 Stop Ringing */
        function callingStopRinging() {
            ringing = false;
            if (ringingInterval) clearInterval(ringingInterval);
            if (ringingTimer) clearTimeout(ringingTimer);

            const overlay = document.getElementById('ringing-overlay');
            if (overlay) overlay.style.display = 'none';
        }

        /* 🎵 Tone Generator (simple ring sound) */
        function callingPlayTone(freq = 440, duration = 0.12) {
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

        /* 🧍 Get authenticated user's ID from Blade */
        const authUserId = {{ auth()->id() }};

        /* 📡 Listen for incoming call events */
        Echo.private(`calluser.${authUserId}`)
            .listen('.incoming-call', (e) => {

                callingStartRinging();

                // 🔁 Repeat ring tones
                ringingInterval = setInterval(() => {
                    callingPlayTone(880, 0.12);
                    setTimeout(() => callingPlayTone(660, 0.12), 180);
                }, 1000);

                const caller = e.caller;
                const receiver = e.receiver;
                const joinUrl = e.joinUrl;

                // Show popup
                Swal.fire({
                    title: `📞 Incoming Call`,
                    html: `<strong>${caller.name}</strong> is calling you...`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Join Call',
                    cancelButtonText: 'Decline',
                    allowOutsideClick: false,
                    timer: 60000, // 1 minute
                    timerProgressBar: true,
                    didOpen: () => {
                        // ⏱ Auto-decline after 1 minute
                        ringingTimer = setTimeout(() => {
                            if (ringing) {
                                console.log("⏰ No response — auto-declining call...");
                                Swal.close(); // Close the alert
                                callingStopRinging();

                                axios.get(
                                        `/friends/call/end/notification/${caller.id}/${receiver.id}`
                                    )
                                    .then(response => {
                                        console.log(
                                            "📴 Auto-declined successfully:",
                                            response.data);
                                    })
                                    .catch(error => {
                                        console.error(
                                            "❌ Error auto-declining call:",
                                            error.response ? error.response
                                            .data : error);
                                    });
                            }
                        }, 60000); // 1 minute = 60000 ms
                    }
                }).then(result => {
                    clearTimeout(ringingTimer);
                    callingStopRinging();
                    // stopRinging();


                    if (result.isConfirmed) {
                        window.location.href = joinUrl;
                    } else {
                        axios.get(`/friends/call/end/notification/${caller.id}/${receiver.id}`)
                            .then(response => {
                                console.log("📴 Call declined manually:", response.data);
                            })
                            .catch(error => {
                                console.error("❌ Error declining call:", error.response ? error
                                    .response.data : error);
                            });
                    }
                });
            });



    });
</script>
