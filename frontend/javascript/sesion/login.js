document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById('iniciosesion').disabled=true;
    document.getElementById('cargandologin').classList.remove('d-none');

    const email = document.getElementById('usuario').value.trim();
    const password = document.getElementById('clave').value;

    try {
        const response = await fetch(`${API}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Error al iniciar sesión');
        }

        console.log('Respuesta login:', data);

        localStorage.setItem('token', data.data.token);

        window.location.href = '/frontend/vistas/menu.html';

    } catch (error) {
        document.getElementById('mostrarerror').textContent = error.message;
        document.getElementById('mostrarerror').classList.remove('d-none');
        document.getElementById('iniciosesion').classList.remove('mt-5');
    }
    document.getElementById('iniciosesion').disabled=false;
    document.getElementById('cargandologin').classList.add('d-none');
});
