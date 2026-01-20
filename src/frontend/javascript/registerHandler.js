const registerForm = document.getElementById('register-form');
const registerModal = document.getElementById('register-modal');

registerForm.addEventListener('submit', (event) => {
    event.preventDefault();
    //TODO password === conf password
    //TODO registration success
    registerModal.classList.remove('active');
});