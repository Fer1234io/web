<template>
  <div class="reportes-container">
    <!-- Botón para regresar -->
    <div class="back-button">
      <button @click="irARuta">Regresar</button>
    </div>

    <h1>Reporte de Productos Más Vendidos</h1>

    <!-- Formulario para seleccionar filtros -->
    <div class="reporte-form">
      <h2>Filtrar Reporte</h2>
      <form @submit.prevent="generarReporte">
        <label for="opcionReporte">Filtrar por:</label>
        <select v-model="opcionReporte" id="opcionReporte" required>
          <option value="general">General</option>
          <option value="por-mes">Por Mes</option>
          <option value="por-sucursal">Por Sucursal</option>
        </select>

        <!-- Filtro por mes -->
        <div v-if="opcionReporte === 'por-mes'">
          <label for="mesSeleccionado">Mes:</label>
          <select v-model="mesSeleccionado" id="mesSeleccionado" required>
            <option v-for="mes in meses" :key="mes.numero" :value="mes.numero">
              {{ mes.nombre }}
            </option>
          </select>
        </div>

        <!-- Filtro por sucursal -->
        <div v-if="opcionReporte === 'por-sucursal'">
          <label for="sucursal">Sucursal:</label>
          <select v-model="sucursalSeleccionada" id="sucursal" required>
            <option value="">Seleccione una sucursal</option>
            <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
              {{ sucursal.nombre }}
            </option>
          </select>
        </div>

        <button type="submit">Generar Reporte</button>
      </form>
    </div>

    <!-- Resultados del reporte -->
    <div class="reporte-resultados" v-if="reporteSeleccionado.length > 0">
      <h2>Resultados del Reporte</h2>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th v-if="opcionReporte === 'por-mes'">Mes</th>
            <th>Cantidad</th>
            <th v-if="opcionReporte === 'por-sucursal'">Sucursal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in reporteSeleccionado" :key="index">
            <td>{{ item.nombre_producto }}</td>
            <td v-if="opcionReporte === 'por-mes'">{{ item.mes }}</td>
            <td>{{ item.total_vendido }}</td>
            <td v-if="opcionReporte === 'por-sucursal'">{{ item.nombre_tienda }}</td>
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
      opcionReporte: "general", // Opción seleccionada para el filtro
      sucursales: [],
      sucursalSeleccionada: "",
      mesSeleccionado: null, // Mes seleccionado en el filtro
      reporteSeleccionado: [], // Datos del reporte
      meses: [
        { numero: 1, nombre: "Enero" },
        { numero: 2, nombre: "Febrero" },
        { numero: 3, nombre: "Marzo" },
        { numero: 4, nombre: "Abril" },
        { numero: 5, nombre: "Mayo" },
        { numero: 6, nombre: "Junio" },
        { numero: 7, nombre: "Julio" },
        { numero: 8, nombre: "Agosto" },
        { numero: 9, nombre: "Septiembre" },
        { numero: 10, nombre: "Octubre" },
        { numero: 11, nombre: "Noviembre" },
        { numero: 12, nombre: "Diciembre" },
      ],
      reporteGenerado: false,
    };
  },
  mounted() {
    this.fetchSucursales();
  },
  methods: {
    irARuta() {
      this.$router.push("/gerente/Reportes");
    },
    async fetchSucursales() {
      try {
        const response = await axios.get("http://127.0.0.1:8000/tiendas");
        this.sucursales = response.data.map((sucursal) => ({
          id: sucursal.id_tienda,
          nombre: sucursal.nombre_tienda,
        }));
      } catch (error) {
        console.error("Error al cargar las sucursales:", error);
        alert("Hubo un error al cargar las sucursales.");
      }
    },
    async generarReporte() {
      try {
        this.reporteSeleccionado = [];
        this.reporteGenerado = false;

        const endpoint = "http://127.0.0.1:8000/reportes/productos-mas-vendidos-completo";
        const payload = {
          tipo: this.opcionReporte,
          mes: this.opcionReporte === "por-mes" ? this.mesSeleccionado : null,
          sucursal: this.opcionReporte === "por-sucursal" ? this.sucursalSeleccionada : null,
        };

        const response = await axios.post(endpoint, payload);
        this.reporteSeleccionado = response.data.data;
        this.reporteGenerado = true;
      } catch (error) {
        console.error("Error al generar el reporte:", error);
        alert("Hubo un error al generar el reporte.");
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

.reporte-form select,
.reporte-form button {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 10px;
}

.reporte-form button {
  background-color: #007bff;
  color: white;
  font-size: 1rem;
}

.reporte-form button:hover {
  background-color: #0056b3;
}

.reporte-resultados table {
  width: 100%;
  margin-top: 20px;
  border-collapse: collapse;
}

.reporte-resultados th,
.reporte-resultados td {
  padding: 10px;
  border: 1px solid #ddd;
}

.reporte-resultados th {
  background-color: #f1f1f1;
}

.no-resultados {
  text-align: center;
  color: #757575;
}
</style>
