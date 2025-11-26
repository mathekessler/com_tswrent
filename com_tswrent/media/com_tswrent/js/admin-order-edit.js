window.addEventListener('DOMContentLoaded', () => {
    // Wird ausgeführt, wenn DOM fertig geladen ist
});

async function c_contact() {    
    const customer_value = document.getElementById("jform_customer_id_id").value;
    const url = `index.php?option=com_tswrent&task=order.getcustomercontact&id=${customer_value}&format=json`;
    
    if (!customer_value || customer_value === "0") {
        console.warn("No customer selected.");
        optionsC_contact({ data: [] });
        return;
    }

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) {
            console.error('HTTP error:', response.status, response.statusText);
            return;
        }

        const json = await response.json();
        optionsC_contact(json);

    } catch (error) {
        console.error('Fetch C_Contact error:', error);
    }
}


async function getFactor() {

    const graduation = document.getElementById("jform_graduation").value;
    let days = document.getElementById("jform_days").value;
 
    if (isNaN(days) || days <= 1) {
        days = 1;
    }

    const url = `index.php?option=com_tswrent&task=order.getGraduationFactor&id=${graduation}&days=${days}&format=json`;
    

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
 
        if (!response.ok) {
            console.error('HTTP error:', response.status, response.statusText);
            return;
        }

        const json = await response.json();
        // Beispiel: Erwartete Antwort { "data": 1.5 }
        document.getElementById("jform_factor").value = json.data ?? '1';

    } catch (error) {
        console.error('Fetch error:', error);
    }
}


function optionsC_contact(responseData) {
    
    deleteOptions();

    const select = document.getElementById("jform_c_contact_id"); // ✅ Must come first
    const elmts = responseData.data;


    // Check if we have data to fill
    if (!Array.isArray(elmts) || elmts.length === 0) {
        return; // No further contacts to add
    }

    // Append actual contact options
    elmts.forEach(item => {
        const el = document.createElement("option");
        el.textContent = item.text;
        el.value = item.value;
        select.appendChild(el);
    });
}

function deleteOptions() {
    const selectobject = document.getElementById("jform_c_contact_id");

    // Defensive check in case element is missing
    if (!selectobject) {
        console.warn("Could not find jform_c_contact_id select element.");
        return;
    }

    // Remove all existing options
    while (selectobject.length > 0) {
        selectobject.remove(0);
    }
}

function calcDays() {
    const startDate = new Date(document.getElementById("jform_startdate").value);
    const endDate = new Date(document.getElementById("jform_enddate").value);
    const oneDay = 24 * 60 * 60 * 1000; // Number of milliseconds in a day
    const start = new Date(startDate);
    const end = new Date(endDate);
  
    // Calculate the time difference in milliseconds
    const timeDiff = Math.abs(end - start);
  
    // Calculate the number of full days
    const fullDays = Math.floor(timeDiff / oneDay)+1;
    document.getElementById("jform_days").value = fullDays;
    calcHours();
    getFactor();
}

function calcHours() {
    const startDate = new Date(document.getElementById("jform_startdate").value);
    const endDate = new Date(document.getElementById("jform_enddate").value);
    const millisecondsPerHour = 60 * 60 * 1000;
    const hoursBetween = (endDate - startDate) / millisecondsPerHour;
    const hours=Math.floor(hoursBetween)
    const element = document.getElementById("jform_hours");
    document.getElementById("jform_hours").value = hours;
}

/**
 * Berechnet den Gesamtpreis für eine einzelne Produktzeile.
 * @param {HTMLElement} element Das geänderte Input-Element (Menge oder Rabatt).
 */
function calcPrice(element) {
    if (!element) {
        console.error('calcPrice was called without an element.');
        return;
    }

    const row = element.closest('tr');
    if (!row) return;

    // Werte aus der Zeile und dem Hauptformular auslesen
    const quantityInput = row.querySelector('input[name*="[reserved_quantity]"]');
    const pricePerPiece = parseFloat(row.querySelector('input[name*="[product_price]"]').value.replace(',', '.')) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    const discount = parseFloat(row.querySelector('input[name*="[productdiscount]"]').value) || 0;
    const factor = parseFloat(document.getElementById('jform_factor').value) || 1;

    // Verfügbaren Lagerbestand prüfen
    const totalField = row.querySelector('input[name*="[product_price_total]"]');
    const availableStock = parseInt(quantityInput.getAttribute('max'), 10);

    if (!isNaN(availableStock) && quantity > availableStock) {
        // Wenn die Menge den Bestand überschreitet, Feld markieren
        quantityInput.style.borderColor = 'red';
        quantityInput.style.backgroundColor = '#fff0f0';
        // Tooltip mit Joomla-Sprachstring setzen
        quantityInput.title = quantityInput.getAttribute('data-stock-exceeded-message');
        
        // Preisberechnung abbrechen und Zeilensumme auf 0 setzen
        if (totalField) {
            totalField.value = (0).toFixed(2);
        }
        updateOrderTotal(); // Gesamtsumme neu berechnen
        return; // Funktion hier beenden
    } else {
        // Markierung entfernen, wenn die Menge gültig ist
        quantityInput.style.borderColor = '';
        quantityInput.style.backgroundColor = '';
        quantityInput.title = '';
    }

    // Preisberechnung
    const priceBeforeDiscount = pricePerPiece * quantity;
    const priceAfterDiscount = priceBeforeDiscount * (1 - discount / 100);
    const totalPrice = priceAfterDiscount * factor;

    // Formatiere das Ergebnis auf 2 Dezimalstellen
    const formattedPrice = totalPrice.toFixed(2);

    // Ergebnis in das Total-Feld der Zeile schreiben
    if (totalField) {
        totalField.value = formattedPrice;
    }
    // Nach der Aktualisierung der Zeilensumme, aktualisiere die Gesamtsumme der Bestellung
    updateOrderTotal();
}

/**
 * Löst die Preisberechnung für alle Produktzeilen aus.
 * Nützlich, wenn sich ein globaler Wert wie der Staffelfaktor ändert.
 */
function updateAllPrices() {
    const rows = document.querySelectorAll('#product-table-body tr');
    rows.forEach(row => {
        // Wir nehmen das Mengen-Feld als Auslöser für die Berechnung
        const quantityInput = row.querySelector('input[name*="[reserved_quantity]"]');
        if (quantityInput) {
            calcPrice(quantityInput);
        }
    });
    // Nachdem alle Zeilen aktualisiert wurden, aktualisiere die Gesamtsumme
    updateOrderTotal();
}

/**
 * Berechnet die Gesamtsumme aller Produkte und aktualisiert die Anzeige.
 */
function updateOrderTotal() {
    
    let grandTotal = 0;

    // Finde alle Felder, die eine Zeilensumme enthalten
    const totalFields = document.querySelectorAll('input[name*="[product_price_total]"]');

    totalFields.forEach(field => {
        // Konvertiere den Wert in eine Zahl und addiere ihn zur Gesamtsumme
        const value = parseFloat(field.value.replace(',', '.'));
        if (!isNaN(value)) {
            grandTotal += value;
        }
    });

    // Berücksichtige den Bestellrabatt
    const discountField = document.querySelector('input[name="jform[orderdiscount]"]');
    let orderDiscount = 0;
    if (discountField) {
        orderDiscount = parseFloat(discountField.value.replace(',', '.'));
    }
    if (!isNaN(orderDiscount) && orderDiscount > 0) {
        grandTotal = grandTotal * (1 - orderDiscount / 100);
    }   
    // Auf 2 Dezimalstellen runden
    grandTotal = grandTotal.toFixed(2); 
    // Auf 0.05 abrunden
    grandTotal = (Math.floor(grandTotal * 20) / 20).toFixed(2); 


    // Ergebnis in das Order-Total-Feld der Zeile schreiben
    const totalField = document.querySelector('input[name*="[order_total_price]"]');
    if (totalField) {
        totalField.value = grandTotal;
    }
}



window.jSelectProducts = async function (selectedProduct) {
    const tbody = document.getElementById('product-table-body');
    if (!tbody) {
        console.error('Table body #product-table-body not found!');
        return;
    }

    // Doppelte Einträge verhindern
    if (tbody.querySelector(`input[value="${selectedProduct.id}"]`)) {
        alert('This product is already added.');
        return;
    }

    // URL für den AJAX-Request
    const url = 'index.php?option=com_tswrent&task=order.renderNewOrderProductRow&format=json';

    // Zusätzliche Daten aus dem Hauptformular auslesen
    const startDate = document.getElementById('jform_startdate').value;
    const endDate = document.getElementById('jform_enddate').value;
    const orderId = document.getElementById('jform_id').value;

    // Daten für den POST-Request vorbereiten
    const formData = new FormData();
    formData.append('id', selectedProduct.id); // Produkt-ID
    formData.append('jform[startdate]', startDate);
    formData.append('jform[enddate]', endDate);
    formData.append('jform[id]', orderId);

    try {
        const response = await fetch(url, {
            method: 'POST', // Methode auf POST ändern
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData // Formulardaten im Body senden
        });

        if (!response.ok) {
            console.error('HTTP error:', response.status, response.statusText);
            return;
        }

        const json = await response.json();

        if (json.success === false) {
            console.warn(json.message);
            return;
        }

        tbody.insertAdjacentHTML('beforeend', json.data.html);

        // Modal schließen
        const modalEl = document.getElementById('ModalSelect');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }

    } catch (e) {
        console.error('Fetch error:', e.message);
    }

};