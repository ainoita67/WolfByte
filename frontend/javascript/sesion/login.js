document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById('iniciosesion').disabled=true;
    document.getElementById('cargandologin').classList.remove('d-none');

    const email = document.getElementById('usuario').value.trim();
    const password = document.getElementById('clave').value;

    try {
        const response = await fetch(`${API}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al iniciar sesión');
        }

        localStorage.setItem('token', data.data.token);
        const token = localStorage.getItem('token');
        const payload = JSON.parse(atob(token.split('.')[1]));
        
        let redirect = sessionStorage.getItem("url");
        let finalredirect=redirect;
        let rol = Number(payload.rol);

        if(redirect && redirect.includes("/frontend/vistas/administrador/usuarios") && rol == 30){
            finalredirect = "/frontend/vistas/administrador/menuadministrador.html";
        } else if (
            !redirect ||
            redirect.includes("/frontend/auth/login.html") ||
            (
                redirect.includes("/frontend/vistas/administrador") && rol !== 30 && rol !== 40
            ) ||
            (
                redirect.includes("/frontend/vistas/reservas/portatiles") && rol !== 20 && rol !== 30 && rol !== 40
            )
        ) {
            finalredirect = "/frontend/vistas/menu.html";
        }

        window.location.href = finalredirect;

    } catch (error) {
        document.getElementById('mostrarerror').textContent = error.message;
        document.getElementById('mostrarerror').classList.remove('d-none');
        document.getElementById('iniciosesion').classList.remove('mt-5');
    }
    document.getElementById('iniciosesion').disabled=false;
    document.getElementById('cargandologin').classList.add('d-none');
});
