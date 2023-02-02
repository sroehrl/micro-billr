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
    const url = ev.target.dataset.delete + '/' + ev.target.dataset.deleteId;
    fetch(url,{
        method: 'delete'
    }).then(() => window.location.reload())
}