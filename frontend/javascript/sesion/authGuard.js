// sesion/authGuard.js
console.log('AuthGuard ejecutado');

const token = localStorage.getItem('token');
console.log('Token leído:', token ? 'Presente' : 'No presente');

if (!token) {
    console.warn('No hay token, redirigiendo a login');
    window.location.href = '/frontend/auth/login.html';
} else {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        console.log('Payload JWT:', payload);
        
        // Guardar datos en sessionStorage - asegurar que se guardan correctamente
        sessionStorage.setItem("correo", payload.email || '');
        sessionStorage.setItem("rol", payload.rol || '');
        sessionStorage.setItem("id_usuario", payload.id_usuario || payload.sub || payload.user_id || '');
        
        // Verificar que se guardaron
        console.log('Datos guardados en sessionStorage:', {
            correo: sessionStorage.getItem('correo'),
            rol: sessionStorage.getItem('rol'),
            id_usuario: sessionStorage.getItem('id_usuario')
        });

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