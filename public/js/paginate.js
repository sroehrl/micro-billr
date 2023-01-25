
export default class Paginate{
    constructor(page, pages) {
        this.page = page;
        this.pages = pages;
        this.existingQuery = Object.fromEntries(new URLSearchParams(window.location.search).entries());
        this.filters = __('[data-paginate-filter]')
        this.processFilter()
        this.sortable = __('[data-paginate-sort]')
        this.processSortable()
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
    processFilter(){
        this.filters.forEach(filter => {
            // set
            if(this.existingQuery[filter.name]){
                filter.value = this.existingQuery[filter.name]
            }
            // change?
            filter.addEventListener('change', ev => {
                this.existingQuery[filter.name] = ev.target.value
                window.location.href = window.location.pathname + this.getQuery();
            })
        })
    }
    processSortable(){
        this.sortable.forEach(sortable => {
            const toggler = document.createElement('button');
            toggler.className = "ml-2 mr-1 bg-transparent border-transparent cursor-pointer";
            toggler.innerHTML = `<svg class="w-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"></path>
                                </svg>`;
            sortable.appendChild(toggler)
            toggler.addEventListener('click', ev => {
                if(this.existingQuery.sort && this.existingQuery.sort === sortable.dataset.paginateSort){
                    this.existingQuery.sort = '-' + sortable.dataset.paginateSort
                } else {
                    this.existingQuery.sort = sortable.dataset.paginateSort
                }
                window.location.href = window.location.pathname + this.getQuery();
            })
        })
    }

}