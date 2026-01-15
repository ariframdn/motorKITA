import './bootstrap';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;

// Setup Laravel Echo for real-time notifications (only if Pusher is configured)
window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
            forceTLS: true,
            encrypted: true,
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        axios.post('/broadcasting/auth', {
                            socket_id: socketId,
                            channel_name: channel.name
                        })
                        .then(response => {
                            callback(false, response.data);
                        })
                        .catch(error => {
                            callback(true, error);
                        });
                    }
                };
            },
        });

        // Listen for notifications
        if (typeof window.userId !== 'undefined' && window.Echo) {
            window.Echo.private(`user.${window.userId}`)
                .listen('.notification.sent', (e) => {
                    // Update notification count
                    if (typeof updateNotificationCount === 'function') {
                        updateNotificationCount();
                    }
                    
                    // Show notification toast
                    if (typeof showNotificationToast === 'function') {
                        showNotificationToast(e);
                    }
                });
        }
    } catch (error) {
        console.warn('Failed to initialize Pusher/Echo:', error);
    }
} else {
    console.warn('Pusher key not configured. Real-time notifications disabled.');
}

Alpine.start();
