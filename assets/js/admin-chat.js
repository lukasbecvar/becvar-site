// get references to HTML elements
const chatDiv = document.getElementById('chat');
const messagesDiv = document.getElementById('messages');
const messageInput = document.getElementById('message');
const sendButton = document.getElementById('send');

// initialize variables
let lastMessageId = 0; // keeps track of the last displayed message's ID
let userIsScrolling = false; // indicates whether the user is currently scrolling

// function to display a chat message in the chat window
function displayMessage(message, sender, day, time) {
    // check if the sender is not the system
    if (sender !== 'You_184911818748818kgf') {
        // append the message to the messages container
        messagesDiv.innerHTML += '<p>' + sender + ' (' + day + ' ' + time + '): ' + message + '</p>';
    }
    // if the user is not scrolling, automatically scroll to the bottom of the chat
    if (!userIsScrolling) {
        chatDiv.scrollTop = chatDiv.scrollHeight;
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

// dunction to fetch and display chat messages from the server
function getChatMessages() {
    // detch chat messages from the server
    fetch('/api/chat/get/messages')
    .then(response => response.json())
    .then(data => {
        data.forEach(message => {
            // display the message if it has a greater ID than the last displayed message
            if (message.id > lastMessageId) {
                displayMessage(message.message, message.sender, message.day, message.time);
                // update the last displayed message's ID
                lastMessageId = message.id;
            }
        });
    });
}

// unitial fetch and display of chat messages
getChatMessages();

// set an interval to periodically fetch chat messages (every 500 milliseconds)
setInterval(getChatMessages, 500);

// add a scroll event listener to detect user scrolling
chatDiv.addEventListener('scroll', () => {
    // update the scrolling status
    userIsScrolling = chatDiv.scrollTop < chatDiv.scrollHeight - chatDiv.clientHeight;
    // if the user scrolls to the top, fetch more older messages
    if (chatDiv.scrollTop === 0) {
        getMoreMessages();
    }
});

// function to fetch and display more older messages
function getMoreMessages() {
    // fetch older chat messages from the server
    fetch('/api/chat/get/messages?load_older=true')
    .then(response => response.json())
    .then(data => {
        data.forEach(message => {
            // display the older message if it has a smaller ID than the last displayed message
            if (message.id < lastMessageId) {
                displayMessage(message.message, message.sender, message.id, message.day, message.time);
                // update the last displayed message's ID
                lastMessageId = message.id;
            }
        });
    });
}

// add a click event listener to the send button to send messages
sendButton.addEventListener('click', sendMessage);

// add a keypress event listener to send a message when the Enter key is pressed
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
