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
    const accesibilidadDiv = document.getElementById("accesibilidad");
    const botones = accesibilidadDiv.querySelectorAll("button");

    // Cargar estado guardado al iniciar
    const savedFontSize = localStorage.getItem("fontSizeMain");
    if(savedFontSize) main.style.fontSize = savedFontSize;

    const highContrast = localStorage.getItem("highContrast") === "true";
    if(highContrast) body.classList.add("bg-dark", "text-white");
    if(highContrast) main.classList.add("bg-dark", "text-white");

    const btnAccesibilidad = document.getElementById("btnaccesibilidad");

    function abrirAccesibilidad() {
        accesibilidadDiv.classList.add("abierto");
        botones.forEach(btn => btn.style.display = "block");
    }

    function cerrarAccesibilidad() {
        accesibilidadDiv.classList.remove("abierto");

        botones.forEach((btn, i) => {
            if (i > 0) btn.style.display = "none";
        });
    }

    btnAccesibilidad.addEventListener("click", () => {
        const abierto = accesibilidadDiv.classList.contains("abierto");
        if (!abierto) {
            abrirAccesibilidad();
        }else{
            cerrarAccesibilidad();
        }
    });

    btnAccesibilidad.addEventListener("keydown", (e) => {
        if(e.key === "Enter" || e.key === " "){
            e.preventDefault();
            const abierto = accesibilidadDiv.classList.contains("abierto");
            if (!abierto) {
                abrirAccesibilidad();
            }else{
                cerrarAccesibilidad();
            }
        }
    });

    const restablecerBtn = document.getElementById("restablecer");

    function restablecer(){
        // Resetear estilos en el DOM
        main.style.fontSize = "1em";
        body.classList.remove("bg-dark", "text-white");
        main.classList.remove("bg-dark", "text-white");
        localStorage.removeItem("fontSizeMain");
        localStorage.removeItem("highContrast");
    }

    restablecerBtn.addEventListener("click", () => {
        restablecer();
    });


    restablecerBtn.addEventListener("keydown", (e) => {
        if(e.key === "Enter" || e.key === " "){
            restablecer();
        }
    });

    // Botón aumentar letra
    const aumentarBtn = document.getElementById("masletra");
    if(aumentarBtn){
        aumentarBtn.addEventListener("click", () => {
            const style = window.getComputedStyle(main);
            const currentPx = parseFloat(style.fontSize); // tamaño en px
            const basePx = parseFloat(window.getComputedStyle(body).fontSize); // 1em = tamaño body
            const currentEm = currentPx / basePx; // convertimos a em
            if(currentEm<1.5){ // límite máximo 2em
                const newEm = currentEm + 0.1; // aumentamos 0.1em
                main.style.fontSize = newEm + "em";
                localStorage.setItem("fontSizeMain", main.style.fontSize);
            }
        });
    }

    // Botón disminuir letra
    const disminuirBtn = document.getElementById("menosletra");
    if(disminuirBtn){
        disminuirBtn.addEventListener("click", () => {
            const style = window.getComputedStyle(main);
            const currentPx = parseFloat(style.fontSize); // tamaño en px
            const basePx = parseFloat(window.getComputedStyle(body).fontSize); // 1em = tamaño body
            const currentEm = currentPx / basePx; // convertimos a em
            if(currentEm>0.75){ // límite mínimo 0.5em
                const newEm = currentEm - 0.1; // disminuimos 0.1em
                main.style.fontSize = newEm + "em";
                localStorage.setItem("fontSizeMain", main.style.fontSize);
            }
        });
    }

    // Botón alto contraste
    const contrasteBtn = document.getElementById("altocontraste");
    if(contrasteBtn){
        contrasteBtn.addEventListener("click", () => aplicarAltoContraste());
    }

    // Cerrar al perder foco del div
    accesibilidadDiv.addEventListener("focusout", (e) => {
        if (!accesibilidadDiv.contains(e.relatedTarget)) {
            const botonesVisibles = Array.from(accesibilidadDiv.querySelectorAll("button")).some((btn,i) => i>0 && btn.style.display === "block");
            if (botonesVisibles && accesibilidadDiv.classList.contains("abierto")) {
                cerrarAccesibilidad();
            }
        }
    });
}

function aplicarAltoContraste(aplicar = false) {
    const body = document.body;
    const main = document.querySelector("main");
    const botones = document.querySelectorAll(".btn-outline-secondary");
    const botoneslight = document.querySelectorAll(".btn-outline-light");
    let highContrast = localStorage.getItem("highContrast") === "true";

    if(aplicar){
        // Aplicamos la clase según el valor guardado
        if(highContrast){
            body.classList.add("bg-dark", "text-white");
            main.classList.add("bg-dark", "text-white");
            botones.forEach(btn => {
                btn.classList.remove("btn-outline-secondary");
                btn.classList.add("btn-outline-light");
            });
        } else {
            body.classList.remove("bg-dark", "text-white");
            main.classList.remove("bg-dark", "text-white");
            botoneslight.forEach(btn => {
                btn.classList.remove("btn-outline-light");
                btn.classList.add("btn-outline-secondary");
            });
        }
        return;
    }

    if(highContrast){
        body.classList.remove("bg-dark", "text-white");
        main.classList.remove("bg-dark", "text-white");
        botoneslight.forEach(btn => {
            btn.classList.remove("btn-outline-light");
            btn.classList.add("btn-outline-secondary");
        });
        localStorage.setItem("highContrast", "false");
    } else {
        body.classList.add("bg-dark", "text-white");
        main.classList.add("bg-dark", "text-white");
        botones.forEach(btn => {
            btn.classList.remove("btn-outline-secondary");
            btn.classList.add("btn-outline-light");
        });
        localStorage.setItem("highContrast", "true");
    }
}