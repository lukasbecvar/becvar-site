function sendOnlineStatus() {
    fetch('/api/visitor/update/activity', {
        method: 'POST'
    });
}

// send online status after 3 min
setInterval(sendOnlineStatus, 30000);

// send online status on page init
sendOnlineStatus();