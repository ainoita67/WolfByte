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