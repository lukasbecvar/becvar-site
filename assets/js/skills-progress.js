/* becvar-site: skills bars smooth progress load */ 
document.addEventListener('DOMContentLoaded', function() {
    // select all progress-bar elements
    var progressBars = document.querySelectorAll('.progress-bar')

    // iterate through each progress bar and animate it
    progressBars.forEach(function (progressBar) {
        var value = progressBar.getAttribute('aria-valuenow')  
        animateProgressBar(progressBar, value, 1000)
    })

    // animate the progress bar
    function animateProgressBar(progressBar, value, duration) {
        var startTime
        var startWidth = 0
        var targetWidth = value

        // step function to update the progress bar width over time
        function step(timestamp) {
            // initialize the start time if not already set
            if (!startTime) startTime = timestamp
            
            // calculate the progress of the animation
            var progress = timestamp - startTime
            
            // calculate the percentage of completion based on the animation duration
            var percentage = Math.min(progress / duration, 1)
            
            // calculate the current width of the progress bar based on the percentage
            var currentWidth = startWidth + percentage * (targetWidth - startWidth)
            
            // set the width of the progress bar element
            progressBar.style.width = currentWidth + '%'

            // continue the animation if not yet complete
            if (progress < duration) {
                window.requestAnimationFrame(step)
            }
        }

        // start the animation by calling the step function
        window.requestAnimationFrame(step)
    }
})
