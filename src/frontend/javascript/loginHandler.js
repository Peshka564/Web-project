const loginForm = document.getElementById('login-form');

loginForm.addEventListener('submit', (event) => {
    event.preventDefault();
    //TODO validation
    window.location.href= 'converter.html';
});