/**
 * Genera el header y nav dinámicamente según el rol y la página actual
 * @param {string} menuactivo - Nombre de la página actual (ej: 'aulas', 'salonactos')
 * @param {string} role - Rol del usuario (ej: 'admin', 'usuario')
 */
function generateHeaderNav(menuactivo, role) {
    const header = document.getElementsByTagName('header')[0];
    
    //crear los 2 navs vacios ordenador y movil
    const navd = document.createElement('nav');
        navd.classList.add("row");
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
                    <a href="#"><img src="/public/assets/imagenes/ieslogo.png" alt="Logo"></a>
                </li>
                <li></li>
            </ul>
            `;
        
        navm.classList.add("row", "mt-0", "d-lg-none");
        navm.innerHTML = 
            `<ul class="col-7 d-grid d-lg-none text-center fs-5 pt-2">
                <li class="col-12 ps-2 pe-5">
                    <a href="#"><img src="/public/assets/imagenes/ieslogo.png" alt="Logo"></a>
                </li>
                <li></li>
            </ul>
            `;
    } else {
        // si hay usuario llena los navs con los enlaces necesarios

        //apartados del menu, para reutilizarlos y bucles
        const menus = [
            { texto: "Aulas", href: "/public/views/reservas/aulas/aulas.php", key: "aulas" },
            { texto: "Salón de Actos", href: "/public/views/reservas/salondeactos/salondeactos.php", key: "salonactos" },
            { texto: "Material", href: "/public/views/reservas/materiales/materiales.php", key: "material" },
            { texto: "Otros espacios", href: "/public/views/reservas/otros/otros.php", key: "espacios" },
            { texto: "Incidencias", href: "/public/views/incidencias/incidencias.php", key: "incidencias" },
            { texto: "Liberar aulas", href: "/public/views/liberar/liberar.php", key: "liberar" }
        ];

        //MENU DESKTOP
        // crear ul y logo
        const uld = document.createElement('ul');
            uld.classList.add("col-12", "d-none", "d-lg-grid", "text-center", "fs-5", "pt-3");
            uld.id = "menudesktop"
            uld.innerHTML = 
            `<li class="col-12">
                <a href="/public/views/menu.php">
                    <img src="/public/assets/imagenes/ieslogo.png" alt="Logo">
                </a>
            </li>
            `;
        navd.appendChild(uld);

        // apartados menu desktop
        menus.forEach(menu => {
            const li = document.createElement('li');
            li.classList.add("pt-4", "pb-4", "d-none", "d-lg-block", "ms-3");

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
            aa.href = "/public/views/administrador/menuadministrador.php";
            aa.textContent = "Administrador";

            if(menuactivo === "admin"){
                aa.classList.add("fw-bold", "text-lightgrey");
                aa.style.color = "grey";
            }

            liadmin.appendChild(aa);
            uld.appendChild(liadmin);
        }
        

        // apartado perfil
        const lipd = document.createElement('li');
        uld.appendChild(lipd);
        lipd.classList.add("list-group-item", "pt-4", "pb-4", "d-none", "d-lg-block");
        lipd.id = "perfil";
        lipd.innerHTML = 
            `<a href="#" id="perfildesktop" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-1"></i>
            </a>
            <ul class="dropdown-menu" data-target="#perfildesktop" id="ulpd">
                <li>
                    <a href="/perfil/datos.php" class="dropdown-item">Mis datos</a>
                </li>
                <li>
                    <a href="/perfil/reserva.php" class="dropdown-item"> Mis reservas</a>
                </li>
                <li>
                    <a href="/perfil/incidencias.php" class="dropdown-item"> Mis incidencias</a>
                </li>
                <li>
                    <button id="logoutBtn" class="dropdown-item btn btn-link p-0">Cerrar sesión</button>
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
                <a href="/public/views/menu.php">
                    <img src="/public/assets/imagenes/ieslogo.png" alt="Logo">
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
                //administrador
                if (role == "admin"){
                    const liad = document.createElement('li');
                        const aad = document.createElement('a');
                            aad.href = "/public/views/administrador/menuadministrador.php";
                            aad.textContent = "Administrador";
                            aad.classList.add("dropdown-item");
                            liad.appendChild(aad);
                    dropdown.appendChild(liad);
                }

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
                    <a href="/perfil/datos.php" class="dropdown-item">Mis datos</a>
                </li>
                <li>
                    <a href="/perfil/reserva.php" class="dropdown-item"> Mis reservas</a>
                </li>
                <li>
                    <a href="/perfil/incidencias.php" class="dropdown-item"> Mis incidencias</a>
                </li>
                <li>
                    <a href="/auth/logout.php" class="dropdown-item"> Cerrar sesión</a>
                </li>
            </ul>
            `;
        }

}

// Logout
document.addEventListener('click', async (e) => {
    if (e.target && e.target.id === 'logoutBtn') {
        e.preventDefault();
        try {
            const response = await fetch('http://192.168.13.202/API/public/logout', {
                method: 'POST',           // tu API acepta POST
                credentials: 'include',   // 🔑 permite enviar cookies de sesión
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cerrar sesión');
            }

            console.log('Sesión cerrada:', data);
            window.location.href = '/auth/login.php';  // Redirige al login

        } catch (err) {
            alert('Error al cerrar sesión: ' + err.message);
        }
    }
});
