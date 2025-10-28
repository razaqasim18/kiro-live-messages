import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import axios from 'axios';
window.axios = axios;


window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'ap2',
    forceTLS: true,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    withCredentials: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }
});

console.log("app");

// import { createApp } from 'vue';
// import VideoCall from './components/VideoCall.vue';

// const app = createApp({});

// app.component('video-call', VideoCall);

// app.mount('#app');
