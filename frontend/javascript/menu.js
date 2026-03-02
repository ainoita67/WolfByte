/**
 * Genera el header y nav dinámicamente según el rol y la página actual
 * @param {string} menuactivo - Nombre de la página actual (ej: 'aulas', 'salonactos')
 * @param {string} role - Rol del usuario (ej: 'admin', 'usuario')
 */
function generateHeaderNav(menuactivo, role) {
    const header = document.getElementById('header');
    
    //crear los 2 navs vacios ordenador y movil
    const nav = document.createElement('nav');
        nav.classList.add("row");
        nav.id = "menu";

    header.appendChild(nav);

    //si no hay usuario cargar menus vacios
    if (!role){
        nav.classList.add("row", "d-lg-grid");
        nav.innerHTML = 
            `<ul class="col-7 d-grid d-lg-grid text-center fs-5 pt-3 pe-1">
                <li class="col-12 ps-2 pe-5">
                    <a href="#"><img src="${BASE}/assets/img/ieslogo.png" alt="Logo"></a>
                </li>
                <li></li>
            </ul>
            `;

    } else {
        // si hay usuario llena los navs con los enlaces necesarios

        //apartados del menu, para reutilizarlos y bucles
        let menus=[
            { texto: "Aulas", href: "/vistas/reservas/aulas/aulas.html", key: "aulas" },
            { texto: "Salón de actos", href: "/vistas/reservas/salondeactos/salondeactos.html", key: "salonactos" },
            { texto: "Portátiles", href: "/vistas/reservas/portatiles/portatiles.html", key: "portatiles" },
            { texto: "Otros espacios", href: "/vistas/reservas/otrosespacios/espacios.html", key: "espacios" },
            { texto: "Incidencias", href: "/vistas/incidencias/verincidencias.html", key: "incidencias" },
            { texto: "Liberar aulas", href: "/vistas/liberar/liberar.html", key: "liberar" }
        ];
        console.log("Rol del usuario:", role); // DEBUG: Verificar el rol del usuario
        if(role>=30){ // solo para admin
            menus.push({ texto: "Administrador", href: "/vistas/administrador/menuadministrador.html", key: "administrador" });
        }

        // UL PARA TODO Y LOGO
        const ul = document.createElement('ul');
            ul.classList.add("col-12", "d-flex", "d-lg-grid", "text-center", "fs-5", "py-2", "py-lg-4", "ps-4", "ps-lg-0", "mb-0");
            ul.innerHTML = 
                `<li class="col-lg-12 col-2">
                    <a href="${BASE}/vistas/menu.html">
                        <img src="${BASE}/assets/img/ieslogo.png" alt="Logo">
                    </a>
                </li>
                <li class="d-block d-lg-none offset-6 offset-sm-7"></li>
                `;

        nav.appendChild(ul);

        // NAV DESKTOP
        menus.forEach(menu => {
            const li = document.createElement('li');
            li.classList.add("pt-5", "pb-5", "d-none", "d-lg-block", "ms-5");

            const a = document.createElement('a');
            a.href = `${BASE}${menu.href}`;
            a.textContent = menu.texto;

            if(menuactivo === menu.key){
                a.classList.add("fw-bold", "text-lightgrey");
                a.style.color = "grey";
            }

            li.appendChild(a);
            ul.appendChild(li);
        });

        // NAV MOVIL HAMBURGUESA
        const liMenu = document.createElement('li');
            liMenu.classList.add("mx-3", "list-group-item", "pt-2", "pb-2", "d-lg-none", "ms-5", "desplegablemenu");

            const aMenu = document.createElement('a');
                aMenu.href = "#";
                aMenu.id = "menudesplegable";
                aMenu.setAttribute("data-bs-toggle", "dropdown");
                aMenu.setAttribute("aria-expanded", "false");
                aMenu.innerHTML = `<i class="bi bi-list fs-1"></i>`;
            liMenu.appendChild(aMenu);

            // Crear UL del dropdown
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
        ul.appendChild(liMenu);

        // PERFIL COMUN PARA DESKTOP Y MOVIL
        const lip = document.createElement('li');
        ul.appendChild(lip);
        lip.classList.add("list-group-item", "pt-2", "pb-2", "desplegablemenu");
        lip.id = "perfil";
        lip.innerHTML = 
            `<a href="#" id="perfildesktop" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#perfil" id="ulp">
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
                    <a href="#" class="dropdown-item" id="logoutBtn">Cerrar sesión</a>
                </li>
            </ul>
            `;
}
}

