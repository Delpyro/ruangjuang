document.querySelectorAll('.animate-pulse').forEach((el, index) => {
    el.style.animationDelay = `${index * 500}ms`;
});


const togglePassword = document.querySelector('#togglePassword');
const passwordInput = document.querySelector('#password');

if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
}

const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
const passwordConfirmationInput = document.querySelector('#password_confirmation');

if (togglePasswordConfirmation && passwordConfirmationInput) {
    togglePasswordConfirmation.addEventListener('click', function () {
        const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
}

if (typeof gsap !== 'undefined') {
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', () => {
            gsap.to(input, { boxShadow: '0 0 0 3px rgba(88, 167, 33, 0.2)', duration: 0.3 });
        });
        
        input.addEventListener('blur', () => {
            gsap.to(input, { boxShadow: 'none', duration: 0.3 });
        });
    });
}

if (typeof gsap !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        gsap.from('.bg-gradient-to-br', { duration: 1, x: -100, opacity: 0, ease: "power2.out" });
        
        // Selektor GSAP yang aman
        gsap.from('.login-form-content', { duration: 1, x: 100, opacity: 0, ease: "power2.out", delay: 0.3 });
    });
}