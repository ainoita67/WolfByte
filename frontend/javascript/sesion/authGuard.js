const token = localStorage.getItem('token');
sessionStorage.setItem("url", window.location.href);

if (!token) {
    console.warn('No hay token, redirigiendo a login');
    window.location.href = '/frontend/auth/login.html';
} else {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        
        //guardar el correo en sessionStorage para mostrarlo en el header
        sessionStorage.setItem("correo", payload.email);
        //guardar el rol en sessionStorage para mostrarlo en el header
        sessionStorage.setItem("rol", payload.rol);
        // guardar id_usuario en sessionStorage para usarlo en las reservas
        sessionStorage.setItem("id_usuario", payload.id_usuario);
        sessionStorage.setItem("nombre", payload.nombre);

        if (payload.exp && Date.now() >= payload.exp * 1000) {
            console.warn('Token expirado');
            localStorage.removeItem('token');
            window.location.href = '/frontend/auth/login.html';
        }
    } catch (e) {
        console.error('Token inválido', e);
        localStorage.removeItem('token');
        window.location.href = '/frontend/auth/login.html';
    }
}
