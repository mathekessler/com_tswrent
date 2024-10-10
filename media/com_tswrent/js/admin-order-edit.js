'use strict';

function c_contact(){
    
    const customer = document.getElementById("jform_customer");
    const customer_value= customer.value
    // Replace this URL with the actual endpoint you want to fetch data from
    const url = 'index.php?option=com_tswrent&task=order.c_contact&id='+customer_value+'&format=raw';
    var responseData='';
    
    ajaxrequest(url)
        .then(response => {
           responseData=response; // Call the function to process the result
           optionsC_contact(responseData);
        })
        .catch(error =>{
            console.log('Error:', error);
        });         
}

function ajaxrequest(url) {

    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        
        xhr.onreadystatechange = () =>
        {
            var DONE = 4; // readyState 4 means the request is done.
            var OK = 200; // status 200 is a successful return.
            if (xhr.readyState === DONE) 
            {
                if (xhr.status === OK) {
                    var result = JSON.parse(xhr.responseText); // Parse the response as JSON
                    resolve(result); // Resolve the promise with the result
                } else {
                    reject('Error: ' + xhr.status); // Reject the promise with an error message
                }
            }  
        };
        xhr.send();
    }); 
 
}

function optionsC_contact(responseData) {
    
   deleteOptions();

    const select = document.getElementById("jform_c_contact");
    const elmts = responseData.data;
        let value = "";
        let text = "";
        for (let i = 0; i < elmts.length; i++) {
            value = elmts[i].value;
            text = elmts[i].text;
            let el = document.createElement("option");
            el.textContent = text;
            el.value = value;
            select.appendChild(el);
        }
}
function deleteOptions() {
    const selectobject = document.getElementById("jform_c_contact");  
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

function getFactor(){
    const graduation = document.getElementById("jform_graduation").value;
    const days = document.getElementById("jform_days").value;
    const url = 'index.php?option=com_tswrent&task=order.getGraduationFactor&id='+graduation+'&days='+days+'&format=raw';

    ajaxrequest(url)
        .then(response => {
           var responseData=response; // Call the function to process the result
           document.getElementById("jform_factor").value = responseData.data.factor;
           console.log(responseData.data);

        })
        .catch(error =>{
            console.log('Error:', error);
        }); 
}

function addProductRow(){
    // Handle the click event of the "Add Template" button here
    // For example, you can add your template creation logic here
    const templateContainer = document.getElementById("order-products-table");
    const template = document.getElementById("order-products-template");

    // Clone the template content and append it to the container
    const clone = document.importNode(template.content, true);

    templateContainer.appendChild(clone);
}

function refreshPartOfPage() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?option=com_tswrent&view=order&layout=edit_products&format=raw', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            // Update the specific part of the page with the new content
            document.getElementById('targetDiv').innerHTML = response;
        }
    };

    xhr.send();
}

