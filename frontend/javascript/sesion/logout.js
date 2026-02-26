document.getElementById('logoutBtn').addEventListener('click', async () => {
    try {
        const response = await fetch('http://192.168.13.202/API/logout', {
            method: 'POST',      // O 'GET', tu API soporta ambos
            credentials: 'include'  // Muy importante para enviar la cookie de sesión
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al cerrar sesión');
        }

        // Eliminar token JWT
        localStorage.removeItem('token');

        // Limpiar datos de sesión del frontend
        sessionStorage.removeItem('correo');
        sessionStorage.removeItem('rol');
        sessionStorage.removeItem('id_usuario');

        console.log('Logout OK:', data);

        // Redirige al login
        window.location.href = '/frontend/auth/login.html';

    } catch (error) {
        alert(error.message);
    }
});



