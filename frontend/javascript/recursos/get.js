//API Obtener recursos para filtrar
function obtenerRecursosSelect(){
    fetch(window.location.origin+"/API/recurso")
    .then(res => res.json())
    .then(response => {
        let recursos = response.data;

        let idPrincipal = 'salon';

        recursos.sort((a, b) => {
            if(a.id_recurso == idPrincipal) return -1;
            if(b.id_recurso == idPrincipal) return 1;
            return 0;
        });

        let filtrarRecurso = document.querySelectorAll(".filtrarRecurso")
        filtrarRecurso.forEach(select => {
            select.innerHTML = "";
            let optiontodos=document.createElement("option");
            optiontodos.setAttribute("value", "Todos");
            optiontodos.textContent = "Todos";
            select.appendChild(optiontodos);

            recursos.forEach(recurso => {
                let option = document.createElement("option");
                option.setAttribute("value", recurso.id_recurso);
                if(recurso.id_recurso=='salon'){
                    option.textContent = 'Salón de actos';
                }else{
                    option.textContent = recurso.id_recurso+' - '+recurso.descripcion;
                }
                select.appendChild(option);
            });
        });
    })
    .catch(error => console.error("<p>Error al obtener recursos</p>", error));
}

export async function getInfoRecurso(id_recurso) {
  try {
    const response = await fetch(`${API}/recurso/${id_recurso}`);
    if (!response.ok) throw new Error("Error al obtener informacion del recurso");

    const json = await response.json();

    recurso = {
        id: json.data.id_recurso,
        descripcion: json.data.descripcion,
        tipo: json.data.tipo,
        activo: json.data.activo === 1,
        especial: json.data.especial === 1,
        numero_planta: json.data.numero_planta,
        nombre_planta: json.data.nombre_planta,
        id_edificio: json.data.id_edificio,
        nombre_edificio: json.data.nombre_edificio,
        es_aula: json.data.es_aula === 1,
        unidades: json.data.unidades
    }

    return recurso;
  } catch (error) {
    console.error(error);
    return [];
  }
}