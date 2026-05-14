function obtenerMisReservas(){
    usuario=sessionStorage.getItem("id_usuario");
    fetch(window.location.origin+"/API/mis-reservas/"+usuario, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
    .then(res => res.json())
    .then(response => {
        let reservas = response.data;
        let tarjetasReservas = document.getElementById("misReservasTarjetas");
        if(!tarjetasReservas) return;
        mostrarMisReservas(reservas, tarjetasReservas);
    });
}

function mostrarMisReservas(reservas, tarjetasReservas){
    tarjetasReservas.innerHTML = "";
    if(!reservas||reservas.length === 0){
        let card = document.createElement("div");
        card.className = "card h-100 p-0 mb-4 reserva-card text-center";
        card.innerHTML = `
            <div class="card-body bg-secondary-subtle">No se han encontrado reservas</div>
        `;
        tarjetasReservas.appendChild(card);
    }else{
        reservas.forEach(reserva => {
            let div = document.createElement("div");
            div.className="col-lg-3 col-6";
            let divReserva = document.createElement("div");
            divReserva.className="card h-100 p-0 mb-4 reserva-card";
            if(reserva.autorizada!="0"){
                divReserva.addEventListener("click", function(){
                    mostrarDatosModal(reserva);

                    let modal = new bootstrap.Modal(document.getElementById("modalReserva"));
                    modal.show();
                });
            }else{
                divReserva.addEventListener("click", function(){
                    mostrarDatosModal(reserva);

                    let modal = new bootstrap.Modal(document.getElementById("modalVerReserva"));
                    modal.show();
                });
            }

            let tipo='Portátil';                
            if(reserva.tipo == 'Reserva_espacio'){
                tipo='Espacio';
            }

            divReserva.innerHTML = `
                <div class="card-body bg-secondary-subtle">
                    <p class="text-black fw-bold mb-0">Reserva #${reserva.id_reserva}</p>
                    <p class="text-black mb-0"><span class="fw-bold">${tipo}: </span>${reserva.id_recurso}
                    <p class="text-black mb-0"><span class="fw-bold">Fecha inicio: </span>${formatearFecha(reserva.inicio)}</p>
                    <p class="text-black mb-0"><span class="fw-bold">Fecha fin: </span>${formatearFecha(reserva.fin)}</p>
                    ${reserva.unidades !== null ? `<p class="text-black mb-0"><span class="fw-bold">Unidades: </span>${reserva.unidades}</p>` : ''}
                </div>
            `;
            if(reserva.autorizada=="1"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado aceptado text-light pt-2 ps-3 fs-6">Aceptado</div>';
            }else if(reserva.autorizada=="0"){
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado rechazado text-black pt-2 ps-3 fs-6">Rechazado</div>';
            }else{
                divReserva.innerHTML=divReserva.innerHTML+'<div class="aceptado-rechazado text-light pt-2 ps-3 fs-6">En proceso</div>';
            }

            divReserva.addEventListener("click", function(){
                if(reserva.autorizada!==0){
                    mostrarDatosModal(reserva);
                }else{
                    mostrarVerDatosModal(reserva);
                }
            });

            div.appendChild(divReserva);
            tarjetasReservas.appendChild(div);
        });
    }
}


function mostrarDatosModal(reserva){
    let selectNecesidades = document.getElementById("reserva_necesidades");
    
    if(reserva.tipo == "Reserva_espacio"){
        let necesidades=[];

        if(typeof reserva.necesidades === "string"){
            necesidades = reserva.necesidades ? reserva.necesidades.split(',').map(n => n.trim()) : [];
        }

        for (let i = 1; i < selectNecesidades.options.length; i++) {
            selectNecesidades.options[i].selected = false;
            selectNecesidades.options[i].classList.remove("border", "border-primary");
        }

        seleccionadas=false;
        necesidades.forEach(nec => {
            for (let i = 1; i < selectNecesidades.options.length; i++) {
                if (selectNecesidades.options[i].value === nec) {
                    selectNecesidades.options[i].selected = true;
                    selectNecesidades.options[i].classList.add("border", "border-primary");
                    seleccionadas=true;
                }
            }
        });
        if(!seleccionadas||necesidades.length==0){
            selectNecesidades.options[0].selected=true;
            selectNecesidades.options[0].classList.add("border", "border-primary");
        }else{
            selectNecesidades.options[0].selected=false;
            selectNecesidades.options[0].classList.remove("border", "border-primary");
        }
    }

    let autorizada='Denegada';
    if(reserva.autorizada==null){
        autorizada='Pendiente';
    }else if(reserva.autorizada==1){
        autorizada='Autorizada';
    }
    document.getElementById("reserva_autorizada").value = reserva.autorizada;
    document.getElementById("reserva_id").value = reserva.id_reserva;
    document.getElementById("reserva_f_creacion").value = reserva.f_creacion;
    document.getElementById("reserva_inicio").value = reserva.inicio;
    document.getElementById("reserva_fin").value = reserva.fin;
    document.getElementById("reserva_tipo").value = reserva.tipo;
    document.getElementById("reserva_espacio_portatil").value = reserva.id_recurso;
    document.getElementById("reserva_asignatura").value = reserva.asignatura;
    document.getElementById("reserva_grupo").value = reserva.grupo;
    document.getElementById("reserva_profesor").value = reserva.profesor;
    document.getElementById("reserva_id_usuario").value = reserva.id_usuario;
    document.getElementById("reserva_usuario").value = reserva.nombreusuario;
    document.getElementById("reserva_id_usuario_autoriza").value = reserva.id_usuario_autoriza;
    
    document.getElementById("reserva_unidades").value = reserva.unidades;
    document.getElementById("reserva_espacio_uso").value = reserva.usaenespacio;
    document.getElementById("reserva_actividad").value = reserva.actividad;
    document.getElementById("reserva_observaciones").value = reserva.observaciones;
                    
    if (reserva.tipo == 'Reserva_espacio') {
        document.getElementById("div_reserva_usuario").classList.add('col-lg-6');
        document.getElementById("div_reserva_unidades").classList.add('d-none');
        document.getElementById("reserva_unidades").required = false;
        document.getElementById("div_reserva_espacio_uso").classList.add('d-none');
        document.getElementById("reserva_espacio_uso").required = false;
        document.getElementById("div_reserva_actividad").classList.remove('d-none');
        document.getElementById("reserva_actividad").required = true;
        document.getElementById("div_reserva_necesidades").classList.remove('d-none');
    } else {
        document.getElementById("div_reserva_usuario").classList.remove('col-lg-6');
        document.getElementById("div_reserva_unidades").classList.remove('d-none');
        document.getElementById("reserva_unidades").required = true;
        document.getElementById("div_reserva_espacio_uso").classList.remove('d-none');
        document.getElementById("reserva_espacio_uso").required = true;
        document.getElementById("div_reserva_actividad").classList.add('d-none');
        document.getElementById("reserva_actividad").required = false;
        document.getElementById("div_reserva_necesidades").classList.add('d-none');
    }
}


function mostrarVerDatosModal(reserva){
    let ulNecesidades = document.getElementById("ver_reserva_necesidades");
    ulNecesidades.innerHTML='';
    
    if(reserva.tipo == "Reserva_espacio"){
        let necesidades=[];

        if(typeof reserva.necesidades === "string"){
            necesidades = reserva.nombrenecesidades ? reserva.nombrenecesidades.split(',').map(n => n.trim()) : [];
        }

        if(!necesidades||necesidades.length==0){
            document.getElementById("p_ver_necesidades").classList.add('d-none');
        }else{
            necesidades.forEach(nec => {
                let li=document.createElement('li');
                li.classList.add('text-black')
                li.textContent=nec;
                ulNecesidades.appendChild(li);
            });
            document.getElementById("p_ver_necesidades").classList.remove('d-none');
        }
    }

    let autorizada='Denegada';
    if(reserva.autorizada==null){
        autorizada='Pendiente';
    }else if(reserva.autorizada==1){
        autorizada='Autorizada';
    }
    
    document.getElementById("ver_reserva_id").textContent = reserva.id_reserva;
    document.getElementById("ver_reserva_f_creacion").textContent = reserva.f_creacion;
    document.getElementById("ver_reserva_inicio").textContent = reserva.inicio;
    document.getElementById("ver_reserva_fin").textContent = reserva.fin;
    document.getElementById("ver_reserva_espacio_portatil").textContent = reserva.id_recurso;
    document.getElementById("ver_reserva_asignatura").textContent = reserva.asignatura;
    document.getElementById("ver_reserva_grupo").textContent = reserva.grupo;
    document.getElementById("ver_reserva_profesor").textContent = reserva.profesor;
    document.getElementById("ver_reserva_usuario").textContent = reserva.nombreusuario;
    
    document.getElementById("ver_reserva_unidades").textContent = reserva.unidades ?? '-';
    document.getElementById("ver_reserva_espacio_uso").textContent = reserva.usaenespacio ?? '-';
    document.getElementById("ver_reserva_actividad").textContent = reserva.actividad ?? '-';
    document.getElementById("ver_reserva_observaciones").textContent = reserva.observaciones ?? '-';
    
    if (reserva.tipo == 'Reserva_espacio') {
        document.getElementById("p_ver_unidades").classList.add('d-none');
        document.getElementById("reserva_unidades").required = false;
        document.getElementById("p_ver_espacio_uso").classList.add('d-none');
        document.getElementById("reserva_espacio_uso").required = false;
        document.getElementById("p_ver_actividad").classList.remove('d-none');
        document.getElementById("reserva_actividad").required = true;
        if(!necesidades||necesidades.length==0){
            document.getElementById("p_ver_necesidades").classList.add('d-none');
        }else{
            document.getElementById("p_ver_necesidades").classList.remove('d-none');
        }
    } else {
        document.getElementById("p_ver_unidades").classList.remove('d-none');
        document.getElementById("reserva_unidades").required = true;
        document.getElementById("p_ver_espacio_uso").classList.remove('d-none');
        document.getElementById("reserva_espacio_uso").required = true;
        document.getElementById("p_ver_actividad").classList.add('d-none');
        document.getElementById("reserva_actividad").required = false;
        document.getElementById("p_ver_necesidades").classList.add('d-none');
    }
}




//Editar reservas menú administrador
function activarEditarMisReservas() {
    let formeditar = document.getElementById("formReserva");
    if(!formeditar) return;

    //EDITAR
    formeditar.addEventListener("submit", function (e) {
        e.preventDefault();
        let reserva=obtenerDatosReserva();
        if(!reserva.id||!reserva.fechacreacion||!reserva.inicio||!reserva.fin||!reserva.tipo||!reserva.id_recurso||!reserva.grupo||!reserva.profesor||!reserva.usuario){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );

        modificarReserva(reserva.id, reserva.autorizada, reserva.fechacreacion, reserva.inicio, reserva.fin, reserva.tipo, reserva.id_recurso, reserva.asignatura, reserva.grupo, reserva.profesor, reserva.usuario, reserva.usuarioautoriza, reserva.actividad, reserva.necesidades, reserva.unidades, reserva.espacio_uso, reserva.observaciones, formeditar, modal);
    });

    let btnCancelar=document.getElementById("cancelarReserva")

    //DENEGAR
    btnCancelar.addEventListener("click", function (e) {
        e.preventDefault();
        let reserva=obtenerDatosReserva(sessionStorage.getItem("id_usuario"));
        if(!reserva.id||!reserva.fechacreacion||!reserva.inicio||!reserva.fin||!reserva.tipo||!reserva.id_recurso||!reserva.grupo||!reserva.profesor||!reserva.usuario){
            mostrarToast("Error al autorizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }

        let modal = bootstrap.Modal.getInstance(
            document.getElementById("modalReserva")
        );
        
        reserva.autorizada=0;
        modificarReserva(reserva.id, reserva.autorizada, reserva.fechacreacion, reserva.inicio, reserva.fin, reserva.tipo, reserva.id_recurso, reserva.asignatura, reserva.grupo, reserva.profesor, reserva.usuario, reserva.usuarioautoriza, reserva.actividad, reserva.necesidades, reserva.unidades, reserva.espacio_uso, reserva.observaciones, formeditar, modal);
    });
}


function obtenerDatosReserva(usuarioautoriza=null){
    let id = document.getElementById("reserva_id").value;
    let autorizada = document.getElementById("reserva_autorizada").value.trim()||null;;
    if(autorizada=="Pendiente"){
        autorizada=null;
    }else if(autorizada=="Denegada"){
        autorizada=0;
    }else if(autorizada=="Autorizada"){
        autorizada=1;
    }
    let fechacreacion = anyadirFecha(document.getElementById("reserva_f_creacion").value);
    let inicio = anyadirFecha(document.getElementById("reserva_inicio").value);
    let fin = anyadirFecha(document.getElementById("reserva_fin").value);
    let tipo = document.getElementById("reserva_tipo").value;
    let id_recurso = document.getElementById("reserva_espacio_portatil").value;
    let asignatura = document.getElementById("reserva_asignatura").value.trim()||null;
    let grupo = document.getElementById("reserva_grupo").value;
    let profesor = document.getElementById("reserva_profesor").value;
    let usuario = document.getElementById("reserva_id_usuario").value;
    if(!usuarioautoriza||usuarioautoriza==null){
        usuarioautoriza = document.getElementById("reserva_id_usuario_autoriza").value;
    }
    if(autorizada!=null&&(!usuarioautoriza||usuarioautoriza==null)){
        mostrarToast("Error al actualizar los datos", 'danger');
        return;
    }
    let observaciones = document.getElementById("reserva_observaciones").value.trim()||null;
    let actividad=null;
    let necesidades=null;
    let unidades=null;
    let espacio_uso=null;
    if(tipo=="Reserva_espacio"){
        actividad = document.getElementById("reserva_actividad").value;
        necesidades = Array.from(document.getElementById("reserva_necesidades").selectedOptions).map(opt => opt.value);
        if(actividad==null||actividad.trim()==''){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }
    }else if(tipo=="Reserva_material"){
        unidades = document.getElementById("reserva_unidades").value;
        espacio_uso = document.getElementById("reserva_espacio_uso").value;
        if(unidades==null||unidades<=0||espacio_uso==null){
            mostrarToast("Error al actualizar los datos. Campos obligatorios no rellenados.", 'danger');
            return;
        }
    }else{
        mostrarToast("Error al actualizar los datos", 'danger');
        return;
    }
    return {id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones}
}



//API Editar reservas
async function modificarReserva(id, autorizada, fechacreacion, inicio, fin, tipo, id_recurso, asignatura, grupo, profesor, usuario, usuarioautoriza, actividad, necesidades, unidades, espacio_uso, observaciones, formeditar, modal){
    let fechaactual=new Date();
    let fechainicio=new Date(inicio);
    if(fechaactual>=fechainicio){
        mostrarToast("No se puede modificar una reserva pasada", 'danger');
    }else{
        if(tipo=="Reserva_espacio"||tipo=="Reserva_material"){
            let resultado=0;
            if(tipo=="Reserva_espacio"){
                resultado=await modificarReservaEspacio(id, autorizada, id_recurso, asignatura, actividad, necesidades, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones);
            }else if(tipo=="Reserva_material"&&unidades>0){
                resultado=await modificarReservaPortatil(id, autorizada, id_recurso, asignatura, unidades, espacio_uso, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones);
            }
            if(resultado!=1&&resultado!==0){
                mostrarToast("Error al actualizar la reserva", 'danger');
            }else{
                // Cerrar modal
                modal.hide();

                // Limpiar input
                formeditar.reset();

                if(resultado===0){
                    mostrarToast("No han habido cambios", 'warning');
                }else{
                    mostrarToast("Reserva actualizada correctamente", 'success');
                }

                // Recargar
                obtenerMisReservas();
            }
        }else{
            mostrarToast("Error al actualizar la reserva", 'danger');
        }
    }
}



//API Editar reservas de tipo espacio
async function modificarReservaEspacio(id, autorizada, id_recurso, asignatura, actividad, necesidades, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones){
    try{
        let arraynecesidades = (necesidades||[]).filter(valor => valor !== '').map(id => ({ id_necesidad: id }));
        document.getElementById('cargandoreservas').classList.remove('d-none');
        let res=await fetch(window.location.origin+"/API/reservaEspacio/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({
                id_espacio: id_recurso,
                autorizada: autorizada,
                asignatura: asignatura,
                actividad: actividad,
                necesidades: arraynecesidades,
                f_creacion: fechacreacion,
                inicio: inicio,
                fin: fin,
                grupo: grupo,
                profesor: profesor,
                id_usuario: usuario,
                id_usuario_autoriza: usuarioautoriza,
                id_usuario_actor: sessionStorage.getItem("id_usuario"),
                observaciones: observaciones,
                tipo: "Reserva_espacio"
            })
        })
        document.getElementById('cargandoreservas').classList.add('d-none');
        let response = await res.json();
        
        if (response.status == "success"){
            if(response.data.status=='no_changes'){
                return 0;
            }
            return 1;
        }else{
            mostrarToast("Ya hay una reserva entre esas horas", 'warning');
            return -1;
        }
    }catch(err){
        console.error("Error al actualizar la reserva:", err);
        return -1;
    }
}



//API Editar reservas de tipo portátil
async function modificarReservaPortatil(id, autorizada, id_recurso, asignatura, unidades, espacio_uso, fechacreacion, inicio, fin, grupo, profesor, usuario, usuarioautoriza, observaciones){
    try{
        document.getElementById('cargandoreservas').classList.remove('d-none');
        let res = await fetch(window.location.origin+"/API/portatiles/reservas/"+id, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({
                id_material: id_recurso,
                autorizada: autorizada,
                asignatura: asignatura,
                unidades: unidades,
                usaenespacio: espacio_uso,
                f_creacion: fechacreacion,
                inicio: inicio,
                fin: fin,
                grupo: grupo,
                profesor: profesor,
                id_usuario: usuario,
                id_usuario_autoriza: usuarioautoriza,
                id_usuario_actor: sessionStorage.getItem("id_usuario"),
                observaciones: observaciones,
                tipo: "Reserva_material"
            })
        })
        document.getElementById('cargandoreservas').classList.add('d-none');
        
        let response = await res.json();
        
        if (response.status == "success") {
            if(response.data.status=='no_changes'){
                return 0;
            }
            return 1;
        } else {
            mostrarToast("No hay suficientes portátiles disponibles entre esas horas", 'warning')
            return -1;
        }
    }catch(err){
        console.error("Error al actualizar la reserva:", err);
        return -1;
    }
}



function formatearFecha(fecha){
    if (!fecha) return null;
    let fechaFormulario = new Date(fecha);
    let anyo = fechaFormulario.getFullYear();
    let mes = String(fechaFormulario.getMonth() + 1).padStart(2, '0');
    let dia = String(fechaFormulario.getDate()).padStart(2, '0');
    let hh = String(fechaFormulario.getHours()).padStart(2, '0');
    let mm = String(fechaFormulario.getMinutes()).padStart(2, '0');
    let ss = String(fechaFormulario.getSeconds()).padStart(2, '0');

    return `${dia}/${mes}/${anyo} ${hh}:${mm}:${ss}`;
}



function anyadirFecha(fecha){
    if (!fecha) return null;
    let fechaFormulario = new Date(fecha);
    let anyo = fechaFormulario.getFullYear();
    let mes = String(fechaFormulario.getMonth() + 1).padStart(2, '0');
    let dia = String(fechaFormulario.getDate()).padStart(2, '0');
    let hh = String(fechaFormulario.getHours()).padStart(2, '0');
    let mm = String(fechaFormulario.getMinutes()).padStart(2, '0');
    let ss = String(fechaFormulario.getSeconds()).padStart(2, '0');

    return `${anyo}-${mes}-${dia} ${hh}:${mm}:${ss}`;
}

// Limpiar backdrop cuando el modal se cierra manualmente
document.addEventListener('DOMContentLoaded', function() {
    const modalReserva = document.getElementById('modalReserva');
    if (modalReserva) {
        modalReserva.addEventListener('hidden.bs.modal', function() {
            // Limpiar cualquier backdrop residual
            document.querySelectorAll('.modal-backdrop').forEach(elemento => elemento.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
});