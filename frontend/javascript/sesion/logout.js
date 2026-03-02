document.addEventListener('click', async (e) => {

    if (!e.target.closest('#logoutBtn')) return;

    e.preventDefault();

    try {
        const response = await fetch(`${API}/logout`, {
            method: 'POST',
            credentials: 'include'
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al cerrar sesión');
        }

<<<<<<< Updated upstream
        console.log('Logout OK:', data);

        // Redirige al login
        window.location.href = '/frontend/auth/login.html';
=======
        // Limpiar almacenamiento
        localStorage.removeItem('token');
        sessionStorage.removeItem('correo');
        sessionStorage.removeItem('rol');
        sessionStorage.removeItem('id_usuario');

        window.location.href = `${BASE}/auth/login.html`;
>>>>>>> Stashed changes

    } catch (error) {
        alert(error.message);
    }
});