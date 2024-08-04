document.addEventListener("DOMContentLoaded", function () {

    let toggle_password = function (event) {
        event.preventDefault();
        let clicked = this;
        let pass_input = document.getElementById(clicked.dataset.target);
        let icon = this.firstElementChild;
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        if (pass_input.type === 'password') {
            pass_input.type = 'text';
        } else {
            pass_input.type = 'password';
        }
    }

    const toggle = document.querySelectorAll(".view-password");

    for (let i = 0; i < toggle.length; i++) {
        toggle[i].removeEventListener('click', toggle_password, false);
        toggle[i].addEventListener('click', toggle_password, false);
    }
});