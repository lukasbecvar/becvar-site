/* front-end visitor status update */ 
document.addEventListener('DOMContentLoaded', function() {
    function sendOnlineStatus() {
        fetch('/api/visitor/update/activity', {
            method: 'POST'
        })
    }

    // send online status after 5 minutes
    setInterval(sendOnlineStatus, 300000)

    // send online status on page load
    sendOnlineStatus()
})
