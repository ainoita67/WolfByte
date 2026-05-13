//API Obtener edificios para crear liberación
function obtenerEdificios(){
    fetch(window.location.origin+"/API/edificios", {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
    .then(res => res.json())
    .then(response => {
        let edificios = response.data;

        let selectedificios = document.getElementById("selectedificio");
        selectedificios.innerHTML = "";
        if(edificios.length === 0){
            let option = document.createElement("option");
            option.value = "";
            option.textContent = "No hay edificios registrados";
            option.selected = true;
            option.disabled = true;
            selectedificios.appendChild(option);
        }else{
            let optionseleccionar = document.createElement("option");
            optionseleccionar.value = "";
            optionseleccionar.textContent = "Seleccione un edificio";
            optionseleccionar.selected = true;
            optionseleccionar.disabled = true;
            selectedificios.appendChild(optionseleccionar);

            edificios.forEach(edificio => {
                let optionedificio = document.createElement("option");
                optionedificio.value = edificio.id_edificio;
                optionedificio.textContent = edificio.nombre_edificio;
                selectedificios.appendChild(optionedificio);
            });
        }
    })
    .catch(error => console.error("<p>Error al obtener edificios</p>", error));
}



//API Obtener plantas para crear liberación
function obtenerPlantas(edificio){
    fetch(window.location.origin+"/API/plantas/"+edificio, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
    .then(res => res.json())
    .then(response => {
        let plantas = response.data;
        let divplantas = document.getElementById("divplanta");
        let selectplantas = document.getElementById("selectplanta");
        selectplantas.innerHTML = "";

        divplantas.classList.remove("d-none");
        divplantas.classList.add("d-block");
        
        if(!plantas||plantas.length === 0||edificio == ""||!edificio){
            let optionplanta = document.createElement("option");
            optionplanta.value = "";
            optionplanta.disabled = true;
            optionplanta.selected = true;
            optionplanta.textContent = "No se han encontrado plantas para este edificio";
            selectplantas.appendChild(optionplanta);
        }else{
            let optionseleccionar = document.createElement("option");
            optionseleccionar.value = "";
            optionseleccionar.textContent = "Seleccionar planta";
            optionseleccionar.selected = true;
            optionseleccionar.disabled = true;
            selectplantas.appendChild(optionseleccionar);

            plantas.forEach(planta => {
                let optionplanta = document.createElement("option");
                optionplanta.value = planta.numero_planta;
                optionplanta.textContent = 'Planta '+planta.numero_planta;
                selectplantas.appendChild(optionplanta);
            });
        }
    })
    .catch(error => console.error("<p>Error al obtener plantas</p>", error));
}



//API Obtener espacios para crear liberación
function obtenerAulas(edificio=-100, planta=-100){
    if(!edificio||edificio<=0){
        return obtenerEdificios();
    }else if(!planta||planta<-10){
        return obtenerPlantas(edificio);
    }else{
        fetch(window.location.origin+"/API/espacios/", {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        .then(res => res.json())
        .then(response => {
            let aulas = response.data;

            let divaulas = document.getElementById("divaula");
            let selectaulas = document.getElementById("selectaula");
            selectaulas.innerHTML = "";
            
            let optionseleccionar = document.createElement("option");
            optionseleccionar.value = "";
            optionseleccionar.textContent = "Seleccionar aula";
            optionseleccionar.selected = true;
            optionseleccionar.disabled = true;
            selectaulas.appendChild(optionseleccionar);

            let naulas=0;
            aulas.forEach(aula => {
                if(aula.es_aula==1&&aula.id_edificio==edificio&&aula.numero_planta==planta){
                    let optionaula = document.createElement("option");
                    optionaula.value = aula.id_recurso;
                    optionaula.textContent = aula.id_recurso+' - '+aula.descripcion;
                    selectaulas.appendChild(optionaula);

                    naulas++;
                }
            });

            divaulas.classList.remove("d-none");
            divaulas.classList.add("d-block");
            
            if(!aulas||aulas.length==0||naulas==0||edificio == ""||!edificio){
                selectaulas.innerHTML = "";
                let optionaula = document.createElement("option");
                optionaula.value = "";
                optionaula.disabled = true;
                optionaula.selected = true;
                optionaula.textContent = "No se han encontrado aulas para este edificio y planta";
                selectaulas.appendChild(optionaula);
            }
        }).catch(error => console.error("<p>Error al obtener aulas por edificio</p>", error));
    }
}


function obtenerFechas(edificio=-100, planta=-100, aula=-100){
    let divfechas = document.querySelectorAll(".divfechahora");
    if(!edificio||edificio<=0||!planta||planta<-10||!aula||aula<=0){
        divfechas.forEach(divfecha => {
            divfecha.classList.remove("d-block");
            divfecha.classList.add("d-none");
        });

        if(!edificio||edificio<=0){
            return obtenerEdificios();
        }else if(!planta||planta<-10){
            return obtenerPlantas(edificio);
        }else if(!aula||aula<=0){
            return obtenerAulas(edificio, planta);
        }
    }else{
        divfechas.forEach(divfecha => {
            divfecha.classList.remove("d-none");
            divfecha.classList.add("d-block");
        });
    }
}