const _ = document.querySelector.bind(document);
const __ = document.querySelectorAll.bind(document);

// query
const queryParameter = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

