document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const login = document.getElementById('usuario').value.trim();
    const password = document.getElementById('clave').value;

    try {
        const response = await fetch('http://192.168.13.202/API/public/login', {
            method: 'POST',
            credentials: 'include', 
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ login, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al iniciar sesión');
        }

        // LOGIN OK
        console.log('Usuario logueado:', data);

        // Redirección
        window.location.href = '/public/views/menu.php';

    } catch (error) {
        mostrarError(error.message);
    }
});

function mostrarError(mensaje) {
    let alert = document.getElementById('loginError');

    if (!alert) {
        alert = document.createElement('div');
        alert.id = 'loginError';
        alert.className = 'alert alert-danger mt-3';
        document.querySelector('form').prepend(alert);
    }

    alert.textContent = mensaje;
}
