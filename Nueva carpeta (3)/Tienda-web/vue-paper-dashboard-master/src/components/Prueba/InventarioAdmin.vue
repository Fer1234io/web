<template>
  <div class="inventory-container">
    <h1 class="title">Gestión de Inventario</h1>

    <!-- Filtro de tienda -->
    <div class="filter-container">
      <div class="filter-group">
        <label for="tienda" class="filter-label">Filtrar por tienda:</label>
        <select v-model="selectedTienda" @change="fetchInventario" class="filter-select" id="tienda">
          <option value="">Mostrar Todo el Inventario</option>
          <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
            {{ tienda.nombre_tienda }}
          </option>
        </select>
      </div>
      <button @click="openAddModal" class="btn btn-primary">Agregar Registro de Inventario</button>
    </div>

    <table v-if="inventario.length > 0" class="inventory-table">
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
            <img :src="'data:image/jpeg;base64,' + item.producto.fotografia_url" alt="Imagen del producto" class="product-image" />
          </td>
          <td>{{ item.tienda.nombre_tienda }}</td>
          <td>{{ item.existencia }}</td>
          <td>
            <button @click="confirmEdit(item)" class="btn btn-edit">Editar</button>
            <button @click="confirmDelete(item)" class="btn btn-delete">Eliminar</button>
            <button @click="openTransferModal(item)" class="btn btn-transfer">Trasladar</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else class="no-data-message">No hay registros de inventario disponibles.</p>

    <!-- Modal para agregar/editar inventario -->
    <div v-if="isModalOpen" class="modal">
      <div class="modal-content">
        <span @click="closeModal" class="close">&times;</span>
        <h2 class="modal-title">{{ isEditMode ? 'Editar Registro de Inventario' : 'Agregar Registro de Inventario' }}</h2>

        <form @submit.prevent="submitForm" class="form-container">
          <div class="form-group">
            <label for="producto">Seleccionar producto:</label>
            <select v-model="form.id_producto" @change="updateProductPreview" required class="form-select" id="producto">
              <option value="" disabled>Seleccione un producto</option>
              <option v-for="producto in productos" :key="producto.id_producto" :value="producto.id_producto">
                {{ producto.nombre_producto }}
              </option>
            </select>
          </div>
          <div class="product-preview" v-if="selectedProduct">
            <h3>Previsualización del Producto</h3>
            <img :src="'data:image/jpeg;base64,' + selectedProduct.fotografia_url" alt="Previsualización del producto" class="preview-image" />
            <p><strong>Modelo:</strong> {{ selectedProduct.modelo }}</p>
            <p><strong>Marca:</strong> {{ selectedProduct.marca }}</p>
          </div>
          <div class="form-group" v-if="!isEditMode">
            <label for="tienda">Seleccionar tienda:</label>
            <select v-model="form.id_tienda" required class="form-select" id="tienda">
              <option value="" disabled>Seleccione una tienda</option>
              <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
                {{ tienda.nombre_tienda }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="existencia">Cantidad:</label>
            <input 
              type="number" 
              v-model="form.existencia" 
              required 
              class="form-input" 
              id="existencia" 
              :min="1" 
              placeholder="Ingrese la cantidad"
            />
          </div>
          <button type="submit" class="btn btn-submit">{{ isEditMode ? 'Actualizar' : 'Guardar' }}</button>
        </form>
      </div>
    </div>

    <!-- Modal de Error -->
    <div v-if="errorModal" class="modal">
      <div class="modal-content error">
        <span @click="closeErrorModal" class="close">&times;</span>
        <h2>Error</h2>
        <p>{{ errorMessage }}</p>
        <button class="btn btn-close" @click="closeErrorModal">Cerrar</button>
      </div>
    </div>

    <!-- Modal para traslado de inventario -->
    <div v-if="isTransferModalOpen" class="modal">
      <div class="modal-content">
        <span @click="closeTransferModal" class="close">&times;</span>
        <h2 class="modal-title">Traslado de Producto</h2>

        <form @submit.prevent="submitTransfer" class="form-container">
          <div class="form-group">
            <label>Producto:</label>
            <p class="read-only">{{ transferForm.productoNombre }}</p>
          </div>
          <div class="form-group">
            <label>Tienda Origen:</label>
            <p class="read-only">{{ transferForm.tiendaOrigenNombre }}</p>
          </div>
          <div class="form-group">
            <label for="tienda_destino">Seleccionar tienda destino:</label>
            <select v-model="transferForm.tiendaDestino" required class="form-select" id="tienda_destino">
              <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda" :disabled="tienda.id_tienda === transferForm.tiendaOrigen">
                {{ tienda.nombre_tienda }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="cantidad">Cantidad a trasladar:</label>
            <input 
              type="number" 
              v-model="transferForm.cantidad" 
              :max="transferForm.maxCantidad" 
              required 
              class="form-input" 
              id="cantidad" 
              :min="1" 
              placeholder="Ingrese la cantidad"
            />
            <p v-if="transferForm.cantidad > transferForm.maxCantidad" class="error-message">
              La cantidad no puede ser mayor a {{ transferForm.maxCantidad }}.
            </p>
          </div>
          <div class="form-group">
            <label for="estado">Estado:</label>
            <select v-model="transferForm.estado" required class="form-select" id="estado">
              <option value="En Camino">En Camino</option>
              <option value="Entregado">Entregado</option>
            </select>
          </div>
          <button type="submit" class="btn btn-submit">Confirmar Traslado</button>
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
      isTransferModalOpen: false,
      errorModal: false, // Mostrar modal de error
      errorMessage: "", // Mensaje de error
      form: {
        id_inventario: null,
        id_producto: null,
        id_tienda: null,
        existencia: 1,
      },
      selectedProduct: null, // Producto seleccionado para previsualización
      transferForm: {
        id_inventario: null,
        productoNombre: '',
        tiendaOrigen: null,
        tiendaOrigenNombre: '',
        tiendaDestino: '',
        cantidad: 1,
        maxCantidad: 1,
        estado: 'En Camino', // Valor fijo
        fechaTraslado: new Date().toISOString().split('T')[0], // Fecha actual
        fechaRecibido: null,
      },
    };
  },
  methods: {
    async fetchInventario() {
      try {
        let url = 'http://127.0.0.1:8000/inventario';
        if (this.selectedTienda) {
          url = `http://127.0.0.1:8000/inventario/tienda/${this.selectedTienda}`;
        }
        const response = await axios.get(url);
        this.inventario = response.data;
      } catch (error) {
        console.error("Error al obtener el inventario:", error);
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
    openAddModal() {
      this.isModalOpen = true;
      this.isEditMode = false;
      this.errorMessage = "";
      this.resetForm();
    },
    updateProductPreview() {
      // Buscar detalles del producto seleccionado
      this.selectedProduct = this.productos.find(
        (producto) => producto.id_producto === this.form.id_producto
      );
    },
    confirmEdit(item) {
      if (confirm("¿Deseas editar este registro?")) {
        this.openEditModal(item);
      }
    },
    openEditModal(item) {
      this.isModalOpen = true;
      this.isEditMode = true;
      this.errorMessage = "";
      this.form = { ...item };
      this.updateProductPreview();
    },
    confirmDelete(item) {
      if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
        this.eliminarRegistro(item);
      }
    },
    async eliminarRegistro(item) {
      try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        await axios.post(`http://127.0.0.1:8000/inventario/${item.id_inventario}`, formData);
        this.fetchInventario();
      } catch (error) {
        console.error("Error al eliminar el inventario:", error);
      }
    },
    closeModal() {
      this.isModalOpen = false;
      this.errorMessage = "";
      this.selectedProduct = null; // Resetear previsualización
      this.resetForm();
    },
    resetForm() {
      this.form = {
        id_inventario: null,
        id_producto: null,
        id_tienda: null,
        existencia: 1,
      };
      this.selectedProduct = null;
    },
    async submitForm() {
      try {
        const formData = new FormData();
        formData.append("id_producto", this.form.id_producto);
        formData.append("existencia", this.form.existencia);

        if (this.isEditMode) {
          formData.append("_method", "PUT");
          await axios.post(`http://127.0.0.1:8000/inventario/update/${this.form.id_inventario}`, formData);
        } else {
          formData.append("id_tienda", this.form.id_tienda);
          await axios.post("http://127.0.0.1:8000/inventario", formData);
        }

        this.closeModal();
        this.fetchInventario();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Validación personalizada del servidor
          this.errorMessage = error.response.data.error || "Este producto ya tiene un registro en esta sucursal.";
          this.errorModal = true;
        } else {
          console.error("Error en la solicitud:", error);
          this.errorMessage = "Error al guardar el inventario.";
          this.errorModal = true;
        }
      }
    },
    openTransferModal(item) {
      this.isTransferModalOpen = true;
      this.transferForm = {
        id_inventario: item.id_inventario,
        productoNombre: item.producto.nombre_producto,
        tiendaOrigen: item.tienda.id_tienda,
        tiendaOrigenNombre: item.tienda.nombre_tienda,
        tiendaDestino: '',
        cantidad: 1,
        maxCantidad: item.existencia,
        estado: 'En Camino',
        fechaTraslado: new Date().toISOString().split('T')[0], // Fecha actual
        fechaRecibido: null,
      };
    },
    closeTransferModal() {
      this.isTransferModalOpen = false;
      this.resetTransferForm();
    },
    resetTransferForm() {
      this.transferForm = {
        id_inventario: null,
        productoNombre: '',
        tiendaOrigen: null,
        tiendaOrigenNombre: '',
        tiendaDestino: '',
        cantidad: 1,
        maxCantidad: 1,
        estado: 'En Camino',
      };
    },
    async submitTransfer() {
      if (this.transferForm.cantidad > this.transferForm.maxCantidad) {
        alert(`La cantidad no puede ser mayor a ${this.transferForm.maxCantidad}.`);
        return;
      }

      try {
        const transferUrl = 'http://127.0.0.1:8000/traslados';
        const formData = new FormData();
        formData.append('id_inventario', this.transferForm.id_inventario);
        formData.append('tienda_origen', this.transferForm.tiendaOrigen);
        formData.append('tienda_destino', this.transferForm.tiendaDestino);
        formData.append('cantidad', this.transferForm.cantidad);
        formData.append('estado', 'En Camino'); // Fijar estado en "En Camino"
        formData.append('fecha_traslado', this.transferForm.fechaTraslado);
        formData.append('fecha_recibido', null);

        await axios.post(transferUrl, formData);
        alert("Traslado registrado exitosamente");
        this.closeTransferModal();
        this.fetchInventario();
      } catch (error) {
        console.error("Error al registrar el traslado:", error);
        alert("Ocurrió un error al registrar el traslado. Intente de nuevo.");
      }
    },
    closeErrorModal() {
      this.errorModal = false;
      this.errorMessage = "";
    },
  },
  created() {
    this.fetchProductos();
    this.fetchTiendas();
    this.fetchInventario();
  },
};
</script>

<style scoped>
/* Contenedor general */
.inventory-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

/* Título */
.title {
  font-size: 28px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 20px;
}

/* Filtro */
.filter-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.filter-label {
  font-weight: bold;
  margin-right: 10px;
}

.filter-select {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

/* Tabla */
.inventory-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.inventory-table th,
.inventory-table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.inventory-table th {
  background-color: #f5f5f5;
}

.product-image {
  width: 80px;
  height: auto;
  border-radius: 5px;
}

/* Botones */
.btn {
  margin: 5px;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary {
  background-color: #4caf50;
  color: white;
}

.btn-primary:hover {
  background-color: #45a049;
}

.btn-edit {
  background-color: #007bff;
  color: white;
}

.btn-edit:hover {
  background-color: #0056b3;
}

.btn-delete {
  background-color: #dc3545;
  color: white;
}

.btn-delete:hover {
  background-color: #a71d2a;
}

.btn-transfer {
  background-color: #ffc107;
  color: white;
}

.btn-transfer:hover {
  background-color: #e0a800;
}

/* Modal */
.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
}

.modal-content {
  background: white;
  padding: 20px;
  width: 90%;
  max-width: 600px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  position: relative;
}

.modal-title {
  text-align: center;
  margin-bottom: 20px;
  font-size: 20px;
  font-weight: bold;
}

/* Previsualización del producto */
.product-preview {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  background-color: #f9f9f9;
}

.product-preview img {
  width: 120px;
  height: auto;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.product-preview .product-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-preview .product-details span {
  font-size: 16px;
  color: #333;
  font-weight: 500;
}

.product-preview .product-details span.label {
  font-size: 14px;
  color: #888;
  font-weight: normal;
}

/* Formulario */
.form-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-input,
.form-select {
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.form-input:focus,
.form-select:focus {
  border-color: #4caf50;
}

.error-message {
  font-size: 12px;
  color: #dc3545;
}

.btn-submit {
  background-color: #4caf50;
  color: white;
  align-self: flex-end;
}

.btn-submit:hover {
  background-color: #45a049;
}

/* Responsivo */
@media (max-width: 768px) {
  .filter-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .form-container {
    gap: 10px;
  }

  .product-preview {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .product-preview img {
    width: 100px;
  }
}

/* Modal de error */
.modal-content.error {
  border-left: 5px solid #dc3545; /* Rojo */
}

.modal-content.error h2 {
  color: #dc3545;
}

.modal-content.error button {
  background-color: #dc3545; /* Rojo */
}

.modal-content.error button:hover {
  background-color: #a71d2a; /* Rojo más oscuro */
}
</style>



