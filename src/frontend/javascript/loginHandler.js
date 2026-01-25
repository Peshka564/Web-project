const loginForm = document.getElementById('login-form');
const loginUsername = document.getElementById('login-username');
const loginPassword = document.getElementById('login-password');

loginUsername.addEventListener('input', () => {
    document.getElementById('login-username-error').textContent = '';
});

loginPassword.addEventListener('input', () => {
    document.getElementById('login-password-error').textContent = '';
});

loginForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = getFormData();
    const isValid = validate(data);

    if (!isValid) {
        return;
    }
    
    window.location.href= 'converter.html';
});

const validate = (data) => {
    document.getElementById('login-username-error').textContent = '';
    document.getElementById('login-password-error').textContent = '';
    
    let valid = true;

    if (data.username === '') {
        showError('login-username-error', 'Username is required!');
        valid = false;
    }

    if (data.password === '') {
        showError('login-password-error', 'Password is required!');
        valid = false;
    }

    return valid;
};

const getFormData = () => ({
    username: document.getElementById('login-username').value.trim(),
    password: document.getElementById('login-password').value.trim()
});

const showError = (id, message) => {
    const element = document.getElementById(id);
    element.textContent = message;
};