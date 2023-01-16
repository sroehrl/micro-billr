export default class Autocomplete{
    constructor(searchElement, targetElement) {
        this.target = document.querySelector(targetElement)
        this.input = document.querySelector(searchElement)
        this.input.addEventListener('input', this.search())
        this.results = [];
        this.resultContainer = document.createElement('div')
        this.input.parentNode.append(this.resultContainer)
        this.whenSelected = ()=>{}
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
        this.whenSelected = () => fn.call();
    }
    render(){
        this.resultContainer.childNodes.forEach(child => this.resultContainer.removeChild(child))

        this.results.forEach(result => {
            const div = document.createElement('div');
            div.className = 'p-2 pt-1 pb-1 hover:text-white hover:bg-neutral-light border-l-1 mb-1 cursor-pointer';
            div.textContent = result[this.displayProperty];
            div.addEventListener('click', ()=> {
                this.target.value = result.id
                this.input.value = result[this.displayProperty]
                this.resultContainer.childNodes.forEach(child => this.resultContainer.removeChild(child))
                this.whenSelected()
            })
            this.resultContainer.append(div)
        })
    }

}