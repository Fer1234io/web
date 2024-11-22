<template>
    <div class="reportes-container">
      <!-- Botón para regresar -->
      <div class="back-button">
        <button @click="irARuta">Regresar</button>
      </div>
  
      <h1>Reporte de Clientes Frecuentes</h1>
  
      <!-- Formulario para seleccionar reporte -->
      <div class="reporte-form">
        <h2>Generar Reporte</h2>
        <form @submit.prevent="generarReporte">
          <label for="tipoReporte">Filtrar por:</label>
          <select v-model="tipoReporte" id="tipoReporte" required>
            <option value="general">General</option>
            <option value="sucursal">Por Sucursal</option>
          </select>
  
          <!-- Filtro por sucursal -->
          <div v-if="tipoReporte === 'sucursal'">
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
              <th>Cliente</th>
              <th>Correo</th>
              <th>Total Compras</th>
              <th v-if="tipoReporte === 'sucursal'">Sucursal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in reporteSeleccionado" :key="index">
              <td>{{ item.nombre_cliente }}</td>
              <td>{{ item.correo }}</td>
              <td>{{ item.total_compras }}</td>
              <td v-if="tipoReporte === 'sucursal'">{{ item.sucursal }}</td>
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
        tipoReporte: "general",
        sucursales: [],
        sucursalSeleccionada: "",
        reporteSeleccionado: [],
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
          const endpoint = "http://127.0.0.1:8000/reportes/clientes-frecuentes";
          const payload = {
            tipo: this.tipoReporte,
            sucursal: this.tipoReporte === "sucursal" ? this.sucursalSeleccionada : null,
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
  