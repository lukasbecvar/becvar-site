function sendOnlineStatus() {
    fetch('/api/user/activity/iwvtqakxzkldtemmicanf', {
        method: 'POST'
    });
}

// send online status after 1 min
setInterval(sendOnlineStatus, 10000);

// send online status on page init
sendOnlineStatus();