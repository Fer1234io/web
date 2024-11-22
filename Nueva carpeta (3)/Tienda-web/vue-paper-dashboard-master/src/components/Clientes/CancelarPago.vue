<template>
    <div class="cancelar-pago-container">
      <h1>Cancelar Pago</h1>
      <p class="tienda-nombre"><strong>Tienda:</strong> {{ tienda }}</p>
      <p><strong>Comprador:</strong> {{ comprador }}</p>
  
      <div class="resumen-grid">
        <!-- Información del comprador -->
        <div class="comprador-info">
          <h2>Información del Comprador</h2>
          <p><strong>ID Usuario:</strong> {{ usuario.id_usuario }}</p>
          <p><strong>Nombre:</strong> {{ usuario.nombre }}</p>
          <p><strong>Apellido:</strong> {{ usuario.apellido }}</p>
        </div>
  
        <!-- Tabla de productos -->
        <div class="productos-container">
          <h2>Productos</h2>
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
                  <img
                    :src="'data:image/jpeg;base64,' + item.foto"
                    alt="Producto"
                    class="tabla-img"
                  />
                </td>
                <td>{{ item.nombre }}</td>
                <td>{{ item.categoria || "Sin categoría" }}</td>
                <td>Q{{ formatNumber(item.precioNormal) }}</td>
                <td>
                  Q{{ item.descuento > 0 ? formatNumber(item.precioConDescuento) : "0.00" }}
                </td>
                <td>{{ item.cantidad }}</td>
                <td>Q{{ formatNumber(item.cantidad * item.precioConDescuento) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Formulario para finalizar la cancelación -->
        <div class="finalizar-pago">
          <h2>Información de Pago</h2>
  
          <!-- Método de entrega -->
          <div class="form-group">
            <label for="metodo-entrega">Método de Entrega:</label>
            <select id="metodo-entrega" v-model="metodoEntrega" @change="verificarMetodoEntrega">
              <option disabled value="">Selecciona un método de entrega</option>
              <option v-for="(metodo, index) in metodosEntrega" :key="index" :value="metodo">
                {{ metodo }}
              </option>
            </select>
            <p v-if="metodoEntrega === 'Recoger en tienda'" class="info-text">
              Dirección de la tienda: {{ tiendaDireccion }}
            </p>
          </div>
  
          <!-- Dirección (solo para Envío a domicilio) -->
          <div v-if="metodoEntrega === 'Envío a domicilio'" class="form-group">
            <label for="direccion">Dirección:</label>
            <input
              type="text"
              id="direccion"
              v-model="direccion"
              placeholder="Ingresa tu dirección exacta"
            />
          </div>
  
          <!-- Método de pago -->
          <div class="form-group">
            <label for="metodo-pago">Método de Pago:</label>
            <select id="metodo-pago" v-model="metodoPago">
              <option disabled value="">Selecciona un método de pago</option>
              <option v-for="(metodo, index) in opcionesPago" :key="index" :value="metodo">
                {{ metodo }}
              </option>
            </select>
            <p v-if="metodoPago === 'Previo Depósito'" class="info-text">
              Por favor, envía el comprobante de pago al correo
              <strong>pagos@tienda.com</strong> o preséntalo al recoger en la tienda.
            </p>
          </div>
  
          <!-- Total y costos -->
          <div class="form-group">
            <p><strong>Sub-total:</strong> Q{{ formatNumber(totalCompra) }}</p>
            <p v-if="metodoEntrega === 'Envío a domicilio'">
              <strong>Costo de Envío:</strong> Q{{ formatNumber(envioCosto) }}
            </p>
            <p><strong>Total Final:</strong> Q{{ formatNumber(totalCompraFinal) }}</p>
          </div>
  
          <!-- Mensaje de error -->
          <p v-if="mensajeError" class="error-message">{{ mensajeError }}</p>
  
          <!-- Feedback de proceso -->
          <p v-if="cargando" class="loading-message">Procesando tu pedido...</p>
  
          <!-- Botones -->
          <button class="confirm-btn" :disabled="cargando" @click="finalizarPago">Finalizar Pago</button>
          <button class="back-btn" @click="volver">Cancelar</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        usuario: null,
        tienda: "",
        carrito: [],
        direccion: "",
        metodoPago: "",
        metodoEntrega: "",
        tiendaDireccion: "Zona 1, Ciudad de Guatemala", // Dirección ficticia de la tienda
        metodosEntrega: ["Envío a domicilio", "Recoger en tienda"],
        metodosPago: ["Previo Depósito", "Pago contra entrega", "Pagar en tienda"],
        envioCosto: 30, // Costo por envío
        mensajeError: "", // Mensaje de error
        cargando: false, // Estado de carga
        opcionesPago: [], // Opciones dinámicas para el método de pago
      };
    },
    computed: {
      comprador() {
        return this.usuario
          ? `${this.usuario.nombre} ${this.usuario.apellido}`
          : "Usuario desconocido";
      },
      totalCompra() {
        return this.carrito.reduce(
          (total, item) => total + item.cantidad * item.precioConDescuento,
          0
        );
      },
      totalCompraFinal() {
        return this.metodoEntrega === "Envío a domicilio"
          ? this.totalCompra + this.envioCosto
          : this.totalCompra;
      },
    },
    created() {
      const userData = localStorage.getItem("userData");
      if (userData) {
        this.usuario = JSON.parse(userData);
      }
  
      const carritoData = localStorage.getItem("carrito");
      if (carritoData) {
        this.carrito = JSON.parse(carritoData);
      }
  
      this.tienda = localStorage.getItem("nombreTienda") || "Tienda desconocida";
    },
    methods: {
        verificarMetodoEntrega() {
    if (this.metodoEntrega === "Recoger en tienda") {
      this.direccion = ""; // Limpiar la dirección
      this.opcionesPago = ["Previo Depósito", "Pagar en tienda"];
    } else if (this.metodoEntrega === "Envío a domicilio") {
      this.opcionesPago = ["Previo Depósito", "Pago contra entrega"];
    }
  },
      ffinalizarPago() {
    if (!this.metodoEntrega || !this.metodoPago || (this.metodoEntrega === "Envío a domicilio" && !this.direccion)) {
      this.mensajeError = "Por favor, completa todos los campos requeridos.";
      return;
    }
    const payload = {
      usuario: this.usuario,
      carrito: this.carrito,
      direccion: this.metodoEntrega === "Recoger en tienda" ? this.tiendaDireccion : this.direccion,
      MetodoPago: this.metodoPago,
      MetodoEntrega: this.metodoEntrega,
      total: this.totalCompraFinal,
      TotalPagado: null,
      Cambio: null,
      fecha_entrega: null,
    };
  
        // Enviar datos al backend
    // Verificar que Axios esté configurado correctamente
    axios.post("http://127.0.0.1:8000/ventas", payload)
      .then(() => {
        this.mensajeError = "";
        alert("Pago finalizado con éxito.");
        this.$router.push("/cliente/historial-compras");
      })
      .catch((error) => {
        console.error("Error al finalizar el pago:", error);
        this.mensajeError = "Ocurrió un error al finalizar el pago.";
      });
  },
  volver() {
    this.$router.push("/cliente/resumen-compra");
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
  /* Estilos generales */
  .cancelar-pago-container {
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    max-width: 1200px;
    margin: 20px auto;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  .info-text {
    color: #007bff;
    font-size: 14px;
    margin-top: 5px;
  }
  .error-message {
    color: #d9534f;
    font-size: 14px;
    margin-top: 10px;
  }
  .loading-message {
    color: #007bff;
    font-size: 16px;
    margin-top: 10px;
  }
  </style>
  