const _ = document.querySelector.bind(document);
const __ = document.querySelectorAll.bind(document);

// query
const queryParameter = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

// ping

let loggedIn = false;
async function heartbeat() {
    try{
        const alive = await fetch('/api/auth/me').then(j => j.json())
        loggedIn = true;

    } catch (e) {
        if(loggedIn){
            alert('Session expired')
            window.location.href = '/'
        }
    }

}
setInterval(heartbeat, 60 * 1000);


