
document.getElementById('Category').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const optgroup = selectedOption.parentNode; // Get the parent <optgroup>
    const groupValue = optgroup.getAttribute('data-group'); // Get the data-group value

    // Set the hidden input's value
    document.getElementById('mainCategory').value = groupValue;
});

