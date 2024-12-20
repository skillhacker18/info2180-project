document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form');
    const messageDiv = document.querySelector('.message');

    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(loginForm);

        fetch('login.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                messageDiv.textContent = data.message;
                messageDiv.style.color = 'red';

                setTimeout(() => {
                    loginForm.reset();
                    messageDiv.textContent = '';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.textContent = 'An unexpected error occurred. Please try again.';
            messageDiv.style.color = 'red';

            loginForm.reset();

            setTimeout(() => {
                messageDiv.textContent = '';
            }, 2000);
        });
    });
});
