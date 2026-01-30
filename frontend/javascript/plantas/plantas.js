const API_PLANTAS = "http://192.168.13.202:80/API/plantas";

const tablaPlantas = document.getElementById("tablaPlantas");
const modal = new bootstrap.Modal(document.getElementById("modalPlanta"));

const idPlantaInput = document.getElementById("idPlanta");
const nombreEdificioInput = document.getElementById("nombreEdificio");
const plantaInput = document.getElementById("planta");

const modalTitulo = document.getElementById("modalTitulo");
const btnGuardar = document.getElementById("btnGuardar");

document.addEventListener("DOMContentLoaded", () => {
    cargarPlantas();

    document.querySelector(".btn-success").addEventListener("click", abrirCrear);
    btnGuardar.addEventListener("click", guardarPlanta);
});


async function cargarPlantas() {
    tablaPlantas.innerHTML = "";

    const res = await fetch(API_PLANTAS);
    const data = await res.json();

    data.forEach(p => {
 acknowledged = `
            <tr>
                <td>${p.id}</td>
                <td>${p.nombre_edificio}</td>
                <td>${p.planta}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick='abrirEditar(${JSON.stringify(p)})'>
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarPlanta(${p.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tablaPlantas.innerHTML += fila;
    });
}
function abrirCrear() {
    modalTitulo.textContent = "Crear planta";
    idPlantaInput.value = "";
    nombreEdificioInput.value = "";
    plantaInput.value = "";

    modal.show();
}

function abrirEditar(planta) {
    modalTitulo.textContent = "Editar planta";

    idPlantaInput.value = planta.id;
    nombreEdificioInput.value = planta.nombre_edificio;
    plantaInput.value = planta.planta;

    modal.show();
}


async function guardarPlanta() {
    const id = idPlantaInput.value;

    const datos = {
        nombre_edificio: nombreEdificioInput.value,
        planta: plantaInput.value
    };

    let url = API_PLANTAS;
    let metodo = "POST";

    if (id) {
        url += "/" + id;
        metodo = "PUT";
    }

    await fetch(url, {
        method: metodo,
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(datos)
    });

    modal.hide();
    cargarPlantas();
}


async function eliminarPlanta(id) {
    if (!confirm("¿Seguro que quieres eliminar esta planta?")) return;

    await fetch(API_PLANTAS + "/" + id, {
        method: "DELETE"
    });

    cargarPlantas();
}
