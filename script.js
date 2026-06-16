// Esperar a que toda la página web esté cargada
document.addEventListener("DOMContentLoaded", () => {
// 1. SIMULACIÓN DE ACCIONES RÁPIDAS (BOTONES PRINCIPALES)

// Usamos un selector específico para que SOLO afecte al botón de ventas si está en el dashboard o ventas
// Así el botón de "Registrar Producto" queda completamente libre
const btnNuevaVenta = document.querySelector(".dashboard-container .sales-icon, #btn-nueva-venta");

if (btnNuevaVenta) {
    btnNuevaVenta.addEventListener("click", (e) => {
        e.preventDefault(); // Evita comportamientos raros
        let producto = prompt("Ingrese el nombre o código del producto:");
        if (producto) {
            let cantidad = prompt("Ingrese la cantidad:");
            if (cantidad && !isNaN(cantidad)) {
                alert(`🛒 Producto añadido correctamente:\n- Artículo: ${producto}\n- Cantidad: ${cantidad}\n\nLógica de 'DetalleVenta' procesada del sistema.`);
            } else {
                alert("❌ Cantidad no válida.");
            }
        }
    });
}


    // 2. SIMULACIÓN DE NAVEGACIÓN DE LA BARRA LATERAL (SIDEBAR)
    const linksMenu = document.querySelectorAll(".sidebar-menu a");
    
    linksMenu.forEach(link => {
        link.addEventListener("click", (evento) => {
            // Evitar que la página se recargue
            evento.preventDefault();
            
            // Quitar la clase activa del botón anterior y ponérsela al que se le dio clic
            document.querySelector(".sidebar-menu a.active").classList.remove("active");
            link.classList.add("active");
            
            // Obtener el nombre del módulo del texto del botón
            const nombreModulo = link.textContent.trim();
            
            // Cambiar el título del Panel Principal dinámicamente
            document.querySelector(".header-title h1").textContent = `Módulo: ${nombreModulo}`;
            document.querySelector(".header-title p").textContent = `Accediendo de forma segura a la gestión de ${nombreModulo.toLowerCase()}.`;
        });
    });

    // 3. INTERACCIÓN CON LA ALERTA DE STOCK (REQUERIMIENTO DEL SISTEMA)
    const tarjetaAlerta = document.querySelectorAll(".card")[1]; // La segunda tarjeta es la de stock
    if (tarjetaAlerta) {
        tarjetaAlerta.style.cursor = "pointer";
        tarjetaAlerta.addEventListener("click", () => {
            alert("⚠️ Alerta del Sistema de Inventario:\nLos siguientes productos están por debajo del stock mínimo:\n1. Arroz Diana 1kg (2 unidades)\n2. Aceite Gourmet 1L (1 unidad)\n\nSe requiere orden de compra a proveedores.");
        });
    }
});
// --- CÓDIGO NUEVO PARA LOS BOTONES DE LA BARRA LATERAL ---
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Alerta interactiva para el botón de Proveedores
    const btnProveedores = document.getElementById("btn-proveedores");
    if (btnProveedores) {
        btnProveedores.addEventListener("click", function(e) {
            e.preventDefault(); // Evita que la página se recargue sola
            alert("🚛 Módulo de Proveedores:\nEste módulo estará disponible en la Fase 2 del proyecto con el modelo relacional extendido.");
        });
    }

    // 2. Alerta interactiva para el botón de Reportes
    const btnReportes = document.getElementById("btn-reportes");
    if (btnReportes) {
        btnReportes.addEventListener("click", function(e) {
            e.preventDefault(); // Evita que la página se recargue sola
            alert("📄 Generador de Reportes:\nCompilando historial de ventas desde la tabla 'Venta' de la base de datos para exportar a PDF...");
        });
    }
});
