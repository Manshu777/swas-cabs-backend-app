

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

// Configure Laravel Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY, // ya process.env.MIX_PUSHER_APP_KEY agar Mix
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER, // ya process.env.MIX_PUSHER_APP_CLUSTER
    forceTLS: true
});

// Example: Listen to chat channel
window.Echo.channel('chat')
    .listen('MessageSent', (e) => {
        console.log('New message received:', e.message);
        let chatBox = document.getElementById('chat-box');
        if (chatBox) {
            let div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `<strong>${e.message.user_id == 0 ? 'Guest' : e.message.user.name}:</strong> ${e.message.message}`;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
