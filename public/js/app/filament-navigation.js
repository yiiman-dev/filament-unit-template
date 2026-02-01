document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar')?.collapseGroup();
});
