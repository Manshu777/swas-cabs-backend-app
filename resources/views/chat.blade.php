<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Chat (Guest)</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Pusher JS -->




    <!-- Laravel Echo (if compiled with app.js) -->
@vite('resources/js/app.js')
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Real-Time Chat (Guest)</h1>

        <!-- Chat Box -->
        <div id="chat-box" class="border rounded p-4 h-96 overflow-y-scroll bg-white shadow">
            @foreach($messages as $message)
                <div class="mb-2">
                    <strong>{{ $message->user_id == 0 ? 'Guest' : $message->user->name }}:</strong>
                    <span>{{ $message->message }}</span>
                </div>
            @endforeach
        </div>

        <!-- Input -->
        <form id="chat-form" class="mt-4 flex">
            <input type="text" id="message-input" class="flex-1 border rounded p-2 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Type a message..." />
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Send</button>
        </form>
    </div>


<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<!-- Laravel Echo IIFE version -->
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
    <script>
        function scrollChat() {
            let chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollChat();

        // Send message
        document.getElementById('chat-form').addEventListener('submit', function(e){
            e.preventDefault();
            let message = document.getElementById('message-input').value;
            if(message.trim() === '') return;

            axios.post('/messages', { message: message })
                .then(res => {
                    document.getElementById('message-input').value = '';
                    scrollChat();
                });
        });

        console.log("Pusher is not loaded!",window.Pusher);

    //       window.Echo = new Echo({
    //     broadcaster: 'pusher',
    //     key: 'cfa15b20fe5f23dfef57',   // <- yahan apna Pusher key daal
    //     cluster: 'ap22',  // <- cluster sahi daal
    //     forceTLS: true
    // });
    

        // Listen for new messages
        window.Echo.channel('chat')
            .listen('MessageSent', (e) => {
                let chatBox = document.getElementById('chat-box');
                let div = document.createElement('div');
                div.classList.add('mb-2');
                div.innerHTML = `<strong>${e.message.user_id == 0 ? 'Guest' : e.message.user.name}:</strong> ${e.message.message}`;
                chatBox.appendChild(div);
                scrollChat();
            });
    </script>
</body>
</html>
