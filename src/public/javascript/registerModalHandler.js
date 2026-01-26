const registerModal = document.getElementById('register-modal');
const registerLink = document.getElementById('register-link');
const closeRegister = document.getElementById('close-register');
const registerForm = document.getElementById('register-form');

const registrationUsername = document.getElementById('register-username');
const registrationPassword = document.getElementById('register-password');
const registrationPasswordConfirmation = document.getElementById('register-password-confirmation');

registrationUsername.addEventListener('input', () => {
    document.getElementById('register-username-error').textContent = '';
});

registrationPassword.addEventListener('input', () => {
    document.getElementById('register-password-error').textContent = '';
});

registrationPasswordConfirmation.addEventListener('input', () => {
    document.getElementById('register-password-confirmation-error').textContent = '';
});

registerLink.addEventListener('click', (event) => {
    event.preventDefault();
    registerModal.showModal();
    clearLoginErrors();
});

closeRegister.addEventListener('click', () => {
    clearRegistryErrors();
    registerModal.close();
});

registerModal.addEventListener('click', (event) => {
    if (event.target === registerModal) {
        clearRegistryErrors();
        registerModal.close();
    }
});

const clearRegistryErrors = () => {
    document.getElementById('register-username-error').textContent = '';
    document.getElementById('register-password-error').textContent = '';
    document.getElementById('register-password-confirmation-error').textContent = '';
};

const clearLoginErrors = () => {
    document.getElementById('login-username-error').textContent = '';
    document.getElementById('login-password-error').textContent = '';
};