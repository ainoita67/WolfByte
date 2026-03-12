// sesion/authGuard.js
console.log('AuthGuard ejecutado');

const token = localStorage.getItem('token');
console.log('Token leído:', token);

if (!token) {
    console.warn('No hay token, redirigiendo a login');
    window.location.href = '/frontend/auth/login.html';
} else {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        console.log('Payload JWT:', payload);
        
        // Guardar datos en sessionStorage
        sessionStorage.setItem("correo", payload.email);
        sessionStorage.setItem("rol", payload.rol);
        // guardar id_usuario en sessionStorage para usarlo en las reservas
        sessionStorage.setItem("id_usuario", payload.id_usuario);
        sessionStorage.setItem("nombre", payload.nombre);

        // Verificar expiración
        if (payload.exp && Date.now() >= payload.exp * 1000) {
            console.warn('Token expirado');
            localStorage.removeItem('token');
            sessionStorage.clear();
            window.location.href = '/frontend/auth/login.html';
        }
    } catch (e) {
        console.error('Token inválido', e);
        localStorage.removeItem('token');
        sessionStorage.clear();
        window.location.href = '/frontend/auth/login.html';
    }
}