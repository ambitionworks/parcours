const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: ['./storage/framework/views/*.php', './resources/views/**/*.blade.php'],

    theme: {
        extend: {
        },
    },

    variants: {
        opacity: ['responsive', 'hover', 'focus', 'disabled'],
        visibility: ['responsive', 'group-hover'],
    },

    plugins: [require('@tailwindcss/ui')],
};
