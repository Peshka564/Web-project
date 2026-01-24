const saveModal = document.getElementById('save-modal');
const saveOutputButton = document.getElementById('save-output-button');
const closeSave = document.getElementById('close-save');

saveOutputButton.addEventListener('click', (event) => {
    event.preventDefault();
    saveModal.showModal();
});

closeSave.addEventListener('click', () => {
    saveModal.close();
});
saveModal.addEventListener('click', (e) => {
    if (e.target === saveModal) {
        saveModal.close();
    }
});