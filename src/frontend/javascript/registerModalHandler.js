const registerModal = document.getElementById('register-modal');
const registerLink = document.getElementById('register-link');
const closeRegister = document.getElementById('close-register');

registerLink.addEventListener('click', (event) => {
    event.preventDefault();
    registerModal.showModal();
});

closeRegister.addEventListener('click', () => {
    registerModal.close();
});
registerModal.addEventListener('click', (e) => {
    if (e.target === registerModal) {
        registerModal.close();
    }
});