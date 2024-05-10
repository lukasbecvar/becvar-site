/* becvar-site: admin chat function */ 
document.addEventListener("DOMContentLoaded", function() {
    // get html elements
    const chat = document.getElementById('chat');
    const messages = document.getElementById('messages');
    const messageInput = document.getElementById('message');
    const sendButton = document.getElementById('send');
    
    // init variables
    let lastMessageId = 0; // keeps track of the last displayed message's ID

    // linkify message content
    function linkifyText(text) {
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, function(url) {
            return '<a href="' + url + '" target="_blank">' + url + '</a>';
        });
    }

    // scroll to the bottom of the chat
    function scrollToBottom() { 
        chat.scrollTop = chat.scrollHeight;
    }

    // display a message in the chat
    function displayMessage(message, sender, role, pic, day, time) {
        // determine whether the user is at the bottom of the chat
        const isAtBottom = chat.scrollTop + chat.clientHeight >= chat.scrollHeight - 5;

        // wrap links in the message text with the <a> tag
        const linkifiedMessage = linkifyText(message);

        // add the message to the messages container
        if (role == 'Owner' || role == 'Admin') {
            messages.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture"><span class="text-red">' + sender + '</span> (' + day + ' ' + time + ') <br>' + linkifiedMessage + '</p>';
        } else if (role == 'User') {
            messages.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture"><span class="text-success">' + sender + '</span> (' + day + ' ' + time + ') <br>' + linkifiedMessage + '</p>';
        } else {
            messages.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture">' + sender + ' (' + day + ' ' + time + '):´ <br>' + linkifiedMessage + '</p>';
        }

        // if the user is at the bottom of the chat, scroll to the new message
        if (isAtBottom) {
            scrollToBottom();
        }
    }

    // send a chat message to the server
    function sendMessage() {
        const message = messageInput.value;
        if (message) {
            // send a POST request to save the message
            fetch('/api/chat/save/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // clear the message input after sending
                    messageInput.value = '';
                } else {
                    console.error('Failed to save message');
                }
            });
        }
    }

    // fetch and display chat messages from the server
    function getChatMessages() {
        // fetch chat messages from the server
        fetch('/api/chat/get/messages')
        .then(response => response.json())
        .then(data => {
            data.forEach(message => {
                // display the message if it has a greater ID than the last displayed message
                if (message.id > lastMessageId) {
                    displayMessage(message.message, message.sender, message.role, message.pic, message.day, message.time);
                    // update the last displayed message's ID
                    lastMessageId = message.id;
                }
            });
        });
    }

    // initial fetch and display of chat messages
    getChatMessages();

    // set an interval to periodically fetch chat messages (every 1000 milliseconds)
    setInterval(getChatMessages, 500);

    // add a click event listener to the send button to send messages
    sendButton.addEventListener('click', sendMessage);

    // add a keypress event listener to send a message when the Enter key is pressed
    messageInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            sendMessage();
            messageInput.value = '';
        }
    });
});
