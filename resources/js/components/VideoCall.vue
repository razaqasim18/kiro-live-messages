<template>
  <div id="video-call">
    <button @click="startCall">Start Call</button>
    <div ref="localVideo" id="local-video"></div>
    <div ref="remoteVideo" id="remote-video"></div>
  </div>
</template>

<script>
import { createClient, createMicrophoneAndCameraTracks } from 'agora-rtc-sdk-ng';

export default {
  data() {
    return {
      client: null,
      localTracks: [],
      remoteTracks: {},
    };
  },
  methods: {
    async startCall() {
      this.client = createClient({ mode: 'rtc', codec: 'vp8' });

      // Ensure axios is available globally or imported
      // You might need to import axios if not globally available, e.g., import axios from 'axios';
      const appId = process.env.MIX_AGORA_APP_ID;
      const response = await axios.post('/api/agora-token', { channel: 'test-channel' });
      const token = response.data.token;

      await this.client.join(appId, 'test-channel', token, null);

      const [microphoneTrack, cameraTrack] = await createMicrophoneAndCameraTracks();
      this.localTracks = [microphoneTrack, cameraTrack];

      this.localTracks[1].play(this.$refs.localVideo);

      await this.client.publish(this.localTracks);

      // Handle remote users joining/leaving and publishing tracks
      this.client.on('user-published', async (user, mediaType) => {
        await this.client.subscribe(user, mediaType);
        if (mediaType === 'video') {
          const remoteVideoTrack = user.videoTrack;
          this.$nextTick(() => {
            if (this.$refs.remoteVideo) {
              remoteVideoTrack.play(this.$refs.remoteVideo);
            }
          });
        }
        if (mediaType === 'audio') {
          const remoteAudioTrack = user.audioTrack;
          remoteAudioTrack.play();
        }
      });

      this.client.on('user-unpublished', (user, mediaType) => {
        if (mediaType === 'video' && user.videoTrack) {
          user.videoTrack.stop();
        }
        if (mediaType === 'audio' && user.audioTrack) {
          user.audioTrack.stop();
        }
      });
    },
     async leaveCall() {
      // Unpublish local tracks
      await this.client.unpublish(this.localTracks);
      // Stop local tracks
      this.localTracks.forEach(track => track.stop());
      this.localTracks = [];
      // Leave the channel
      await this.client.leave();
      this.client = null;
      console.log('You left the channel');
    },
  },
  // Add a mounted lifecycle hook to clean up when the component is destroyed
  beforeUnmount() {
    if (this.client) {
      this.leaveCall();
    }
  }
};
</script>

<style scoped>
#local-video, #remote-video {
  width: 400px;
  height: 300px;
  background: black;
  margin: 10px;
  border: 1px solid #ccc;
}
</style>
