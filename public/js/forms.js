const selectBoxes = document.querySelectorAll('[data-selected]')
selectBoxes.forEach(selectBox => {
    selectBox.value = selectBox.dataset.selected
})

const checkBoxes = document.querySelectorAll('[data-checked]')
checkBoxes.forEach( checkbox => {
    checkbox.checked = checkbox.dataset.checked;
})

// js-href
document.querySelectorAll('[data-href]').forEach(ele => {
    ele.href = ele.dataset.href;
})

const deleteButtons = document.querySelectorAll('[data-delete]')
deleteButtons.forEach(button => {
    button.addEventListener('click', deleteEntity)
})
function deleteEntity(ev){
    ev.preventDefault();

    const parent = ev.target.parentNode.nodeName !== 'BUTTON' ? ev.target.parentNode : ev.target.parentNode.parentNode;


    const confirmation = document.createElement('div');
    confirmation.className = "absolute z-4 w-16 -top-1 -left-5 flex gap-x-2 p-1 bg-white shadow-neutral-light border-rounded";

    const delButton = document.createElement('button');
    delButton.className = "button-danger font-md";
    delButton.innerText = 'delete'
    delButton.addEventListener('click', innerEvent => {
        ev.preventDefault();
        fetch(ev.target.dataset.delete + '/' + ev.target.dataset.deleteId,{
            method: 'delete'
        }).then(j => j.json()).then(() => setTimeout(()=>window.location.reload(),100))
    })
    confirmation.appendChild(delButton);

    const noButton = document.createElement('button')
    noButton.className = "button-primary font-md";
    noButton.innerText = "keep it!"
    noButton.addEventListener('click', innerEvent => {
        ev.preventDefault();
        parent.removeChild(confirmation);
    })
    confirmation.appendChild(noButton)
    setTimeout(() => {
        parent.appendChild(confirmation)
    }, 150)


    return;


}

function deleteCall(url){
    console.log({url})
    return;

}