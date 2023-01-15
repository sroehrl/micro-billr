const selectBoxes = document.querySelectorAll('[data-selected]')
selectBoxes.forEach(selectBox => {
    console.log({selected:selectBox.dataset.selected})
    selectBox.value = selectBox.dataset.selected
})

const deleteButtons = document.querySelectorAll('[data-delete]')
deleteButtons.forEach(button => {
    button.addEventListener('click', deleteEntity)
})
function deleteEntity(ev){
    const url = ev.target.dataset.delete + '/' + ev.target.dataset.deleteId;
    fetch(url,{
        method: 'delete'
    }).then(() => window.location.reload())
}