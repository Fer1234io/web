<template>
    <div class="historial-compras">
      <h2 class="title">Historial de Compras</h2>
  
      <!-- Filtro por Estado -->
      <div class="filter-container">
        <label for="estado">Filtrar por Estado:</label>
        <select v-model="filtroEstado" @change="filtrarVentas" id="estado" class="filter-select">
          <option value="">Todos</option>
          <option value="1">Pendiente</option>
          <option value="0">Entregada</option>
        </select>
      </div>
  
      <!-- Tabla de Ventas -->
      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(venta, index) in ventasFiltradas"
              :key="venta.id_venta"
              @click="mostrarDetalles(venta)"
              class="table-row"
            >
              <td>{{ index + 1 }}</td>
              <td>{{ venta.fecha_venta }}</td>
              <td>{{ venta.total }}</td>
              <td :class="estadoClase(venta.estado)">{{ obtenerEstado(venta.estado) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Modal de Detalles -->
      <div v-if="ventaSeleccionada" class="modal" @click.self="cerrarModal">
        <div class="modal-content">
          <h3>Detalles de la Venta</h3>
          <p><strong>Compra N°:</strong> {{ ventasFiltradas.indexOf(ventaSeleccionada) + 1 }}</p>
          <p><strong>Fecha:</strong> {{ ventaSeleccionada.fecha_venta }}</p>
          <p><strong>Total:</strong> {{ ventaSeleccionada.total }}</p>
          <p><strong>Estado:</strong> {{ obtenerEstado(ventaSeleccionada.estado) }}</p>
          <p><strong>Método de Pago:</strong> {{ ventaSeleccionada.metodopago }}</p>
  
          <h4>Productos</h4>
<ul>
  <li v-for="detalle in detallesVenta" :key="detalle.id_detalle">
    {{ detalle.cantidad }} x {{ detalle.nombre_producto }} - Q{{ detalle.precio_total }}
  </li>
</ul>

          <button class="btn-close" @click="cerrarModal">Cerrar</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        ventas: [],
        ventasFiltradas: [],
        detallesVenta: [],
        filtroEstado: "",
        ventaSeleccionada: null,
      };
    },
    methods: {
      async obtenerVentas() {
        const userData = JSON.parse(localStorage.getItem("userData"));
        if (!userData || !userData.id_usuario) {
          alert("Tu sesión ha expirado. Por favor, inicia sesión nuevamente.");
          this.$router.push("/login");
          return;
        }
        try {
          const response = await axios.get(`http://localhost:8000/ventas/${userData.id_usuario}`);
          if (response.status === 200 && Array.isArray(response.data)) {
            this.ventas = response.data.map((venta) => ({
              id_venta: venta.id_venta,
              fecha_venta: venta.fecha_venta,
              total: parseFloat(venta.total).toFixed(2),
              estado: venta.estado,
              metodopago: venta.metodopago,
            }));
            this.ventasFiltradas = [...this.ventas];
          } else {
            alert("No se encontraron ventas para este usuario.");
          }
        } catch (error) {
          alert("Error al obtener las ventas. Inténtalo más tarde.");
          console.error("Error:", error);
        }
      },
      obtenerEstado(estado) {
        switch (estado) {
          case 1:
            return "Pendiente";
          case 0:
            return "Entregada";
          case 2:
            return "Cancelada";
          default:
            return "Desconocido";
        }
      },
      estadoClase(estado) {
        switch (estado) {
          case 1:
            return "estado-pendiente";
          case 0:
            return "estado-entregada";
          case 2:
            return "estado-cancelada";
          default:
            return "";
        }
      },
      filtrarVentas() {
        if (this.filtroEstado === "") {
          this.ventasFiltradas = [...this.ventas];
        } else {
          const estadoNumerico = parseInt(this.filtroEstado);
          this.ventasFiltradas = this.ventas.filter((venta) => venta.estado === estadoNumerico);
        }
      },
      async mostrarDetalles(venta) {
    this.ventaSeleccionada = venta;
    try {
        const response = await axios.get(`http://localhost:8000/detalle-ventas/${venta.id_venta}`);
        this.detallesVenta = response.data.map((detalle) => ({
            nombre_producto: detalle.nombre_producto,
            cantidad: detalle.cantidad,
            precio_unitario: detalle.precio_unitario,
            precio_total: detalle.precio_total,
        }));
    } catch (error) {
        console.error("Error al obtener los detalles de la venta:", error);
        alert("Error al obtener los detalles de la venta.");
    }
},
      cerrarModal() {
        this.ventaSeleccionada = null;
        this.detallesVenta = [];
      },
    },
    mounted() {
      this.obtenerVentas();
    },
  };
  </script>
  
  <style scoped>
  .historial-compras {
    padding: 20px;
    font-family: "Roboto", sans-serif;
    color: #333;
  }
  
  .title {
    text-align: center;
    margin-bottom: 20px;
    color: #444;
  }
  
  .filter-container {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 20px;
  }
  
  .filter-select {
    margin-left: 10px;
    padding: 5px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  
  .table-container {
    overflow-x: auto;
  }
  
  .styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 1em;
    text-align: left;
  }
  
  .styled-table th,
  .styled-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
  }
  
  .styled-table thead tr {
    background-color: #009879;
    color: #ffffff;
  }
  
  .table-row:hover {
    background-color: #f3f3f3;
    cursor: pointer;
  }
  
  .estado-pendiente {
    color: orange;
    font-weight: bold;
  }
  
  .estado-entregada {
    color: green;
    font-weight: bold;
  }
  
  .estado-cancelada {
    color: red;
    font-weight: bold;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    width: 90%;
  }
  
  .btn-close {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
  }
  
  .btn-close:hover {
    background-color: #0056b3;
  }
  </style>
  