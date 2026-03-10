const API_BASE = 'http://192.168.13.202/API';

async function apiFetch(endpoint, options = {}) {
    const token = localStorage.getItem('token');

    const response = await fetch(API_BASE + endpoint, {
        ...options,
        headers: {
            ...(options.headers || {}),
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    });

    // Token inválido o caducado
    if (response.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/public/views/login.php';
        return null;
    }

    return response;
}
