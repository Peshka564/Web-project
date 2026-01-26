const loginForm = document.getElementById('login-form');
const loginUsername = document.getElementById('login-username');
const loginPassword = document.getElementById('login-password');

loginUsername.addEventListener('input', () => {
    document.getElementById('login-username-error').textContent = '';
});

loginPassword.addEventListener('input', () => {
    document.getElementById('login-password-error').textContent = '';
});