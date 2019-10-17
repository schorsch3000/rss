let domReady = function (callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};
domReady(function () {
    let loadItem = function (element) {
        fetch(element.getAttribute('data-url')+'index.html')
            .then((response) => response.text())
            .then((html) => {
                element.outerHTML = html;
                element.classList.remove('unloaded')
                element.classList.add('loaded')
            })
    }
    sloth({
        on: document.getElementsByClassName('unloaded'),
        threshold: 500,
        callback: loadItem
    });
    let next = function (keep) {
        let currentEl = document.querySelector("#main>article[data-new='1']")
        currentEl.setAttribute('data-new', '0')
        document.querySelector("#main>article[data-new='1']").scrollIntoView(true)
        window.scrollBy(0, -15)
        let nextEl = document.getElementsByClassName('unloaded')
        if (nextEl.length) {
            loadItem(nextEl[0])
        }
        if (keep) return
        //report read
        let url=currentEl.getAttribute("data-url").replace(/^\/data/,'/markread')
        fetch(url)


    }
    let prev = function () {
    }
    document.onkeypress = function (evt) {
        evt = evt || window.event;
        if (evt.code === 'KeyJ') {
            next();
        }
        if (evt.code === 'KeyK') {
            next(true)
        }
    };
})


