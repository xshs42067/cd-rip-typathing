function toggleFields() {
    var mode = document.getElementById('mode').value;

    // Define visibility for all sections
    var sections = {
        'newAlbumFields': (mode === 'new'),
        'existingAlbumFields': (mode === 'existing'),
        'deleteFields': (mode === 'delete'),
        'deleteAlbumFields': (mode === 'del_album'),
        'songFields': (mode === 'new' || mode === 'existing')
    };

    // Loop through every section to toggle display and disabled state
    for (var id in sections) {
        var container = document.getElementById(id);
        var isActive = sections[id];

        // 1. Show or Hide the div
        container.style.display = isActive ? 'block' : 'none';

        // 2. THE FIX: Disable inputs inside hidden divs
        // This stops the browser from checking "required" on hidden fields
        var inputs = container.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.disabled = !isActive;
        });
    }

    // Toggle the Delete Buttons
    document.getElementById('deleteButton').style.display = (mode === 'delete') ? 'block' : 'none';
    document.getElementById('deleteAlbumButton').style.display = (mode === 'del_album') ? 'block' : 'none';
}

// Initial run to set the page correctly on load
toggleFields();