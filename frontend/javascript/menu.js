/**
 * Genera el header y nav dinámicamente según el JWT
 * @param {string} menuactivo - Nombre de la página actual (ej: 'aulas', 'salonactos')
 */
function generateHeaderNav(menuactivo) {
    const header = document.getElementById('header');

    // Crear navs para escritorio y móvil
    const navd = document.createElement('nav');
    navd.classList.add("row");
    navd.id = "menuordenador";

    const navm = document.createElement('nav');
    navm.classList.add("row");
    navm.id = "menumovil";

    header.appendChild(navd);
    header.appendChild(navm);

    //si no hay usuario cargar menus vacios
    if (!role){
        navd.classList.add("row", "d-none", "d-lg-grid");
        navd.innerHTML = 
            `<ul class="col-7 d-none d-lg-grid text-center fs-5 pt-3 pe-1">
                <li class="col-12 ps-2 pe-5">
                    <a href="#"><img src="${BASE}/assets/img/ieslogo.png" alt="Logo"></a>
                </li>
                <li></li>
            </ul>
            `;
        
        navm.classList.add("row", "mt-0", "d-lg-none");
        navm.innerHTML = 
            `<ul class="col-7 d-grid d-lg-none text-center fs-5 pt-2">
                <li class="col-12 ps-2 pe-5">
                    <a href="#"><img src="${BASE}/assets/img/ieslogo.png" alt="Logo"></a>
                </li>
                <li></li>
            </ul>
            `;
    } else {
        // si hay usuario llena los navs con los enlaces necesarios

        //apartados del menu, para reutilizarlos y bucles
        const menus = [
            { texto: "Aulas", href: BASE + "/vistas/reservas/aulas/aulas.html", key: "aulas" },
            { texto: "Salón de actos", href: BASE + "/vistas/reservas/salondeactos/salondeactos.html", key: "salonactos" },
            { texto: "Portátiles", href: BASE + "/vistas/reservas/portatiles/portatiles.html", key: "portatiles" },
            { texto: "Otros espacios", href: BASE + "/vistas/reservas/espacios/espacios.html", key: "espacios" },
            { texto: "Incidencias", href: BASE + "/vistas/incidencias/incidencias.html", key: "incidencias" },
            { texto: "Liberar aulas", href: BASE + "/vistas/liberar/liberar.html", key: "liberar" }
        ];

        //MENU DESKTOP
        // crear ul y logo
        const uld = document.createElement('ul');
            uld.id = "menudesktop"
            uld.classList.add("col-12", "d-none", "d-lg-grid", "text-center", "fs-5", "pt-3");
            if(role=="admin"){
                uld.innerHTML = 
                `<li class="col-12">
                    <a href="${BASE}/vistas/menu.html">
                        <img src="${BASE}/assets/img/ieslogo.png" alt="Logo">
                    </a>
                </li>
                `;
            }else{
                uld.innerHTML = 
                `<li class="col-12">
                    <a href="${BASE}/vistas/menu.html">
                        <img src="${BASE}/assets/img/ieslogo.png" alt="Logo">
                    </a>
                </li>
                <li class="col-1"></li>
                `;
            }
        navd.appendChild(uld);

        // apartados menu desktop
        menus.forEach(menu => {
            const li = document.createElement('li');
            li.classList.add("pt-5", "pb-5", "d-none", "d-lg-block", "ms-5");

    // ----------------- DESKTOP -----------------
    const uld = document.createElement('ul');
    uld.id = "menudesktop";
    uld.classList.add("col-12", "d-none", "d-xl-grid", "text-center", "fs-5", "pt-3");

    uld.innerHTML = `
    <li class="col-12">
        <a href="${BASE}/vistas/menu.php">
            <img src="${BASE}/assets/img/ieslogo.png" alt="Logo">
        </a>
    </li>`;

    // Añadir links
    menus.forEach(menu => {
        const li = document.createElement('li');
        li.classList.add("pt-5", "pb-5", "d-none", "d-xl-block", "ms-5");
        const a = document.createElement('a');
        a.href = menu.href;
        a.textContent = menu.texto;
        if(menuactivo === menu.key){
            a.classList.add("fw-bold", "text-lightgrey");
            a.style.color = "grey";
        }
        li.appendChild(a);
        uld.appendChild(li);
    });

        // administrador
        if (role == "admin"){
            const liadmin = document.createElement('li');
            liadmin.classList.add("pt-4", "pb-4", "d-none", "d-lg-block", "ms-3");

            const aa = document.createElement('a');
            aa.href = "/frontend/vistas/administrador/menuadministrador.html";
            aa.textContent = "Administrador";

            if(menuactivo === "admin"){
                aa.classList.add("fw-bold", "text-lightgrey");
                aa.style.color = "grey";
            }

            liadmin.appendChild(aa);
            uld.appendChild(liadmin);
        }
        liadmin.appendChild(aa);
        uld.appendChild(liadmin);
    }

        // apartado perfil
        const lipd = document.createElement('li');
        uld.appendChild(lipd);
        lipd.classList.add("list-group-item", "pt-5", "pb-5", "d-none", "d-lg-block");
        lipd.id = "perfil";
        lipd.innerHTML = 
            `<a href="#" id="perfildesktop" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#perfildesktop" id="ulpd">
                <li>
                    <a href="${BASE}/vistas/perfil/datos.html" class="dropdown-item">Mis datos</a>
                </li>
                <li>
                    <a href="${BASE}/vistas/perfil/misreservas.html" class="dropdown-item">Mis reservas</a>
                </li>
                <li>
                    <a href="${BASE}/vistas/perfil/misincidencias.html" class="dropdown-item">Mis incidencias</a>
                </li>
                <li>
                    <a href="${BASE}/auth/logout.html" class="dropdown-item">Cerrar sesión</a>
                </li>
            </ul>
            `;

        // MENU MOVIL
        // Crear UL principal
        const ulm = document.createElement('ul');
        ulm.classList.add("col-12", "d-flex", "d-lg-none", "text-center", "fs-5", "pt-3", "ps-4");
        // logo
        ulm.innerHTML = 
            `<li class="col-2">
                <a href="${BASE}/vistas/menu.html">
                    <img src="${BASE}/assets/img/ieslogo.png" alt="Logo">
                </a>
            </li>
            <li class="offset-6 offset-sm-7"></li>
            `;
        navm.appendChild(ulm);

        // Menú hamburguesa
        const liMenu = document.createElement('li');
            liMenu.classList.add("mx-3", "list-group-item", "pt-5", "pb-5", "d-lg-none", "ms-5");
            liMenu.id = "perfil";

    const aMenu = document.createElement('a');
    aMenu.href = "#";
    aMenu.id = "menudesplegable";
    aMenu.setAttribute("data-bs-toggle", "dropdown");
    aMenu.setAttribute("aria-expanded", "false");
    aMenu.innerHTML = `<i class="bi bi-list fs-1"></i>`;
    liMenu.appendChild(aMenu);

    const dropdown = document.createElement('ul');
    dropdown.classList.add("dropdown-menu");
    dropdown.setAttribute("aria-labelledby", "menudesplegable");

    menus.forEach(menu => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = menu.href;
        a.textContent = menu.texto;
        a.classList.add("dropdown-item");
        li.appendChild(a);
        dropdown.appendChild(li);
    });

        liMenu.appendChild(dropdown);
        ulm.appendChild(liMenu);

        // apartado perfil
        const lipm = document.createElement('li');
        ulm.appendChild(lipm);
        lipm.classList.add("list-group-item", "pt-5", "pb-5", "d-lg-none");
        lipm.id = "perfil";
        lipm.innerHTML = 
        `<a href="#" id="perfilmovil" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-1"></i>
        </a>
        <ul class="dropdown-menu" data-target="#perfilmovil">
            <li>
                <a href="${BASE}/vistas/perfil/datos.html" class="dropdown-item">Mis datos</a>
            </li>
            <li>
                <a href="${BASE}/vistas/perfil/misreservas.html" class="dropdown-item">Mis reservas</a>
            </li>
            <li>
                <a href="${BASE}/vistas/perfil/misincidencias.html" class="dropdown-item">Mis incidencias</a>
            </li>
            <li>
                <a href="${BASE}/auth/logout.html" class="dropdown-item">Cerrar sesión</a>
            </li>
        </ul>
        `;
    }

    liMenu.appendChild(dropdown);
    ulm.appendChild(liMenu);

    // Perfil móvil
    const lipm = document.createElement('li');
    lipm.classList.add("list-group-item", "pt-5", "pb-5", "d-xl-none");
    lipm.id = "perfil";
    lipm.innerHTML = `
    <a href="#" id="perfilmovil" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-1"></i>
        <span class="ms-2">${userName}</span>
    </a>
    <ul class="dropdown-menu" data-target="#perfilmovil">
        <li><a href="${BASE}/vistas/perfil/datos.html" class="dropdown-item">${userName}</a></li>
        <li><a href="${BASE}/vistas/perfil/misreservas.html" class="dropdown-item">Mis reservas</a></li>
        <li><a href="${BASE}/vistas/perfil/misincidencias.html" class="dropdown-item">Mis incidencias</a></li>
        <li><a href="#" id="logoutBtnMobile" class="dropdown-item">Cerrar sesión</a></li>
    </ul>`;
    ulm.appendChild(lipm);

    // Agregar nav móvil al DOM
    navm.appendChild(ulm);

    // --- Setup logout ---
    setupLogout();
}

// Logout genérico para desktop y móvil
function setupLogout() {
    ['logoutBtnDesktop', 'logoutBtnMobile'].forEach(id => {
        const btn = document.getElementById(id);
        if(btn){
            btn.addEventListener('click', async (e)=>{
                e.preventDefault();
                localStorage.removeItem('token');
                window.location.href = '/frontend/auth/login.html';
            });
        }
    });
}
