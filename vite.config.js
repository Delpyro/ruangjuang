import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/homepage.css',

                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/homepage.js',

                'resources/js/auth/login.js',
                'resources/js/auth/register.js',
                'resources/js/auth/new-password.js',
                'resources/js/auth/forgot-password.js',
                'resources/js/auth/verify-email.js',
                'resources/js/auth/verify-otp.js',
                'resources/js/auth/confirm-password.js',
            ],
            refresh: true,
        }),
    ],
});
