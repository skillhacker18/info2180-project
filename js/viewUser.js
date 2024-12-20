document.addEventListener('DOMContentLoaded', function () {
    function loadUserData() {
        fetch('ViewUser.php?ajax=true')
            .then(response => response.json()) 
            .then(users => {
                const tbody = document.querySelector('#users-table tbody');
                tbody.innerHTML = ''; 

                if (users.length > 0) {
                    users.forEach(user => {
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.firstname} ${user.lastname}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>${user.created_at}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="4">No users found.</td>';
                    tbody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error loading user data:', error);
                alert('An error occurred while loading user data.');
            });
    }


    loadUserData();
});
