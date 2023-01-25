export default class Autocomplete{
    constructor(searchElement, targetElement) {
        this.target = document.querySelector(targetElement)
        this.input = document.querySelector(searchElement)
        this.input.addEventListener('input', this.search())
        this.results = [];
        this.resultContainer = document.createElement('div')
        this.resultContainer.className = 'absolute bg-white left-0 right-0'
        this.input.parentNode.append(this.resultContainer)
        this.whenSelected = ()=>{ this.resultContainer.innerHTML = '' }
        this.input.addEventListener('keydown', ev => {
            if (ev.keyCode === 13){
                ev.preventDefault()
                if(this.results.length > 0){
                    this.select(this.results[0])
                }
            }

        })
    }
    setEndpoint(url){
        this.endpoint = url;
        return this;
    }
    setDisplayProperty(prop){
        this.displayProperty = prop;
        return this;
    }
    search(){
        let timer;
        return () => {
            clearTimeout(timer)
            timer = setTimeout(async()=>{
                const stream = await fetch(this.endpoint + this.input.value)
                this.results = await stream.json()
                this.render()
            }, 350)
        }
    }
    onSelect(fn){
        this.whenSelected = () => {
            this.resultContainer.innerHTML = ''
            fn.call()
        };
    }
    render(){
        this.resultContainer.childNodes.forEach(child => this.resultContainer.removeChild(child))

        this.results.forEach((result,i) => {
            const div = document.createElement('div');
            div.className = 'p-2 pt-1 pb-1 hover:text-white hover:bg-neutral-light border-l-1 border-b-1 mb-1 cursor-pointer';
            if(i === 0){
                div.classList.add('text-underline')
            }
            div.textContent = result[this.displayProperty];
            div.addEventListener('click', ()=> this.select(result))
            this.resultContainer.append(div)
        })
    }
    select(result){
        this.target.value = result.id
        this.input.value = result[this.displayProperty]
        this.resultContainer.childNodes.forEach(child => this.resultContainer.removeChild(child))
        this.whenSelected()
    }

}