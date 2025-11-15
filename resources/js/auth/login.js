document.querySelectorAll('.animate-pulse').forEach((el, index) => {
    el.style.animationDelay = `${index * 500}ms`;
});

const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

if (togglePassword && password) {
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
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

    document.addEventListener('DOMContentLoaded', () => {
        gsap.from('.bg-gradient-to-br', { duration: 1, x: -100, opacity: 0, ease: "power2.out" }); 
        
        gsap.from('.login-form-content', { duration: 1, x: 100, opacity: 0, ease: "power2.out", delay: 0.3 }); 
    });
} else {
    console.warn("GSAP not loaded. Animations disabled.");
}