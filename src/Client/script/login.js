async function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('http://localhost:3000/src/Server/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password }),
        });

        const data = await response.json();

        if (response.status === 200) {
            // Redirect based on account_type
            let redirectUrl;
            if (data.user.account_type === 'Admin') {
                redirectUrl = '/src/Client/scripts/admin.html';
            } else if (data.user.account_type === 'User') {
                redirectUrl = '/src/Client/scripts/dashboard.html';
            } else {
                // Fallback for unexpected types (optional)
                alert('Unknown account type. Redirecting to default dashboard.');
                redirectUrl = '/src/Client/scripts/home.html';
            }

            alert('Login successful!');
            window.location.href = redirectUrl;
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        console.error(error);
    }
}

document.querySelector('form').addEventListener('submit', handleLogin);