document.addEventListener('DOMContentLoaded', function() {
    const userBtn = document.getElementById('userBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');
    
    //opens up the dropdown menu when clicking on the profile icon
    if (userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent click from bubbling to document
            dropdownMenu.classList.toggle('show');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (dropdownMenu && dropdownMenu.classList.contains('show')) {
            if (!e.target.closest('.user-menu')) {
                dropdownMenu.classList.remove('show');
            }
        }
    });
})
