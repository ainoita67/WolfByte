//API Obtener espacios
function obtenerEspaciosSelect(idSeleccionado=null){
    fetch(window.location.origin+"/API/espacios")
    .then(res => res.json())
    .then(response => {
        let espacios = response.data;
        
        let idPrincipal = 'salon';

        espacios.sort((a, b) => {
            if(a.id_recurso == idPrincipal) return -1;
            if(b.id_recurso == idPrincipal) return 1;
            return 0;
        });

        let selectEspacio = document.querySelectorAll(".selectEspacio")
        selectEspacio.forEach(select => {
            select.innerHTML = "";
            espacios.forEach(espacio => {
                let option = document.createElement("option");
                option.setAttribute("value", espacio.id_recurso);
                if(idSeleccionado!=null&&idSeleccionado==espacio.id_recurso){
                    option.selected=true;
                }
                if(espacio.id_recurso=='salon'){
                    option.textContent = 'Salón de actos';
                }else{
                    option.textContent = espacio.id_recurso+' - '+espacio.descripcion;
                }
                select.appendChild(option);
            });
        });
    })
    .catch(error => console.error("<p>Error al obtener espacios</p>", error));
}