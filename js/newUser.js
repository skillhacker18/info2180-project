document.addEventListener('DOMContentLoaded', function () { 
    const form = document.getElementById('new-user-form');
    const responseMessage = document.getElementById('response-message');
    
    form.addEventListener('submit', function (event) {
        event.preventDefault(); 

        const formData = new FormData(form);
        formData.append('ajax', 'true'); 

        fetch('newuser.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  
        .then(data => {
            if (data.success) {
                responseMessage.innerHTML = `<p style="color: green;">${data.message}</p>`;
                form.reset(); 
            } else {
                responseMessage.innerHTML = `<p style="color: red;">${data.message}</p>`;
            }

            
            setTimeout(function() {
                responseMessage.innerHTML = '';
            }, 5000); 
        })
        .catch(error => {
            responseMessage.innerHTML = `<p style="color: red;">An error occurred: ${error.message}</p>`;

            
            setTimeout(function() {
                responseMessage.innerHTML = '';
            }, 4000);
        });
    });
});

