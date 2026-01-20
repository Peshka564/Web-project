const saveModal = document.getElementById('save-modal');
const saveOutputButton = document.getElementById('save-output-button');
const closeSave = document.getElementById('close-save');

saveOutputButton.addEventListener('click', (event) => {
    event.preventDefault();
    saveModal.classList.add('active');
});

closeSave.addEventListener('click', () => {
    saveModal.classList.remove('active');
});
saveModal.addEventListener('click', (e) => {
    if (e.target === saveModal) {
        saveModal.classList.remove('active');
    }
});