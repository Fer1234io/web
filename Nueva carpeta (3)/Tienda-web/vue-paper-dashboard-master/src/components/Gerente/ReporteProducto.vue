<template>
    <div class="reportes-container">
      <h1>Top 100 Productos Más Vendidos</h1>
  
      <!-- Botón para regresar -->
      <div class="back-button">
        <button @click="regresar">Regresar</button>
      </div>
  
      <!-- Formulario para generar reporte -->
      <div class="reporte-form">
        <h2>Generar Reporte</h2>
        <form @submit.prevent="generarReporte">
          <label for="tipoReporte">Tipo de Reporte:</label>
          <select v-model="tipoReporte" id="tipoReporte" required>
            <option value="general">General</option>
            <option value="sucursal">Por Sucursal</option>
          </select>
  
          <!-- Campo para seleccionar sucursal si es por sucursal -->
          <div v-if="tipoReporte === 'sucursal'">
            <label for="sucursal">Seleccionar Sucursal:</label>
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
              <th>#</th>
              <th>Producto</th>
              <th>Total Vendido</th>
              <th v-if="tipoReporte === 'sucursal'">Sucursal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(producto, index) in reporteSeleccionado" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ producto.nombre_producto }}</td>
              <td>{{ producto.total_vendido }}</td>
              <td v-if="tipoReporte === 'sucursal'">{{ producto.nombre_tienda }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Mensaje cuando no hay resultados -->
      <div v-if="reporteSeleccionado.length === 0 && reporteGenerado">
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
      regresar() {
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
          const payload = { tipo: this.tipoReporte };
          if (this.tipoReporte === "sucursal" && this.sucursalSeleccionada) {
            payload.sucursal = this.sucursalSeleccionada;
          }
  
          const response = await axios.post(
            "http://127.0.0.1:8000/reportes/top-productos",
            payload
          );
  
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
  /* Contenedor principal */
  .reportes-container {
    text-align: center;
    padding: 20px;
    font-family: "Roboto", Arial, sans-serif;
    background: linear-gradient(135deg, #f0f8ff, #e6f7ff);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  
  /* Botón de regresar */
  .back-button {
    margin-bottom: 20px;
    width: 100%;
    display: flex;
    justify-content: flex-start;
  }
  
  .back-button button {
    padding: 10px 15px;
    background-color: #ff6666;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  
  .back-button button:hover {
    background-color: #cc0000;
  }
  
  /* Formulario */
  .reporte-form {
    margin-bottom: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
  }
  
  .reporte-form h2 {
    margin-bottom: 15px;
    color: #333;
  }
  
  .reporte-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #666;
  }
  
  .reporte-form select,
  .reporte-form button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
  }
  
  .reporte-form button {
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  
  .reporte-form button:hover {
    background-color: #0056b3;
  }
  
  /* Resultados */
  .reporte-resultados {
    margin-top: 20px;
    width: 100%;
    max-width: 800px;
  }
  
  .reporte-resultados table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  
  .reporte-resultados th,
  .reporte-resultados td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
  }
  
  .reporte-resultados th {
    background-color: #f4f4f4;
    font-weight: bold;
  }
  
  .reporte-resultados tr:hover {
    background-color: #f9f9f9;
  }
  
  /* Mensaje cuando no hay resultados */
  p {
    color: #666;
    font-size: 1rem;
  }
  
  /* Responsividad */
  @media (max-width: 768px) {
    .reporte-form,
    .reporte-resultados {
      width: 90%;
    }
  }
  </style>
  