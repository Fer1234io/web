<template>
    <div>
      <h1>Gestión de Inventario</h1>
      <div>
        <label for="tienda">Seleccionar tienda:</label>
        <select v-model="selectedTienda" @change="fetchInventarioPorTienda">
          <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
            {{ tienda.nombre_tienda }}
          </option>
        </select>
        <button @click="fetchTodosInventarios">Mostrar Todo el Inventario</button>
      </div>
  
      <button @click="openAddModal">Agregar Registro de Inventario</button>
  
      <table v-if="inventario.length > 0">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Imagen</th>
            <th>Tienda</th>
            <th>Cantidad</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in inventario" :key="item.id_inventario">
            <td>{{ item.producto.nombre_producto }}</td>
            <td>
              <img :src="'data:image/jpeg;base64,' + item.producto.fotografia_url" alt="Imagen del producto" width="100" />
            </td>
            <td>{{ item.tienda.nombre_tienda }}</td>
            <td>{{ item.existencia }}</td>
            <td>
              <button @click="openEditModal(item)">Editar</button>
              <button @click="eliminarRegistro(item)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay registros de inventario disponibles.</p>
  
      <!-- Modal para agregar/editar inventario -->
      <div v-if="isModalOpen" class="modal">
        <div class="modal-content">
          <span @click="closeModal" class="close">&times;</span>
          <h2>{{ isEditMode ? 'Editar Registro de Inventario' : 'Agregar Registro de Inventario' }}</h2>
          <form @submit.prevent="submitForm">
            <div>
              <label for="producto">Producto:</label>
              <select v-model="form.id_producto" required>
                <option v-for="producto in productos" :key="producto.id_producto" :value="producto.id_producto">
                  {{ producto.nombre_producto }}
                </option>
              </select>
            </div>
            <div>
              <label for="tienda">Tienda:</label>
              <select v-model="form.id_tienda" required>
                <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
                  {{ tienda.nombre_tienda }}
                </option>
              </select>
            </div>
            <div>
              <label for="existencia">Cantidad:</label>
              <input type="number" v-model="form.existencia" required />
            </div>
            <button type="submit">{{ isEditMode ? 'Actualizar' : 'Guardar' }}</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        productos: [],
        tiendas: [],
        inventario: [],
        selectedTienda: null,
        isModalOpen: false,
        isEditMode: false,
        form: {
          id_inventario: null,
          id_producto: null,
          id_tienda: null,
          existencia: 0,
        },
      };
    },
    methods: {
        async fetchTodosInventarios() {
  try {
    const response = await axios.get('http://127.0.0.1:8000/inventario');
    this.inventario = response.data;
  } catch (error) {
    console.error("Error al obtener todo el inventario:", error);
  }
},
      async fetchProductos() {
        try {
          const response = await axios.get('http://127.0.0.1:8000/productos');
          this.productos = response.data;
        } catch (error) {
          console.error("Error al obtener los productos:", error);
        }
      },
      async fetchTiendas() {
        try {
          const response = await axios.get('http://127.0.0.1:8000/tiendas');
          this.tiendas = response.data;
        } catch (error) {
          console.error("Error al obtener las tiendas:", error);
        }
      },
      async fetchInventarioPorTienda() {
        try {
          if (!this.selectedTienda) return;
          
          const response = await axios.get(`http://127.0.0.1:8000/inventario/tienda/${this.selectedTienda}`);
          this.inventario = response.data;
        } catch (error) {
          console.error("Error al obtener el inventario:", error);
        }
      },
      openAddModal() {
        this.isModalOpen = true;
        this.isEditMode = false;
        this.resetForm();
      },
      openEditModal(item) {
        this.isModalOpen = true;
        this.isEditMode = true;
        this.form = { ...item };
      },
      closeModal() {
        this.isModalOpen = false;
        this.resetForm();
      },
      resetForm() {
        this.form = {
          id_inventario: null,
          id_producto: null,
          id_tienda: null,
          existencia: 0,
        };
      },
      async submitForm() {
        try {
          const payload = { ...this.form };
          if (this.isEditMode) {
            await axios.put(`http://127.0.0.1:8000/inventario/${this.form.id_inventario}`, payload);
          } else {
            await axios.post('http://127.0.0.1:8000/inventario', payload);
          }
          this.closeModal();
          this.fetchInventarioPorTienda(); // Refresca la lista de inventario
        } catch (error) {
          console.error("Error al insertar/actualizar el inventario:", error);
        }
      },
      async eliminarRegistro(item) {
        if (confirm("¿Estás seguro de que deseas eliminar este registro de inventario?")) {
          try {
            await axios.delete(`http://127.0.0.1:8000/inventario/${item.id_inventario}`);
            this.fetchInventarioPorTienda();
          } catch (error) {
            console.error("Error al eliminar el inventario:", error);
          }
        }
      },
    },
    created() {
      this.fetchProductos();
      this.fetchTiendas();
    },
  };
  </script>
  
  <style scoped>
  /* Estilos para la tabla y el modal */
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  img {
    border-radius: 5px;
  }
  
  button {
    margin: 10px;
    padding: 10px;
  }
  
  .modal {
    display: block;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }
  
  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 400px;
    border-radius: 5px;
  }
  
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    cursor: pointer;
  }
  </style>
  