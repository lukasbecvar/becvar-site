/* becvar-site: public page function */ 
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
    window.scrollTo({
    top: 0,
    behavior: 'smooth'
    })
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
