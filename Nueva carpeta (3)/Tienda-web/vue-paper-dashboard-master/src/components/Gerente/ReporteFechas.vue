<template>
  <div class="reportes-container">
    <!-- Botón para regresar -->
    <div class="back-button">
      <button @click="irARuta">Regresar</button>
    </div>

    <h1>Reporte de Compras por Rango de Fecha</h1>

    <!-- Formulario para seleccionar rango de fechas -->
    <div class="reporte-form">
      <h2>Seleccionar Rango de Fecha</h2>
      <form @submit.prevent="generarReporte">
        <label for="fechaInicio">Fecha Inicio:</label>
        <input type="date" v-model="fechaInicio" id="fechaInicio" required />

        <label for="fechaFin">Fecha Fin:</label>
        <input type="date" v-model="fechaFin" id="fechaFin" required />

        <button type="submit">Generar Reporte</button>
      </form>
    </div>

    <!-- Resultados del reporte -->
    <div class="reporte-resultados" v-if="reporteSeleccionado.length > 0">
      <h2>Resultados del Reporte</h2>
      <table>
        <thead>
          <tr>
            <th>ID Venta</th>
            <th>Fecha Venta</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(venta, index) in reporteSeleccionado" :key="index">
            <td>{{ venta.id_venta }}</td>
            <td>{{ venta.fecha_venta }}</td>
            <td>Q{{ venta.total }}</td>
            <td>
              <button @click="verDetalles(venta.id_venta)">Ver Detalles</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Detalles de la compra -->
    <div class="detalle-compra" v-if="detallesCompra.length > 0">
      <h2>Detalles de la Compra</h2>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Precio Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(detalle, index) in detallesCompra" :key="index">
            <td>{{ detalle.nombre_producto }}</td>
            <td>{{ detalle.cantidad }}</td>
            <td>Q{{ detalle.precio_unitario }}</td>
            <td>Q{{ detalle.precio_total }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mensaje cuando no hay resultados -->
    <div class="no-resultados" v-if="reporteSeleccionado.length === 0 && reporteGenerado">
      <p>No se encontraron resultados para este reporte.</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      fechaInicio: "",
      fechaFin: "",
      reporteSeleccionado: [],
      detallesCompra: [],
      reporteGenerado: false,
    };
  },
  methods: {
    irARuta() {
      this.$router.push("/gerente/Reportes");
    },
    async generarReporte() {
      try {
        if (!this.fechaInicio || !this.fechaFin) {
          alert("Por favor, seleccione ambas fechas.");
          return;
        }

        const response = await axios.post("http://127.0.0.1:8000/reportes/compras-por-fecha", {
          fechaInicio: this.fechaInicio,
          fechaFin: this.fechaFin,
        });

        this.reporteSeleccionado = response.data.data;
        this.reporteGenerado = true;
      } catch (error) {
        console.error("Error al generar el reporte:", error);
        alert("Hubo un error al generar el reporte.");
      }
    },
    async verDetalles(idVenta) {
      try {
        const response = await axios.post("http://127.0.0.1:8000/reportes/detalle-compra", {
          id_venta: idVenta,
        });

        this.detallesCompra = response.data.data;
      } catch (error) {
        console.error("Error al obtener los detalles de la compra:", error);
        alert("Hubo un error al obtener los detalles de la compra.");
      }
    },
  },
};
</script>

<style scoped>
.reportes-container {
  padding: 20px;
  font-family: "Roboto", sans-serif;
  background: linear-gradient(to right, #e3f2fd, #ffffff);
  min-height: 100vh;
}

.back-button {
  margin-bottom: 15px;
  display: flex;
}

.back-button button {
  padding: 10px 15px;
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.back-button button:hover {
  background-color: #d32f2f;
}

h1 {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 20px;
}

.reporte-form {
  margin: 0 auto 20px;
  background-color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 600px;
}

.reporte-form label {
  font-weight: bold;
  margin-bottom: 5px;
  display: block;
}

.reporte-form input,
.reporte-form button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.reporte-form button {
  background-color: #007bff;
  color: white;
  font-size: 1rem;
}

.reporte-form button:hover {
  background-color: #0056b3;
}

.reporte-resultados table,
.detalle-compra table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.reporte-resultados th,
.reporte-resultados td,
.detalle-compra th,
.detalle-compra td {
  padding: 10px;
  text-align: center;
  border: 1px solid #ddd;
}

.reporte-resultados th,
.detalle-compra th {
  background-color: #f4f4f4;
}

.no-resultados {
  text-align: center;
  color: #757575;
}
</style>
