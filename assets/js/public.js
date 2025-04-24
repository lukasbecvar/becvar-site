/* public page function */ 
const select = (el, all = false) => {
    el = el.trim()
    if (all) {
        return [...document.querySelectorAll(el)]
    } else {
        return document.querySelector(el)
    }
}

const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
        if (all) {
            selectEl.forEach(e => e.addEventListener(type, listener))
        } else {
            selectEl.addEventListener(type, listener)
        }
    }
}

const scrollto = () => {
    window.scrollTo({top: 0, behavior: 'smooth'})
}

on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
})

let skilsContent = select('.skills-content')
if (skilsContent) {
    new Waypoint({
        element: skilsContent,
        offset: '80%',
        handler: function() {
            let progress = select('.progress .progress-bar', true)
            progress.forEach((el) => {
                el.style.width = el.getAttribute('aria-valuenow') + '%'
            })
        }
    })
}

// home page text animation
document.addEventListener("DOMContentLoaded", () => {
    const textElement = document.getElementById("typed-text")
    const cursor = document.getElementById("cursor")
    const fullText = textElement.dataset.text
    const texts = fullText.split("|").map(t => t.trim())

    cursor.style.display = "inline-block"
    let currentTextIndex = 0
    let charIndex = 0
    let typing = true

    const typingSpeedInitial = 200
    let typingSpeed = typingSpeedInitial
    const pauseAfterDeleting = 500
    const pauseAfterTyping = 1000
    const acceleration = 10
    const minSpeed = 30

    function typeLoop() {
        const currentText = texts[currentTextIndex]
        if (typing) {
            if (charIndex < currentText.length) {
                textElement.textContent += currentText.charAt(charIndex)
                charIndex++
                typingSpeed = Math.max(minSpeed, typingSpeed - acceleration)
                setTimeout(typeLoop, typingSpeed)
            } else {
                typing = false
                setTimeout(typeLoop, pauseAfterTyping)
            }
        } else {
            if (charIndex > 0) {
                textElement.textContent = currentText.slice(0, charIndex - 1)
                charIndex--
                setTimeout(typeLoop, 50)
            } else {
                typing = true
                typingSpeed = typingSpeedInitial
                currentTextIndex = (currentTextIndex + 1) % texts.length
                setTimeout(typeLoop, pauseAfterDeleting)
            }
        }
    }
    typeLoop()
})
