
export default class Toast{
    constructor(content) {
        this.content = content;
        this.backGround = 'bg-white-opaque'
    }
    background(className) {
        this.backGround = className;
    }
    show(seconds = 4) {
        if(this.content.length<1){
            return
        }

        const toastElement = document.createElement('div');
        toastElement.className = "absolute border-rounded border-1 top-2 right-2 blur-4";
        toastElement.classList.add(this.backGround)
        toastElement.innerHTML = `
            <div class="grid bg-neutral text-white p-1">
                <div class="place-end-end cursor-pointer" id="close-toast">
                        <svg class="w-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                </div>
            </div>
            <div class=" p-2" style="min-width: 140px">${this.content}</div>
        `;
        document.body.append(toastElement);
        const timer = setTimeout(() => {
            document.body.removeChild(toastElement)
        }, seconds * 1000)
        document.getElementById('close-toast').addEventListener('click', ev => {
            document.body.removeChild(toastElement)
            clearTimeout(timer)
        })

    }
}