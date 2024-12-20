document.addEventListener('DOMContentLoaded', function() {
    function fetchContactDetails(contactId) {
        fetch(`Contactdetails.php?id=${contactId}`)
            .then(response => response.text())
            .then(data => {
                document.querySelector('.contact-details').innerHTML = data;
            })
            .catch(error => console.error('Error fetching contact details:', error));
    }

    function handleFormSubmission(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const newNote = document.createElement('li');
                const noteContent = form.querySelector('textarea').value;
                const currentTime = new Date().toLocaleString();

                newNote.innerHTML = `
                    <p><strong>${data.user_name}</strong>: ${noteContent}</p>
                    <p><em>${currentTime}</em></p>
                `;
                document.querySelector('.note-form ul').appendChild(newNote);
                
                form.querySelector('textarea').value = '';
            })
            .catch(error => console.error('Error submitting form:', error));
        });
    }

    const contactId = new URLSearchParams(window.location.search).get('id');
    if (contactId) {
        fetchContactDetails(contactId);
    }

    document.querySelectorAll('form').forEach(handleFormSubmission);
});
