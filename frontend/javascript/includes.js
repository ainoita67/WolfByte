function cargarScript(src, callback) {
    const script = document.createElement('script');
    script.src = src;
    script.onload = callback;
    script.onerror = () => console.error("Error cargando script:", src);
    document.head.appendChild(script);
}

function cargarCSS(href) {
    const link = document.createElement('link');
    link.rel = "stylesheet";
    link.href = href;
    document.head.appendChild(link);
}

function cargarHeadHTML(url, callback) {
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const temp = document.createElement('div');
            temp.innerHTML = html;

            // Insertar solo meta y link
            temp.querySelectorAll('meta, link, title').forEach(el => document.head.appendChild(el));

            // Para scripts, los añadimos por separado
            temp.querySelectorAll('script').forEach(s => {
                if(s.src) {
                    // Si el src ya es una URL absoluta (http, https) no concatenamos BASE
                    const src = s.src.startsWith('http') ? s.src : BASE + s.getAttribute('src');
                    cargarScript(src);
                }
                else {
                    eval(s.textContent);
                }
            });

            if(callback) callback();
        })
        .catch(e => console.error("Error cargando head", e));
}

function cargarHTML(pagina, selector, callback) {
    const contenedor = document.querySelector(selector);

    if (!contenedor) return;

    fetch(pagina)
        .then(response => response.text())
        .then(html => {
            contenedor.innerHTML = html;
            if (callback) callback();
        })
        .catch(e => console.error("Error cargando", pagina, e));
}

// includes.js
function generarPagina(menu, rol){
    cargarHeadHTML(BASE + "/includes/head.html", () => {
        const linkCSS = document.createElement('link');
        linkCSS.rel = 'stylesheet';
        linkCSS.href = BASE + '/assets/css/style.css';
        document.head.appendChild(linkCSS);
        linkCSS.onload = () => {
            aplicarAltoContraste(true);
            // Aquí el CSS ya está listo, aplicamos alto contraste si corresponde
            cargarHTML(BASE + "/includes/header.html", "#header", () => {
                generateHeaderNav(menu, rol);
                botonesAccesibilidad();
            });
        };
        cargarHTML(BASE + "/includes/footer.html", "#footer");
    });
}

function botonesAccesibilidad() {
    const body = document.body;
    const main = document.querySelector("main");

    // Cargar estado guardado al iniciar
    const savedFontSize = localStorage.getItem("fontSize");
    if(savedFontSize) main.style.fontSize = savedFontSize;

    const highContrast = localStorage.getItem("highContrast") === "true";
    if(highContrast) body.classList.add("bg-dark", "text-white");
    if(highContrast) main.classList.add("bg-dark", "text-white");

    const resetearBtn = document.querySelector("#accesibilidad button:nth-child(2)");
    if(resetearBtn){
        resetearBtn.addEventListener("click", () => {
            localStorage.removeItem("fontSizeMain");
            localStorage.removeItem("highContrast");

            // Reseteamos estilos en el DOM
            main.style.fontSize = "1em"; // tamaño por defecto
            body.classList.remove("bg-dark", "text-white");
            main.classList.remove("bg-dark", "text-white");
            toggleBotones();
        });
    }

    // Botón aumentar letra
    const aumentarBtn = document.querySelector("#accesibilidad button:nth-child(3)");
    if(aumentarBtn){
        aumentarBtn.addEventListener("click", () => {
            const style = window.getComputedStyle(main);
            const currentPx = parseFloat(style.fontSize); // tamaño en px
            const basePx = parseFloat(window.getComputedStyle(body).fontSize); // 1em = tamaño body
            const currentEm = currentPx / basePx; // convertimos a em
            const newEm = currentEm + 0.1; // aumentamos 0.1em
            main.style.fontSize = newEm + "em";
            localStorage.setItem("fontSizeMain", main.style.fontSize);
        });
    }

    // if(aumentarBtn){
    //     aumentarBtn.addEventListener("click", () => {
    //         let currentSize = window.getComputedStyle(main).fontSize;
    //         currentSize = parseFloat(currentSize) + 0.2; // aumentar 2em
    //         main.style.fontSize = currentSize + "em";
    //         localStorage.setItem("fontSize", main.style.fontSize);
    //     });
    // }

    // Botón disminuir letra
    const disminuirBtn = document.querySelector("#accesibilidad button:nth-child(4)");
    if(disminuirBtn){
        disminuirBtn.addEventListener("click", () => {
            const style = window.getComputedStyle(main);
            const currentPx = parseFloat(style.fontSize); // tamaño en px
            const basePx = parseFloat(window.getComputedStyle(body).fontSize); // 1em = tamaño body
            const currentEm = currentPx / basePx; // convertimos a em
            const newEm = currentEm - 0.1; // disminuimos 0.1em
            main.style.fontSize = newEm + "em";
            localStorage.setItem("fontSizeMain", main.style.fontSize);
        });
    }

    // if(disminuirBtn){
    //     disminuirBtn.addEventListener("click", () => {
    //         let currentSize = window.getComputedStyle(main).fontSize;
    //         currentSize = parseFloat(currentSize) - 0.2; // disminuir 2em
    //         main.style.fontSize = currentSize + "em";
    //         localStorage.setItem("fontSize", main.style.fontSize);
    //     });
    // }

    // Botón alto contraste
    const contrasteBtn = document.querySelector("#accesibilidad button:nth-child(5)");
    if(contrasteBtn){
        contrasteBtn.addEventListener("click", () => aplicarAltoContraste());
    }

    const accesibilidadDiv = document.getElementById("accesibilidad");
    const primerBoton = accesibilidadDiv.querySelector("button:nth-child(1)");
    const todosBotones = accesibilidadDiv.querySelectorAll("button");

    // Función para desplegar o cerrar
    function toggleBotones() {
        const botones = Array.from(todosBotones).slice(1); // todos menos el primero
        const estanOcultos = botones.some(btn => btn.style.display === "none" || btn.style.display === "");

        if(estanOcultos){
            // Mostrar todos excepto el primero
            botones.forEach(btn => btn.style.display = "block");
            primerBoton.style.display = "none"; // ocultamos el primer botón
        } else {
            // Ocultar todos excepto el primero
            botones.forEach(btn => btn.style.display = "none");
            primerBoton.style.display = "block"; // mostramos el primer botón de nuevo
        }
    }


    // Abrir o cerrar con Enter en el div
    accesibilidadDiv.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            toggleBotones();
            e.preventDefault(); // Evita que haga scroll
        }
    });

    // Cerrar al hacer click fuera
    document.addEventListener("click", (e) => {
        if (!accesibilidadDiv.contains(e.target)) {
            // Si alguno de los botones está desplegado
            const estanDesplegados = Array.from(todosBotones).some((btn, i) => i > 0 && btn.style.display === "block");
            if (estanDesplegados) toggleBotones();
        }
    });

    // Cerrar al perder foco del div
    accesibilidadDiv.addEventListener("focusout", (e) => {
        if (!accesibilidadDiv.contains(e.relatedTarget)) {
            const estanDesplegados = Array.from(todosBotones).some((btn, i) => i > 0 && btn.style.display === "block");
            if (estanDesplegados) toggleBotones();
        }
    });

    primerBoton.addEventListener("click", toggleBotones);

    primerBoton.addEventListener("keydown", (e) => {
        if(e.key === "Enter" || e.key === " "){ // también barra espaciadora
            toggleBotones();
            e.preventDefault();
        }
    });
}

function aplicarAltoContraste(aplicar = false) {
    const body = document.body;
    const main = document.querySelector("main");
    let highContrast = localStorage.getItem("highContrast") === "true";

    if(aplicar){
        // Aplicamos la clase según el valor guardado
        if(highContrast){
            body.classList.add("bg-dark", "text-white");
            main.classList.add("bg-dark", "text-white");
        } else {
            body.classList.remove("bg-dark", "text-white");
            main.classList.remove("bg-dark", "text-white");
        }
        return;
    }

    if(highContrast){
        body.classList.remove("bg-dark", "text-white");
        main.classList.remove("bg-dark", "text-white");
        localStorage.setItem("highContrast", "false");
    } else {
        body.classList.add("bg-dark", "text-white");
        main.classList.add("bg-dark", "text-white");
        localStorage.setItem("highContrast", "true");
    }
}