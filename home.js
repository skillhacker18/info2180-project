
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const action = form.getAttribute('action') || window.location.href;
    const method = form.getAttribute('method') || 'POST';

    console.log('Submitting form:', form);

   
    const loadingIndicator = document.createElement('div');
    loadingIndicator.textContent = 'Submitting...';
    form.appendChild(loadingIndicator);

    fetch(action, {
        method: method,
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Response received for form submission:', data);
        document.querySelector('.form-container').innerHTML = data;
        alert('Form submitted successfully');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form');
    })
    .finally(() => {
        loadingIndicator.remove();
    });
}
