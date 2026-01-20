const registerModal = document.getElementById('register-modal');
const registerLink = document.getElementById('register-link');
const closeRegister = document.getElementById('close-register');

registerLink.addEventListener('click', (event) => {
    event.preventDefault();
    registerModal.classList.add('active');
});

closeRegister.addEventListener('click', () => {
    registerModal.classList.remove('active');
});
registerModal.addEventListener('click', (e) => {
    if (e.target === registerModal) {
        registerModal.classList.remove('active');
    }
});