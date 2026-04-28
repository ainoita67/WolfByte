import { getPermanentesRecurso, generarHorarioPermanentes } from "/frontend/javascript/reservas/horario_permanente.js";
import { generarHorarioRealEventos, getLiberacionesRecurso, aplicarLiberaciones, combinarDisponiblesYReservas } from "/frontend/javascript/reservas/disponibilidad.js";

export async function cargarReservas(idRecurso) {
    try {
        const res = await fetch(`${API}/reservaEspacio/espacio/${idRecurso}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        const text = await res.text(); // primero leemos como texto
        let json;
        try {
            json = JSON.parse(text); // luego intentamos parsear a JSON
        } catch {
            console.error("Respuesta no es JSON:", text);
            return [];
        }

        if (json.status !== "success") {
            console.error("Error API:", json);
            return [];
        }

        // Si no hay reservas, devolver array vacío
        if (!json.data || json.data.length === 0) {
            return [];
        }

        const eventos = json.data.filter(r => r.autorizada == 1 || r.autorizada == null).map(r => ({
            id: r.id_reserva,
            text: `${r.asignatura} - ${r.grupo}\n${r.profesor} - ${r.actividad}`,
            start: r.inicio.replace(" ", "T"),
            end: r.fin.replace(" ", "T"),
            f_creacion: r.f_creacion,
            id_usuario: r.id_usuario,
            id_usuario_autoriza: r.id_usuario_autoriza ?? null,
            asignatura: r.asignatura,
            grupo: r.grupo,
            profesor: r.profesor,
            actividad: r.actividad,
            autorizada: r.autorizada,
            observaciones: r.observaciones
        }));

        return eventos; // array de eventos para el calendario
    } catch (error) {
        console.error("Error cargando reservas:", error);
    }
}

// generar el calendario completo con reservas permanentes libereaciones y reservas reales
export async function generarEventos(idRecurso) {
    // Cargar reservas permanentes y slot de disponibilidad
    const permanentes = await getPermanentesRecurso(idRecurso);
    const horario = generarHorarioPermanentes(permanentes);
    const eventos = await generarHorarioRealEventos(horario);
    // Aplicar liberaciones para obtener eventos disponibles
    const liberaciones = await getLiberacionesRecurso(idRecurso);
    const eventosdisponibles = aplicarLiberaciones(eventos, liberaciones);
    // Cargar reservas reales
    const eventosReservas = await cargarReservas(idRecurso) || [];
    // Combinar eventos disponibles con reservas reales (reservas reales tienen prioridad)
    const eventoscombinados = combinarDisponiblesYReservas(eventosdisponibles, eventosReservas);

    return eventoscombinados;
}



export function mostrarToast(mensaje, tipo = 'success') {    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
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