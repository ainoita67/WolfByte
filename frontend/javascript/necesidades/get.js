function capitalizar(string) {
    return string.charAt(0).toUpperCase()+string.slice(1).toLowerCase();
}



//API Obtener necesidades
function obtenerNecesidades(){
    fetch(window.location.origin+"/API/necesidades")
    .then(res => res.json())
    .then(response => {
        let necesidades = response.data;

        let contenedor = document.getElementById("necesidadesReservaContainer");
        contenedor.innerHTML = "";
        if(!necesidades||necesidades.length==0){
            let card = document.createElement("div");
            card.className = "card bg-secondary-subtle h-100 mt-5 reserva-card text-center";
            card.innerHTML = `
                <div class="card-body">No se han encontrado necesidades</div>
            `;
            contenedor.appendChild(card);
        }else{
            necesidades.forEach(necesidad => {
                let card = document.createElement("div");
                card.className = "col-12 col-md-6 col-lg-4 mt-5";
                card.innerHTML = `
                    <div class="card text-center shadow-sm overflow-hidden h-100">
                        <div class="bg-blue card-head rounded-top">
                            <p class="fs-6 text-light m-0 pt-2">ID: ${necesidad.id_necesidad}</p>
                        </div>
                        <div class="card-body">
                            <p class="fs-5 card-title">${necesidad.nombre}</p>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-sm bg-warning text-black"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    onclick="editarNecesidad(${necesidad.id_necesidad}, '${necesidad.nombre}')">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                        </div>
                    </div>
                `;
                contenedor.appendChild(card);
            });
        }
    });
}



//API Obtener necesidades para filtrar
function obtenerNecesidadesSelectMultiple(){
    fetch(window.location.origin+"/API/necesidades")
    .then(res => res.json())
    .then(response => {
        let necesidades = response.data;

        necesidades.sort((a, b) => 
            a.nombre.localeCompare(b.nombre, 'es', { sensitivity: 'base' })
        );
        
        let selectNecesidad = document.querySelectorAll(".selectMultipleNecesidad")
        selectNecesidad.forEach(select => {
            select.innerHTML = "";

            necesidades.forEach(necesidad => {
                let option = document.createElement("option");
                option.setAttribute("value", necesidad.id_necesidad);
                option.textContent = necesidad.nombre;
                select.appendChild(option);
            });
        });
    })
    .catch(error => console.error("<p>Error al obtener necesidades</p>", error));
}