document.querySelectorAll('.toggle-ua').forEach(link => {
    link.addEventListener('click', function (event) {
        event.preventDefault();
        
        const loginContent = document.querySelector('.login-content');
        const registerContent = document.querySelector('.register-content');

        loginContent.classList.toggle('d-none');
        registerContent.classList.toggle('d-none');
    });
});