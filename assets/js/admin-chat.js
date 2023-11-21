// get references to HTML elements
const chat = document.getElementById('chat');
const messages = document.getElementById('messages');
const message_input = document.getElementById('message');
const send_button = document.getElementById('send');
 
// initialize variables
let lastMessageId = 0; // keeps track of the last displayed message's ID
let userIsScrolling = false; // indicates whether the user is currently scrolling

// function to linkify message content
function linkifyText(text) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '" target="_blank">' + url + '</a>';
    });
}

// function to display a message in the chat with linkify for links
function displayMessage(message, sender, role, pic, day, time) {
    // check if the sender is not the system
    if (sender !== 'You_184911818748818kgf') {
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
}

// function to send a chat message to the server
function sendMessage() {
    const message = message_input.value;
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
                message_input.value = '';
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
    chat.scrollTop = chat.scrollHeight;
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

// set an interval to periodically fetch chat messages (every 100 milliseconds)
setInterval(getChatMessages, 100);

// add a click event listener to the send button to send messages
send_button.addEventListener('click', sendMessage);

// add a keypress event listener to send a message when the Enter key is pressed
message_input.addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
        sendMessage();
        message_input.value = '';
    }
});

// set command input to lower case
document.getElementById('command').addEventListener('input', function () {
    this.value = this.value.toLowerCase();
});
