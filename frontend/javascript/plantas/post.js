// plantas/post.js
const API_BASE = localStorage.getItem('API_BASE') || 'http://192.168.13.202:80';
const API_PLANTAS = `${API_BASE}/API/plantas`;
import { mostrarToast } from '/frontend/javascript/reservas/crud.js';

// Mapeo de nombres de edificio a IDs
const edificioToId = {
    'Loscos': 1,
    'Ram': 2
};

function guardarCambiosPlanta(idEdificio, numeroPlantaOriginal) {
    console.log(`Guardando cambios - Edificio: ${idEdificio}, Planta original: ${numeroPlantaOriginal}`);
    
    const nuevoNumeroPlanta = document.getElementById("numeroPlantaInput")?.value;
    
    if (!nuevoNumeroPlanta) {
        mostrarToast("El número de planta es obligatorio", 'danger');
        return;
    }
    
    // Preparar datos según lo que espera tu API
    const datos = {
        nuevo_numero_planta: parseInt(nuevoNumeroPlanta)
    };
    
    console.log("Enviando datos:", datos);
    console.log("URL:", `${API_PLANTAS}/${idEdificio}?numero_planta=${numeroPlantaOriginal}`);
    
    const submitBtn = document.querySelector("#modalEditarBody .btn-primary");
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
    }
    
    fetch(`${API_PLANTAS}/${idEdificio}?numero_planta=${numeroPlantaOriginal}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(datos)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => {
                throw new Error(err.message || err.error || `Error ${res.status}`);
            }).catch(() => {
                throw new Error(`Error HTTP: ${res.status}`);
            });
        }
        return res.json();
    })
    .then(response => {
        console.log("Respuesta:", response);
        mostrarToast("Planta actualizada correctamente", 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById("modalEditar"));
        if (modal) modal.hide();
        
        obtenerPlantas();
    })
    .catch(err => {
        console.error("Error:", err);
        mostrarToast(`Error al actualizar: ${err.message}`, 'danger');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Guardar cambios';
        }
    });
}

// Configurar formulario de creación
document.addEventListener("DOMContentLoaded", function() {
    const formCrear = document.getElementById("formCrearPlanta");
    if (formCrear) {
        formCrear.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const nombreEdificio = this.querySelector("select[name='edificio']")?.value;
            const numeroPlanta = this.querySelector("input[name='numero_planta']")?.value;
            
            // Convertir nombre de edificio a ID
            const idEdificio = edificioToId[nombreEdificio];
            
            if (!idEdificio) {
                mostrarToast("Edificio no válido", 'danger');
                return;
            }
            
            if (!numeroPlanta) {
                mostrarToast("El número de planta es obligatorio", 'danger');
                return;
            }
            
            // La API espera solo el número de planta en el body
            const datos = {
                numero_planta: parseInt(numeroPlanta)
            };
            
            console.log(`Creando planta en edificio ${idEdificio}:`, datos);
            
            const submitBtn = this.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando...';
            }
            
            fetch(`${API_PLANTAS}/${idEdificio}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify(datos)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw new Error(err.message || err.error || `Error ${res.status}`);
                    }).catch(() => {
                        throw new Error(`Error HTTP: ${res.status}`);
                    });
                }
                return res.json();
            })
            .then(response => {
                console.log("Respuesta:", response);
                mostrarToast("Planta creada correctamente", 'success');
                
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalCrear"));
                if (modal) modal.hide();
                
                obtenerPlantas();
                this.reset();
            })
            .catch(err => {
                console.error("Error:", err);
                mostrarToast(`Error al crear: ${err.message}`, 'danger');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Crear planta';
                }
            });
        });
    }
});