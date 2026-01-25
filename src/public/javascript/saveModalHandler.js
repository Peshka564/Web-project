const saveModal = document.getElementById('save-modal');
const saveOutputButton = document.getElementById('save-output-button');
const closeSave = document.getElementById('close-save');
const saveForm = document.getElementById('save-form');
const saveTitle = document.getElementById('save-title');

saveTitle.addEventListener('input', () => {
    document.getElementById('save-title-error').textContent = '';
});

saveOutputButton.addEventListener('click', (event) => {
    event.preventDefault();
    saveModal.showModal();
});

closeSave.addEventListener('click', () => {
    saveModal.close();
});

saveModal.addEventListener('click', (event) => {
    if (event.target === saveModal) {
        saveModal.close();
    }
});

saveForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = getFormData();
    const isValid = validate(data);

    if (!isValid) {
        return;
    }
    
    saveForm.reset();
    saveModal.close();
});

const validate = (data) => {
    document.getElementById('save-title-error').textContent = '';
    
    let valid = true;

    if (data.title === '') {
        showError('save-title-error', 'Title is required!');
        valid = false;
    }

    return valid;
};

const getFormData = () => ({
    title: document.getElementById('save-title').value.trim()
});

const showError = (id, message) => {
    const element = document.getElementById(id);
    element.textContent = message;
};