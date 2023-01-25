// get data toggle-shows

// click
const toggleShows = document.querySelectorAll('[data-toggle-show]');
toggleShows.forEach(toggle => toggleOnEvent('click', toggle, 'toggleShow') )

// hover
const toggleHovers = document.querySelectorAll('[data-toggle-hover]');
toggleHovers.forEach(toggle => toggleOnEvent('mouseenter', toggle, 'toggleHover') )
toggleHovers.forEach(toggle => toggleOnEvent('mouseleave', toggle, 'toggleHover') )


function toggleOnEvent(actionEvent, element, target) {
    console.log({registered: element})
    element.addEventListener(actionEvent, ev => {
        document.querySelectorAll(element.dataset[target]).forEach(target => {
            if(target.classList.contains('none')){
                target.classList.remove('none')
            } else {
                target.classList.add('none')
            }

        })
    })
}

// class
__('[data-class]').forEach(element => {
    console.log(element.dataset.class)
    const classConditions = eval('(' +element.dataset.class+')')
    Object.keys(classConditions).forEach(className => {
        if(classConditions[className]){
            element.className += ' ' +className;
        }
    })
})
