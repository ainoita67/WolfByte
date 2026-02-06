document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('usuario').value.trim();
    const password = document.getElementById('clave').value;

    try {
        const response = await fetch('http://192.168.13.202/API/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al iniciar sesión');
        }

        console.log('Respuesta login:', data);

        // 👇 AQUÍ ESTÁ LA CLAVE
        localStorage.setItem('token', data.data.token);

        window.location.href = '/frontend/vistas/menu.html';

    } catch (error) {
        alert(error.message);
    }
});
