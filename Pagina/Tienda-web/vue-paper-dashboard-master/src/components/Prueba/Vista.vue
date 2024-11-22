<template>
    <div class="admin-ventas">
      <h2>Administración de Ventas</h2>
  
      <!-- Filtro por Estado -->
      <div class="filter-container">
        <label for="estado">Filtrar por Estado:</label>
        <select v-model="filtroEstado" @change="obtenerVentas" id="estado">
          <option value="">Todos</option>
          <option value="1">Pendiente</option>
          <option value="0">Entregado</option>
        </select>
      </div>
  
      <!-- Tabla de Ventas -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>ID Venta</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Fecha Entrega</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(venta, index) in ventas" :key="venta.id_venta">
              <td>{{ index + 1 }}</td>
              <td>{{ venta.id_venta }}</td>
              <td>{{ venta.usuario ? venta.usuario.nombre : "No disponible" }}</td>
              <td>{{ venta.total }}</td>
              <td>{{ obtenerEstado(venta.estado) }}</td>
              <td>{{ venta.fecha_entrega || "No entregado" }}</td>
              <td>
                <button
                  v-if="venta.estado === 1"
                  @click="actualizarEstado(venta)"
                  class="btn-entregar"
                >
                  Marcar como Entregado
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        ventas: [], 
        filtroEstado: "", 
      };
    },
    methods: {
      // Obtener las ventas para administración
      async obtenerVentas() {
        try {
          const response = await axios.get(`http://localhost:8000/admin/ventas`, {
            params: { estado: this.filtroEstado },
          });
          this.ventas = response.data;
        } catch (error) {
          console.error("Error al obtener las ventas:", error);
          alert("Error al obtener las ventas. Inténtalo más tarde.");
        }
      },
  
      // Actualizar el estado de una venta y registrar la fecha de entrega
      async actualizarEstado(venta) {
        const actualizarUrl = `http://localhost:8000/admin/ventas/${venta.id_venta}/estado`;
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("estado", 0); 
        formData.append("fecha_entrega", new Date().toISOString().slice(0, 10));
  
        try {
          const response = await axios.post(actualizarUrl, formData, {
            headers: { "Content-Type": "multipart/form-data" },
          });
          console.log("Respuesta del servidor:", response.data);
          alert("Estado actualizado correctamente.");
          this.obtenerVentas(); 
        } catch (error) {
          console.error("Error al actualizar el estado de la venta:", error);
          alert("Error al actualizar el estado. Inténtalo más tarde.");
        }
      },
  
      // Convertir el estado numérico a texto
      obtenerEstado(estado) {
        switch (estado) {
          case 1:
            return "Pendiente";
          case 0:
            return "Entregado";
          case 2:
            return "Cancelado";
          default:
            return "Desconocido";
        }
      },
    },
    mounted() {
      this.obtenerVentas(); 
    },
  };
  </script>
  
  <style scoped>
  .admin-ventas {
    padding: 20px;
    font-family: "Roboto", sans-serif;
  }
  
  .filter-container {
    margin-bottom: 20px;
  }
  
  .table-container {
    margin-top: 20px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th,
  td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  th {
    background-color: #f4f4f4;
  }
  
  .btn-entregar {
    background-color: #4caf50;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 5px;
  }
  
  .btn-entregar:hover {
    background-color: #45a049;
  }
  </style>
  