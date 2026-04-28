// console.log("JS is running");


// for the alert thingy, fixed 7:02pm i pasted this from botton to top!!!!
const urlmode = new URLSearchParams(window.location.search);
if (urlmode.has('success')) {
    alert("Song added successfully. :D");
    window.history.replaceState({}, document.title, window.location.pathname);
}
// deleted
if (urlmode.has('deleted')) {
    alert("Deleted successfully. D:");
    window.history.replaceState({}, document.title, window.location.pathname);
}
if (urlmode.has('updated')) {
    alert("Updated successfully. :D");
    window.history.replaceState({}, document.title, window.location.pathname);
}

// fields
function toggleFields() {
    const mode = document.getElementById('mode').value;

    console.log('Mode selected:', mode); // ERRORS ARE SPITTING OUT SA F12 CONSOLE IF YOU DID SOMETHING WRONG, BLAME HER!!!!

    document.getElementById('newAlbumFields').style.display = mode === 'new' ? 'block' : 'none';
    document.getElementById('existingAlbumFields').style.display = mode === 'existing' ? 'block' : 'none';
    document.getElementById('updateAlbumFields').style.display = mode === 'update' ? 'block' : 'none'; // okay added
    document.getElementById('deleteFields').style.display = mode === 'delete' ? 'block' : 'none';
    document.getElementById('deleteAlbumFields').style.display = mode === 'del_album' ? 'block' : 'none';

    // Define visibility for all sections
    var sections = {
        'newAlbumFields': (mode === 'new'),
        'existingAlbumFields': (mode === 'existing'),
        'updateAlbumFields': (mode === 'update'), // okay added 6:17pm
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

    // del button
    document.getElementById('deleteButton').style.display = (mode === 'delete') ? 'block' : 'none';
    document.getElementById('deleteAlbumButton').style.display = (mode === 'del_album') ? 'block' : 'none';
}
toggleFields();

document.getElementById('mode').addEventListener('change', toggleFields);

