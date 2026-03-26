document.addEventListener('click', async (e) => {

    if (!e.target.closest('#logoutBtn')) return;

    e.preventDefault();

    try {
        let id = sessionStorage.getItem('id_usuario');

        const response = await fetch(`${API}/logout`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({id_usuario: id})
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al cerrar sesión');
        }

        // Limpiar almacenamiento
        localStorage.removeItem('token');
        sessionStorage.removeItem('correo');
        sessionStorage.removeItem('rol');
        sessionStorage.removeItem('id_usuario');

        window.location.href = `${BASE}/auth/login.html`;

    } catch (error) {
        alert(error.message);
    }
});