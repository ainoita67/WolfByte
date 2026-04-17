// RESERVAS PERMANENTES

export async function getReservas() {
  try {
    const response = await fetch(`${API}/reservas_permanentes`);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - ",
        tipo: r.tipo
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getReservasInactivas() {
  try {
    const response = await fetch(`${API}/reservas_permanentes/inactivas`);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - "
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getReservasRecurso(id_recurso) {
  try {
    const response = await fetch(`${API}/reservas_permanentes/recurso/${id_recurso}`);
    if (!response.ok) throw new Error("Error al obtener reservas");

    const json = await response.json();

    if (!json.data || !Array.isArray(json.data)) {
      console.error("JSON.data no es un array", json.data);
      return [];
    }

    // crear instancias UsuarioSistema
    const reservas = json.data.map(r => {
      return({
        id: r.id_reserva_permanente,
        dia_semana: r.dia_semana,
        inicio: r.inicio,
        fin: r.fin,
        comentario: r.comentario?.trim() || " - ",
        recurso: r.id_recurso,
        unidades: r.unidades != null ? Number(r.unidades) : " - "
      });
    });

    return reservas;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export function mostrarToast(mensaje, tipo = 'success') {
    console.log('Toast:', mensaje, tipo);
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
        console.log('Contenedor de toasts creado');
    }
    
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    let bgClass = 'bg-success';
    let textColor = 'text-white';

    if (tipo === 'error'||tipo === 'danger') {
        bgClass = 'bg-danger';
    } else if (tipo === 'warning') {
        bgClass = 'bg-warning';
        textColor = 'text-black';
    } else if (tipo === 'info') {
        bgClass = 'bg-info';
    }
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${textColor} ${bgClass} border-0 fs-6" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}