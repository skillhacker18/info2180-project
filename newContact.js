document.getElementById('myForm').addEventListener('submit', function (e) {
    e.preventDefault(); 

    let formData = new FormData(this);
    console.log("Form data being sent:", Object.fromEntries(formData.entries()));

    
    fetch('NewContact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Contact created successfully!');
            
            this.reset(); 
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
