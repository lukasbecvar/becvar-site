// get references to HTML elements
const chatDiv = document.getElementById('chat');
const messagesDiv = document.getElementById('messages');
const messageInput = document.getElementById('message');
const sendButton = document.getElementById('send');

// initialize variables
let lastMessageId = 0; // keeps track of the last displayed message's ID
let userIsScrolling = false; // indicates whether the user is currently scrolling

// function to display a chat message in the chat window
function displayMessage(message, sender, role, pic, day, time) {
    // check if the sender is not the system
    if (sender !== 'You_184911818748818kgf') {
        // determine whether the user is at the bottom of the chat
        const isAtBottom = chatDiv.scrollTop + chatDiv.clientHeight >= chatDiv.scrollHeight - 5;

        // append the message to the messages container
        if (role == 'Owner' || role == 'Admin') {
            messagesDiv.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture"><span class="text-red">' + sender + '</span> (' + day + ' ' + time + ') <br>' + message + '</p>';
        } else if (role == 'User') {
            messagesDiv.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture"><span class="text-success">' + sender + '</span> (' + day + ' ' + time + ') <br>' + message + '</p>';
        } else {
            messagesDiv.innerHTML += '<p class="chat-message-box"><img src="data:image/jpeg;base64,' + pic + '" alt="profile_picture">' + sender + ' (' + day + ' ' + time + '):´ <br>' + message + '</p>';
        }

        // if the user is at the bottom of the chat, scroll to the new message
        if (isAtBottom) {
            scrollToBottom();
        }
    } 
}

// function to send a chat message to the server
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
            if (data.status === 'message saved') {
                // clear the message input after sending
                messageInput.value = '';
                // display the sent message in the chat
                displayMessage(message, 'You_184911818748818kgf', new Date().toLocaleString());
            } else {
                console.error('Failed to save message');
            }
        });
    }
}

// function to scroll to the bottom of the chat
function scrollToBottom() {
    chatDiv.scrollTop = chatDiv.scrollHeight;
}

// function to fetch and display chat messages from the server
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

// set an interval to periodically fetch chat messages (every 500 milliseconds)
setInterval(getChatMessages, 500);

// add a click event listener to the send button to send messages
sendButton.addEventListener('click', sendMessage);

// add a keypress event listener to send a message when the Enter key is pressed
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
