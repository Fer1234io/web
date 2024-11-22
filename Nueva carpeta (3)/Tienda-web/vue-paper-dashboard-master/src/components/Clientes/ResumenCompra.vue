<template>
  <div class="resumen-compra-container">
    <h1>Resumen de tu Compra</h1>
    <p class="tienda-nombre"><strong>Tienda:</strong> {{ tienda }}</p>
    <p><strong>Comprador:</strong> {{ comprador }}</p>

    <div class="resumen-grid">
      <!-- Tabla de productos -->
      <div class="productos-container">
        <table class="resumen-tabla">
          <thead>
            <tr>
              <th>Imagen</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Precio Unitario</th>
              <th>Precio con Descuento</th>
              <th>Cantidad</th>
              <th>Precio Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in carrito" :key="index">
              <td>
                <img :src="'data:image/jpeg;base64,' + item.foto" alt="Producto" class="tabla-img" />
              </td>
              <td>{{ item.nombre }}</td>
              <td>{{ item.categoria || "Sin categoría" }}</td>
              <td>Q{{ formatNumber(item.precioNormal) }}</td>
              <td>Q{{ item.descuento > 0 ? formatNumber(item.precioConDescuento) : "0.00" }}</td>
              <td>
                <input
                  type="number"
                  v-model.number="item.cantidad"
                  @input="actualizarTotales"
                  min="1"
                  class="cantidad-input"
                />
              </td>
              <td>Q{{ formatNumber(item.cantidad * item.precioConDescuento) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Resumen total -->
      <div class="resumen-total">
        <h2>Resumen del Pago</h2>
        <div class="resumen-detalle">
          <p><strong>Sub-total:</strong> Q{{ formatNumber(subtotalCompra) }}</p>
          <p><strong>Descuento Total:</strong> Q{{ formatNumber(totalDescuento) }}</p>
          <p class="total"><strong>Total:</strong> Q{{ formatNumber(totalCompra) }}</p>
        </div>
        <button class="confirm-btn" @click="abrirModal">Confirmar Compra</button>
        <button class="back-btn" @click="volver">Volver</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal-content modal-large">
        <h2>Detalles de Entrega y Pago</h2>

        <!-- Tabla con scroll -->
        <div class="table-container">
          <table class="modal-tabla">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in carrito" :key="index">
                <td>{{ item.nombre }}</td>
                <td>{{ item.cantidad }}</td>
                <td>Q{{ formatNumber(item.precioConDescuento) }}</td>
                <td>Q{{ formatNumber(item.cantidad * item.precioConDescuento) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

         <!-- Formulario -->
        <div class="modal-form">
          <label for="nit">Nit:</label>
          <input id="nit" type="text" v-model="nit" placeholder="Si no tiene NTI ingrese CF"/>
          
          <label for="estado">Estado:</label>
          <input id="estado" type="text" v-model="estado" readonly />

          <label for="metodoEntrega">Método de Entrega:</label>
          <select id="metodoEntrega" v-model="metodoEntrega" @change="actualizarEntrega" required>
            <option value="envio">Envío a domicilio</option>
            <option value="recoger">Recoger en tienda</option>
          </select>

          <label for="metodoPago">Método de Pago:</label>
          <select id="metodoPago" v-model="metodoPago" required>
            <option v-for="metodo in metodosPago" :key="metodo" :value="metodo">{{ metodo }}</option>
          </select>

          <p v-if="metodoEntrega === 'recoger'" class="direccion-mensaje">
            Debes recoger tu pedido en: <strong>{{ direccionTienda }}</strong>
          </p>

          <label for="direccionEnvio" v-if="metodoEntrega === 'envio'">Dirección de Envío:</label>
          <input
            id="direccionEnvio"
            v-if="metodoEntrega === 'envio'"
            type="text"
            v-model="direccionEnvio"
            placeholder="Ingrese su dirección"
          />
        </div>

        <!-- Botones -->
        <div class="modal-actions">
          <button class="close-btn" @click="cerrarModal">Cerrar</button>
          <button class="confirm-btn" @click="finalizarCompra">Finalizar Compra</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import jsPDF from "jspdf"; // Nuevo: Importamos la librería para generar PDFs.
import autoTable from "jspdf-autotable"; // (Opcional) Para generar tablas automáticamente.
import axios from "axios";

export default {
  
  data() {
    return {
      carrito: [],
      tiendas: [], // Lista de tiendas
      tienda: "", // Nombre de la tienda
      direccionTienda: "", // Dirección de la tienda seleccionada
      usuario: null,
      mostrarModal: false,
      metodoEntrega: "",
      metodoPago: "",
      metodosPago: [], // Lista dinámica de métodos de pago
      direccionEnvio: "",
      direccionTienda: "",
      tiendaSeleccionada: "1",
      estado: "pendiente",
      costoEnvio: 30, // Costo adicional por envío
      totalFinal: 0, // Total calculado con o sin envío
    };
  },
  computed: {
    comprador() {
      return this.usuario
        ? `${this.usuario.nombre} ${this.usuario.apellido} ${this.usuario.id_usuario}`
        : "Usuario desconocido";
    },
    subtotalCompra() {
      return this.carrito.reduce((total, item) => total + item.cantidad * item.precioNormal, 0);
    },
    totalDescuento() {
      return this.carrito.reduce(
        (total, item) =>
          total +
          (item.descuento > 0
            ? item.cantidad * (item.precioNormal - item.precioConDescuento)
            : 0),
        0
      );
    },
    totalCompra() {
      return this.carrito.reduce(
        (total, item) => total + item.cantidad * item.precioConDescuento,
        0
      );
    },
  },
  created() {
  const queryParams = this.$route.query.carrito;

  if (queryParams) {
    this.carrito = JSON.parse(queryParams).map((item) => ({
      ...item,
      id_inventario: item.id_inventario || item.idInventario || null,
      nombre: item.nombre || "Sin nombre",
      categoria: item.categoria || "Sin categoría",
      precioNormal: parseFloat(item.precioNormal) || 0,
      precioConDescuento:
        parseFloat(item.precioConDescuento) ||
        parseFloat(item.precioNormal) ||
        0,
      cantidad: parseInt(item.cantidad) || 1,
    }));
  }

  const idTienda = this.carrito[0]?.id_tienda;

  if (idTienda) {
    this.obtenerNombreYDireccionTienda(idTienda);
  } else {
    this.tienda = "Tienda desconocida";
    this.direccionTienda = "Dirección no disponible";
  }

  const userData = localStorage.getItem("userData");
  if (userData) {
    this.usuario = JSON.parse(userData);
  }
},
  methods: {
    async obtenerNombreYDireccionTienda(idTienda) {
    try {
      const response = await axios.get(
        `http://127.0.0.1:8000/tiendas/${idTienda}`
      );
      this.tienda = response.data.nombre_tienda || "Tienda desconocida";
      this.direccionTienda = response.data.direccion || "Dirección no disponible";
    } catch (error) {
      console.error("Error al obtener la tienda:", error);
      this.tienda = "Tienda desconocida";
      this.direccionTienda = "Dirección no disponible";
    }
  },
  actualizarEntrega() {
    if (this.metodoEntrega === "envio") {
      this.metodosPago = ["Depósito", "Pago contra entrega"];
      this.totalFinal = this.totalCompra + this.costoEnvio; // Agregar costo de envío
    } else if (this.metodoEntrega === "recoger") {
      this.metodosPago = ["Depósito", "Pagar en tienda"];
      this.totalFinal = this.totalCompra; // Sin costo adicional
    } else {
      this.metodosPago = [];
      this.totalFinal = this.totalCompra;
    }
  },
    abrirModal() {
      this.totalFinal = this.totalCompra; // Inicializar sin costos adicionales
      this.actualizarEntrega();
      this.mostrarModal = true;
    },
    cerrarModal() {
      this.mostrarModal = false;
      this.metodoEntrega = "";
      this.metodoPago = "";
      this.direccionEnvio = "";
    },
     // Este método se actualiza para cambiar la dirección de la tienda
     async actualizarMetodoEntrega() {
  if (this.metodoEntrega === "recoger") {
    // Obtiene el id_tienda del primer producto en el carrito
    const idTienda = this.carrito[0]?.id_tienda;

    if (idTienda) {
      // Busca la tienda en la lista de tiendas cargadas
      const tienda = this.tiendas.find((t) => t.id_tienda === idTienda);

      if (tienda) {
        // Actualiza la dirección de la tienda
        this.direccionTienda = tienda.direccion || "Dirección no disponible";
      } else {
        console.error("No se encontró la tienda asociada.");
        this.direccionTienda = "Tienda no encontrada.";
      }
    } else {
      console.error("No se encontró id_tienda en los productos del carrito.");
      this.direccionTienda = "Tienda no encontrada.";
    }
  } else {
    // Limpia la dirección si el método de entrega no es recoger
    this.direccionTienda = "";
  }
},
    
    async obtenerTiendaPorInventario(idInventario) {
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/tiendas/inventario/${idInventario}`
        );
        return response.data.tienda;
      } catch (error) {
        console.error("Error al obtener la tienda:", error.response.data.error);
        return null;
      }
    },
    async obtenerDireccionTienda() {
      if (!this.tiendas || this.tiendas.length === 0) {
        console.error("La lista de tiendas está vacía o no ha sido cargada.");
        this.direccionTienda = "No se pudo cargar la dirección.";
        return;
      }

      const tienda = this.tiendas.find(
        (t) => t.id_tienda === parseInt(this.tiendaSeleccionada)
      );
      if (tienda) {
        this.direccionTienda = tienda.direccion || "Dirección no disponible";
      } else {
        console.error("No se encontró la tienda seleccionada en la lista.");
        this.direccionTienda = "Tienda no encontrada.";
      }
    },
    async fetchTiendas() {
      try {
        const response = await axios.get("http://127.0.0.1:8000/tiendas");
        this.tiendas = response.data || [];
        console.log("Tiendas cargadas:", this.tiendas);
      } catch (error) {
        console.error("Error al obtener las tiendas:", error);
        this.tiendas = [];
      }
    },
    async finalizarCompra() {
  // Validar que los campos requeridos estén completos
  if (!this.metodoEntrega || !this.metodoPago) {
    alert("Por favor, complete todos los campos requeridos.");
    return;
  }

  try {
    // Registrar datos en la consola antes de enviar
    console.log("Carrito antes de enviar:", this.carrito);

    // Recopilar datos para la venta
    const ventaData = {
      id_tienda: this.carrito[0]?.id_tienda || this.tiendaSeleccionada, // Tienda seleccionada o del producto
      usuario: { id_usuario: this.usuario.id_usuario }, // Usuario logueado
      direccion: this.direccionEnvio || this.direccionTienda, // Dirección de envío o recogida
      MetodoPago: this.metodoPago,
      MetodoEntrega: this.metodoEntrega,
      total: this.totalCompra,
      carrito: this.carrito.map((item) => ({
        id_inventario: item.id_inventario,
        cantidad: item.cantidad,
        precioConDescuento: item.precioConDescuento,
        precio_total: item.cantidad * item.precioConDescuento, // Aquí se calcula
      })),
    };

    console.log("Datos enviados al backend:", ventaData); // Verificación de datos antes del envío

    // Enviar datos al backend
    const response = await axios.post("http://127.0.0.1:8000/ventas", ventaData);

    if (response.status === 200 || response.status === 201) {
      alert("Compra realizada con éxito. ¡Gracias por tu compra!");
      // Resetear carrito y datos
      this.generarFacturaPDF(ventaData);
      this.carrito = []; // Reinicia solo el carrito.
      localStorage.removeItem("carrito");

      this.mostrarModal = false; // Cierra el modal.
      this.direccionEnvio = "";
      this.metodoEntrega = "";
      this.metodoPago = "";
      this.mostrarModal = false;
      this.$router.push("/cliente/NuevoComponente"); // Redirigir al historial de compras
    }
  } catch (error) {
    console.error("Error al finalizar la compra:", error.response || error);
    alert("Hubo un problema al procesar tu compra. Intenta nuevamente.");
  }
},
    // Nuevo método: generarFacturaPDF
    generarFacturaPDF(ventaData) {
    const doc = new jsPDF();

    // Configurar márgenes
    const pageWidth = doc.internal.pageSize.width;
    const margin = 20;
    let cursorY = margin;

    // Título de la factura
    doc.setFontSize(18);
    doc.text("Factura de Compra", pageWidth / 2, cursorY, { align: "center" });
    cursorY += 10;

    // Información del comprador
    doc.setFontSize(12);
    doc.text(`Cliente: ${this.comprador}`, margin, cursorY);
    cursorY += 10;
    doc.text(`Tienda: ${this.tienda}`, margin, cursorY);
    cursorY += 10;
    doc.text(`NIT: ${this.nit || "CF"}`, margin, cursorY);
    cursorY += 10;
    doc.text(`Método de Entrega: ${ventaData.MetodoEntrega}`, margin, cursorY);
    cursorY += 10;
    doc.text(`Método de Pago: ${ventaData.MetodoPago}`, margin, cursorY);
    cursorY += 10;
    doc.text(`Dirección: ${ventaData.direccion}`, margin, cursorY);
    cursorY += 15;

    // Tabla de productos
    const tableData = this.carrito.map((item) => [
        item.nombre,
        item.cantidad,
        `Q${this.formatNumber(item.precioConDescuento)}`,
        `Q${this.formatNumber(item.cantidad * item.precioConDescuento)}`,
    ]);

    autoTable(doc, {
        startY: cursorY,
        head: [["Producto", "Cantidad", "Precio Unitario", "Subtotal"]],
        body: tableData,
        styles: { halign: "center", valign: "middle" },
        headStyles: { fillColor: [52, 152, 219], textColor: [255, 255, 255] },
        alternateRowStyles: { fillColor: [245, 245, 245] },
    });

    cursorY = doc.lastAutoTable.finalY + 10;

    // Total
    doc.setFontSize(14);
    doc.setTextColor(40);
    doc.setFont("helvetica", "bold");
    doc.text(`Total: Q${this.formatNumber(this.totalCompra)}`, margin, cursorY);

    // Agregar pie de página
    doc.setFontSize(10);
    doc.setTextColor(150);
    doc.text(
        "Gracias por su compra. Visítenos nuevamente.",
        pageWidth / 2,
        doc.internal.pageSize.height - 10,
        { align: "center" }
    );

    // Descargar PDF
    doc.save(`Factura-${new Date().toISOString()}.pdf`);
},

    
    volver() {
      this.$router.push("/cliente/NuevoComponente");
    },
    actualizarTotales() {
      this.carrito.forEach((item) => {
        if (item.cantidad > item.existencia) {
          alert(
            `La cantidad solicitada (${item.cantidad}) para "${item.nombre}" excede el stock disponible (${item.existencia}).`
          );
          item.cantidad = item.existencia;
        } else if (item.cantidad < 1 || isNaN(item.cantidad)) {
          item.cantidad = 1;
        }
      });
    },
    formatNumber(value) {
      if (isNaN(value)) return "0.00";
      return parseFloat(value).toLocaleString("es-GT", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
};

</script>


<style scoped>
.modal-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.modal-form label {
  font-weight: bold;
  font-size: 1.1rem;
  color: #34495e;
}

.modal-form input {
  border: 2px solid #ddd;
  border-radius: 5px;
  padding: 10px;
  background: #f9f9f9;
}

.modal-form select {
  padding: 10px;
  font-size: 1rem;
  border: 2px solid #ccc;
  border-radius: 5px;
  width: 100%;
  background: #f7f7f7;
  transition: border-color 0.3s ease;
}

.modal-form input:focus,
.modal-form select:focus {
  border-color: #3498db;
  outline: none;
}

.direccion-mensaje {
  font-size: 0.9rem;
  color: #555;
  margin-top: -10px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050; /* Más alto que el footer */
}

.modal-content {
  width: 70%;
  max-height: 90%;
  overflow-y: auto;
  padding: 30px;
  background: linear-gradient(to right, #ffffff, #f2f2f2);
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.modal-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}
/* Estilo del botón de cancelar */
.cancel-btn {
  background-color: #dc3545;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  margin-right: 10px;
}

.cancel-btn:hover {
  background-color: #c82333;
}
/* Contenedor principal */
.resumen-compra-container {
  padding: 30px;
  background-color: #f5f5f5;
  border-radius: 10px;
  max-width: 1200px;
  margin: 20px auto;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

/* Título */
h1 {
  text-align: center;
  font-size: 28px;
  color: #444;
}

/* Nombre de la tienda */
.tienda-nombre {
  text-align: center;
  font-size: 18px;
  margin-bottom: 20px;
  color: #555;
}

/* Diseño en grid */
.resumen-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
}

/* Tabla de productos */
.productos-container {
  overflow-x: auto;
}

.resumen-tabla {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.resumen-tabla th {
  background-color: #3498db;
  color: #fff;
  text-align: center;
  padding: 12px;
  font-size: 16px;
}
.resumen-tabla td {
  padding: 12px;
  text-align: center;
  border: 1px solid #ddd;
  font-size: 14px;
}
.resumen-tabla th {
  background-color: #f4f4f4;
  font-weight: bold;
  color: #333;
}

.resumen-tabla td {
  vertical-align: middle;
}

.tabla-img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 5px;
}

/* Resumen del pago */
.resumen-total {
  background-color: #fff;
  border-radius: 8px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.resumen-total h2 {
  margin-bottom: 20px;
  font-size: 20px;
  color: #333;
}

.resumen-detalle {
  margin-bottom: 20px;
}

.resumen-detalle p {
  margin: 5px 0;
  font-size: 16px;
  color: #555;
}

.resumen-detalle .total {
  font-size: 18px;
  font-weight: bold;
  color: #000;
}

.confirm-btn {
  background-color: #28a745;
  color: white;
  padding: 10px 20px;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
}
.close-btn {
  background-color: #e74c3c;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
}
.close-btn:hover {
  background-color: #c0392b;
}
.confirm-btn {
  background-color: #28a745;
  padding: 12px 25px;
  border-radius: 8px;
}
.confirm-btn:hover {
  background-color: #218838;
}

.back-btn {
  background-color: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}

.back-btn:hover {
  background-color: #0056b3;
}
.cantidad-input {
  width: 70px;
  padding: 8px;
  font-size: 16px;
  text-align: center;
  border: 2px solid #ccc;
  border-radius: 8px;
  background-color: #f9f9f9;
  transition: all 0.3s ease;
}
.cantidad-input:focus {
  border-color: #28a745;
  background-color: #ffffff;
  box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
  outline: none;
}
.modal-form {
  border-bottom: 1px solid #ddd;
  padding-bottom: 20px;
  margin-bottom: 20px;
}
.direccion-mensaje {
  font-size: 0.9rem;
  color: #777;
  margin-top: 10px;
}
input:focus,
select:focus {
  border-color: #2980b9;
  box-shadow: 0 0 5px rgba(41, 128, 185, 0.5);
}

.modal-large {
  width: 90%;
  max-height: 90%;
  overflow-y: auto;
}

.table-container {
  max-height: 300px; /* Ajusta el alto máximo según sea necesario */
  overflow-y: auto;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

.modal-tabla {
  width: 100%;
  border-collapse: collapse;
}

.modal-tabla th {
  background-color: #007bff;
  color: white;
  font-weight: bold;
}

.modal-tabla td {
  padding: 10px;
  border: 1px solid #ccc;
  text-align: left;
}

.modal-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
</style>
