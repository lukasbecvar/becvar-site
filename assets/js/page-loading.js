/* page loading component */
document.addEventListener("DOMContentLoaded", function () {
    // hide loading component after page load
    document.getElementById("loader-wrapper").style.display = "none"
})

/* loading component for click on links */
document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loader-wrapper")
    document.body.addEventListener("click", function (event) {
        const target = event.target.closest("a")
        if (target && target.href) {
            event.preventDefault()
            loader.style.display = "flex"
            setTimeout(() => {
                window.location.href = target.href
            }, 10)
        }
    })
})

/* handle back/forward navigation */
window.addEventListener("pageshow", function (event) {
    if (event.persisted) { // checks if page was loaded from cache
        const loader = document.getElementById("loader-wrapper")
        loader.style.display = "none" // hide loader when page is shown
    }
})
