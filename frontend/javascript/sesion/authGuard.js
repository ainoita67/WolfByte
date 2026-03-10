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
        
        // El ID está en 'sub' - asegurarse de guardarlo como número, no como string "undefined"
        const idUsuario = payload.sub;  // ← directamente de sub que es 43
        
        sessionStorage.setItem("correo", payload.email || '');
        sessionStorage.setItem("rol", payload.rol || '');
        sessionStorage.setItem("id_usuario", idUsuario);  // ← ahora es 43, no "undefined"
        
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