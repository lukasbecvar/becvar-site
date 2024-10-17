/* front-end visitor status update */ 
document.addEventListener('DOMContentLoaded', function() {
    function sendOnlineStatus() {
        fetch('/api/visitor/update/activity', {
            method: 'POST'
        })
    }

    // send online status after 2 minutes
    setInterval(sendOnlineStatus, 120000)

    // send online status on page load
    sendOnlineStatus()
})
