
export default class Paginate{
    constructor(page, pages) {
        this.page = page;
        this.pages = pages;
        this.existingQuery = Object.fromEntries(new URLSearchParams(window.location.search).entries());
    }
    toTarget(selector){
        document.querySelector(selector).appendChild(this.render())

    }
    render(){
        const ele = document.createElement('div');
        for(let i = 1; i <= this.pages; i++) {
            ele.appendChild(this.pageButton(i))
        }
        return ele;
    }
    pageButton(pageNumber) {
        const a = document.createElement('a');
        this.existingQuery.page = pageNumber;
        a.href = window.location.pathname + this.getQuery()
        a.innerText = pageNumber
        if(pageNumber === this.page){}
        a.classList.add(pageNumber === this.page ? 'button-secondary-light' : 'button-secondary')

        return a;
    }
    getQuery() {
        let query = '?';
        Object.keys(this.existingQuery).forEach(key => {
            query += `${key}=${this.existingQuery[key]}&`;
        })
        return query;
    }

}