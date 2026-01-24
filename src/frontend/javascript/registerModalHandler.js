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

registerForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = getRegisterFormData();
    const isValid = validateRegister(data);

    if (!isValid) {
        return;
    }

    registerForm.reset();
    registerModal.close();
});

const validateRegister = (data) => {
    clearRegistryErrors();
    
    let valid = true;

    if (data.username === '') {
        showRegisterError('register-username-error', 'Username is required!');
        valid = false;
    }

    if (data.password === '') {
        showRegisterError('register-password-error', 'Password is required!');
        valid = false;
    }

    if (data.passwordConfirmation === '') {
        showRegisterError('register-password-confirmation-error',  'Please confirm your password!');
        valid = false;
    } else if (data.password !== data.passwordConfirmation) {
        showRegisterError('register-password-confirmation-error',  'Passwords do not match!');
        valid = false;
    }

    return valid;
};

const getRegisterFormData = () => ({
    username: document.getElementById('register-username').value.trim(),
    password: document.getElementById('register-password').value.trim(),
    passwordConfirmation: document.getElementById('register-password-confirmation').value.trim()
});

const showRegisterError = (id, message) => {
    const element = document.getElementById(id);
    element.textContent = message;
};

const clearRegistryErrors = () => {
    document.getElementById('register-username-error').textContent = '';
    document.getElementById('register-password-error').textContent = '';
    document.getElementById('register-password-confirmation-error').textContent = '';
};

const clearLoginErrors = () => {
    document.getElementById('login-username-error').textContent = '';
    document.getElementById('login-password-error').textContent = '';
};