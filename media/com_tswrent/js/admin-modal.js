document.getElementById('myModal').addEventListener('click', function(event) {
    const selectedItem = event.target.closest('[data-button-action="select-item"]');

    if (selectedItem) {
        const itemId = selectedItem.getAttribute('data-item-id');
        console.log('Gewähltes Item:', itemId);

        // Item-ID in das versteckte Feld im Hauptformular setzen
        document.getElementById('selectedItemId').value = itemId;

        // Modal schließen
        var modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
        modal.hide();
    }
});