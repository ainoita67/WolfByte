
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const login = document.getElementById('login').value;
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('http://localhost/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      // ESTO ES OBLIGATORIO CON SESIONES
      credentials: 'include',

      body: JSON.stringify({
        login: login,
        password: password
      })
    });

    const data = await response.json();

    if (!response.ok) {
      throw data;
    }

    console.log('Usuario logueado:', data.user);

    // Redirección tras login
    window.location.href = '../views/menu.php';

  } catch (err) {
    document.getElementById('error').textContent =
      err.message || 'Error al iniciar sesión';
  }
});

