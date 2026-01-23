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
                cargarScript(s.src); // si tienen src externo
                if(!s.src) eval(s.textContent); // si es inline
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
    document.addEventListener("DOMContentLoaded", () => {
        cargarHeadHTML("/ALEX/frontend/includes/head.html", () => {
            cargarHTML("/ALEX/frontend/includes/header.php", "#header", () => {
                generateHeaderNav(menu, rol);
            });

            cargarHTML("/ALEX/frontend/includes/footer.php", "#footer");
        });
    });
}