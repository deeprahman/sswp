const protectUpdateDirectoryCheckbox = document.getElementById('protect-update-directory');
const updateDirectoryOptions = document.getElementById('update-directory-options');

protectUpdateDirectoryCheckbox.addEventListener('change', () => {
    updateDirectoryOptions.style.display = protectUpdateDirectoryCheckbox.checked ? 'block' : 'none';
});