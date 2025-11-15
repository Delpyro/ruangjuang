import AOS from 'aos';
import 'aos/dist/aos.css';

AOS.init({
    duration: 800,
    once: true,
    offset: 50,
});

document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const navLinks = document.querySelectorAll('.nav-link, button.w-full');
    
    // Profile dropdown elements
    const profileButton = document.getElementById('profile-button');
    const dropdownMenu = document.getElementById('dropdown-menu');

    // Mobile menu functionality
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('open');
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('open');
                mobileMenu.classList.add('hidden');
            }
        });

        // Close mobile menu when clicking on links
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('open');
                mobileMenu.classList.add('hidden');
            });
        });
    }

    // Profile dropdown functionality
    if (profileButton && dropdownMenu) {
        profileButton.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdownMenu.classList.add('hidden');
        });

        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // FAQ functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const icon = this.querySelector('i');
            
            // Toggle current FAQ
            if (answer.style.maxHeight && answer.style.maxHeight !== '0px') {
                answer.style.maxHeight = '0';
                answer.style.marginTop = '0';
                icon.classList.replace('fa-minus', 'fa-plus');
            } else {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.style.marginTop = '1rem';
                icon.classList.replace('fa-plus', 'fa-minus');
            }
            
            // Close other FAQs
            faqQuestions.forEach(otherQuestion => {
                if (otherQuestion !== question) {
                    const otherAnswer = otherQuestion.nextElementSibling;
                    const otherIcon = otherQuestion.querySelector('i');
                    otherAnswer.style.maxHeight = '0';
                    otherAnswer.style.marginTop = '0';
                    otherIcon.classList.replace('fa-minus', 'fa-plus');
                }
            });
        });
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});