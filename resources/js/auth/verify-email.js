document.querySelectorAll('.animate-pulse').forEach((el, index) => {
    el.style.animationDelay = `${index * 500}ms`;
});

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
        // Animasi div kiri (background gradient)
        gsap.from('.bg-gradient-to-br', { duration: 1, x: -100, opacity: 0, ease: "power2.out" });
        
        // PERBAIKAN: Mengganti selektor yang bermasalah (.bg-white) dengan class kustom
        gsap.from('.login-form-content', { duration: 1, x: 100, opacity: 0, ease: "power2.out", delay: 0.3 });
    });
}