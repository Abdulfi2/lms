<style>
    /* Tom Select tidak punya dark mode bawaan — selaraskan dengan tema Tailwind (.dark) project ini */
    .ts-wrapper.form-control,
    .ts-wrapper.form-select {
        padding: 0;
    }
    .ts-control {
        border-radius: 0.5rem;
        border-color: rgb(209 213 219);
        background-color: #fff;
    }
    .dark .ts-control {
        border-color: rgb(75 85 99);
        background-color: rgb(55 65 81);
        color: #fff;
    }
    .dark .ts-control input {
        color: #fff;
    }
    .dark .ts-control input::placeholder {
        color: rgb(156 163 175);
    }
    .ts-dropdown {
        border-color: rgb(209 213 219);
    }
    .dark .ts-dropdown {
        background-color: rgb(55 65 81);
        border-color: rgb(75 85 99);
        color: #fff;
    }
    .dark .ts-dropdown .option:hover,
    .dark .ts-dropdown .option.active {
        background-color: rgb(75 85 99);
    }
    .ts-control > .item {
        background-color: rgb(59 130 246 / 0.1);
        border-color: rgb(59 130 246 / 0.3);
        color: #3B82F6;
    }
    .dark .ts-control > .item {
        background-color: rgb(59 130 246 / 0.2);
    }
</style>
