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

        container.style.display = isActive ? 'block' : 'none';

        var inputs = container.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.disabled = !isActive;
        });
    }

    // Toggle the Delete Buttons
    document.getElementById('deleteButton').style.display = (mode === 'delete') ? 'block' : 'none';
    document.getElementById('deleteAlbumButton').style.display = (mode === 'del_album') ? 'block' : 'none';
}
toggleFields();

// for the alert thingy
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('success')) {
    alert("Song added successfully.");
    window.history.replaceState({}, document.title, window.location.pathname);
}
// deleted
if (urlParams.has('deleted')) {
    alert("Deleted successfully. :3");
    window.history.replaceState({}, document.title, window.location.pathname);
}