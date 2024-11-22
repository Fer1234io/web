<template>
  <div class="container">
    <h1 class="title">Gestión de Traslados</h1>

    <!-- Combobox para seleccionar tienda destino -->
    <div class="filter-group">
      <div class="filter">
        <label for="tiendaDestino">Seleccionar Tienda Destino:</label>
        <select v-model="selectedTiendaDestino" @change="fetchTraslados" class="select">
          <option value="">Mostrar Todos</option>
          <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
            {{ tienda.nombre_tienda }}
          </option>
        </select>
      </div>

      <!-- Combobox para seleccionar estado -->
      <div class="filter">
        <label for="estadoTraslado">Seleccionar Estado:</label>
        <select v-model="selectedEstado" @change="fetchTraslados" class="select">
          <option value="">Mostrar Todos</option>
          <option value="En Camino">En Camino</option>
          <option value="Entregado">Entregado</option>
        </select>
      </div>
    </div>

    <!-- Tabla de Traslados -->
    <div class="table-container">
      <table v-if="traslados.length > 0" class="styled-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Imagen</th>
            <th>Tienda Origen</th>
            <th>Tienda Destino</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Fecha de Traslado</th>
            <th>Fecha de Recepción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="traslado in traslados" :key="traslado.id_traslado">
            <td>{{ traslado.id_traslado }}</td>
            <td>{{ traslado.inventario.producto.nombre_producto }}</td>
            <td>
              <img :src="'data:image/jpeg;base64,' + traslado.inventario.producto.fotografia_url" alt="Producto" class="product-image" />
            </td>
            <td>{{ traslado.tienda_origen.nombre_tienda }}</td>
            <td>{{ traslado.tienda_destino.nombre_tienda }}</td>
            <td>{{ traslado.cantidad }}</td>
            <td>
              <span :class="['badge', traslado.estado === 'En Camino' ? 'badge-warning' : 'badge-success']">
                {{ traslado.estado }}
              </span>
            </td>
            <td>{{ traslado.fecha_traslado }}</td>
            <td>{{ traslado.fecha_recibido || 'N/A' }}</td>
            <td>
              <button @click="actualizarTraslado(traslado)" class="btn btn-primary">Actualizar Estado</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay registros de traslados disponibles.</p>
    </div>

    <!-- Modal para actualizar estado -->
    <div v-if="isUpdateModalOpen" class="modal">
      <div class="modal-content">
        <span @click="closeUpdateModal" class="close">&times;</span>
        <h2>Actualizar Estado de Traslado</h2>
        <form @submit.prevent="enviarActualizar">
          <div class="form-group">
            <label for="estado">Estado:</label>
            <select v-model="selectedTraslado.estado" required class="select">
              <option value="En Camino">En Camino</option>
              <option value="Entregado">Entregado</option>
            </select>
          </div>
          <button type="submit" class="btn btn-success">Guardar Cambios</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      traslados: [], // Array de traslados
      tiendas: [], // Array de tiendas
      selectedTiendaDestino: "", // ID de la tienda seleccionada
      selectedEstado: "", // Estado seleccionado
      selectedTraslado: null,
      isUpdateModalOpen: false,
    };
  },
  mounted() {
    this.fetchTiendas();
    this.fetchTraslados();
  },
  methods: {
    // Obtener todas las tiendas
    fetchTiendas() {
      axios
        .get("http://127.0.0.1:8000/tiendas")
        .then((response) => {
          this.tiendas = response.data;
        })
        .catch((error) => {
          console.error("Error al obtener las tiendas:", error);
        });
    },
    // Obtener los traslados según los filtros seleccionados
    fetchTraslados() {
      let url = "http://127.0.0.1:8000/traslados?";
      if (this.selectedTiendaDestino) {
        url += `id_destino=${this.selectedTiendaDestino}&`;
      }
      if (this.selectedEstado) {
        url += `estado=${this.selectedEstado}`;
      }

      axios
        .get(url)
        .then((response) => {
          this.traslados = response.data;
        })
        .catch((error) => {
          console.error("Error al obtener los traslados:", error);
        });
    },
    // Abrir modal de actualización
    actualizarTraslado(traslado) {
      this.selectedTraslado = traslado;
      this.isUpdateModalOpen = true;
    },
    // Cerrar modal
    closeUpdateModal() {
      this.isUpdateModalOpen = false;
      this.selectedTraslado = null;
    },
    // Actualizar estado del traslado
    enviarActualizar() {
      const url = `http://127.0.0.1:8000/traslados/${this.selectedTraslado.id_traslado}`;
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("estado", this.selectedTraslado.estado);

      axios
        .post(url, formData)
        .then((response) => {
          alert(response.data.mensaje);
          this.closeUpdateModal();
          this.fetchTraslados();
        })
        .catch((error) => {
          console.error("Error al actualizar el traslado:", error);
          alert("Ocurrió un error al actualizar el traslado.");
        });
    },
  },
};
</script>

<style scoped>
/* Container Styles */
.container {
  font-family: Arial, sans-serif;
  margin: 20px auto;
  max-width: 1200px;
  padding: 20px;
}

.title {
  text-align: center;
  font-size: 24px;
  color: #333;
}

/* Filter Group */
.filter-group {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.filter {
  flex: 1;
  margin-right: 10px;
}

label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

.select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Table Styles */
.table-container {
  overflow-x: auto;
}

.styled-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 16px;
}

.styled-table th, .styled-table td {
  text-align: left;
  padding: 12px;
}

.styled-table th {
  background-color: #007bff;
  color: white;
}

.styled-table tr:nth-child(even) {
  background-color: #f2f2f2;
}

/* Button Styles */
.btn {
  padding: 8px 12px;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary {
  background-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-success {
  background-color: #28a745;
}

.btn-success:hover {
  background-color: #218838;
}

/* Badge Styles */
.badge {
  padding: 5px 10px;
  border-radius: 4px;
  color: white;
}

.badge-warning {
  background-color: #ffc107;
}

.badge-success {
  background-color: #28a745;
}

/* Modal Styles */
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
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
  position: relative;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}

.close:hover {
  color: red;
}

/* Product Image */
.product-image {
  max-width: 50px;
  height: auto;
  border-radius: 5px;
}
</style>
