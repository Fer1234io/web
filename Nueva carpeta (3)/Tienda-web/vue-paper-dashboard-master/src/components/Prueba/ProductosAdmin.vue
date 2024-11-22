<template>
  <div>
    <h1>Lista de Productos</h1>
    <button @click="openAddModal" class="btn btn-primary">Agregar Producto</button>

    <table v-if="productos.length > 0" class="product-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Marca</th>
          <th>Modelo</th>
          <th>Precio</th>
          <th>Descuento</th>
          <th>Categoría</th>
          <th>Imagen</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="producto in productos" :key="producto.id_producto">
          <td>{{ producto.nombre_producto }}</td>
          <td>{{ producto.descripcion }}</td>
          <td>{{ producto.marca }}</td>
          <td>{{ producto.modelo }}</td>
          <td>Q {{ producto.precio }}</td>
          <td>{{ producto.descuento ? producto.descuento.nombre : 'Sin descuento' }}</td>
          <td>{{ producto.categoria ? producto.categoria.nombre_categoria : 'Sin categoría' }}</td>
          <td>
            <img :src="'data:image/jpeg;base64,' + producto.fotografia_url" alt="Imagen del producto" class="product-image" />
          </td>
          <td>
            <button @click="openEditModal(producto)" class="btn btn-edit">Editar</button>
            <button @click="eliminarProducto(producto)" class="btn btn-delete">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else>No hay productos disponibles.</p>

    <!-- Modal para agregar/editar producto -->
    <div v-if="isModalOpen" class="modal">
      <div class="modal-content">
        <span @click="closeModal" class="close">&times;</span>
        <h2>{{ isEditMode ? 'Editar Producto' : 'Agregar Producto' }}</h2>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="nombre_producto">Nombre:</label>
            <input type="text" v-model="form.nombre_producto" required />
          </div>

          <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" v-model="form.descripcion" required />
          </div>

          <div class="form-group">
            <label for="marca">Marca:</label>
            <input type="text" v-model="form.marca" required />
          </div>

          <div class="form-group">
            <label for="modelo">Modelo:</label>
            <input type="text" v-model="form.modelo" required />
          </div>

          <div class="form-group">
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" v-model="form.precio" required />
          </div>
          <!-- Añadir un campo de selección en el formulario del modal -->
          <div class="form-group">
            <label for="id_descuento">Descuento:</label>
            <select v-model="form.id_descuento" class="form-select">
              <option value="">Sin descuento</option>
              <option v-for="descuento in descuentos" :key="descuento.id_descuento" :value="descuento.id_descuento">
                {{ descuento.nombre }}
              </option>
              </select>
            </div>

          <div class="form-group">
            <label for="id_categoria">Categoría:</label>
            <select v-model="form.id_categoria" required>
              <option v-for="categoria in categorias" :key="categoria.id_categoria" :value="categoria.id_categoria">
                {{ categoria.nombre_categoria }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="fotografia_url">Imagen:</label>
            <input type="file" @change="handleImageUpload" />
            <img v-if="form.fotografia_url" :src="'data:image/jpeg;base64,' + form.fotografia_url" alt="Vista previa de imagen" class="image-preview" />
          </div>

          <button type="submit" class="btn btn-submit">{{ isEditMode ? 'Actualizar' : 'Guardar' }}</button>
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
      categorias: [],
      descuentos: [], // Lista de descuentos
      isModalOpen: false,
      isEditMode: false,
      form: {
        id_producto: null,
        nombre_producto: '',
        descripcion: '',
        marca: '',
        modelo: '',
        precio: '',
        id_categoria: '',
        id_descuento: '', // Nuevo campo
        fotografia_url: ''
      },
    };
  },
  methods: {
    async fetchProductos() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/productos');
        this.productos = response.data;
      } catch (error) {
        console.error("Error al obtener los productos:", error);
      }
    },
    async fetchDescuentos() {
    try {
      const response = await axios.get('http://127.0.0.1:8000/descuentos');
      this.descuentos = response.data;
    } catch (error) {
      console.error("Error al obtener los descuentos:", error);
    }
  },
    async fetchCategorias() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/categorias');
        this.categorias = response.data;
      } catch (error) {
        console.error("Error al obtener las categorías:", error);
      }
    },
    openAddModal() {
      this.isModalOpen = true;
      this.isEditMode = false;
      this.resetForm();
    },
    openEditModal(producto) {
      this.isModalOpen = true;
      this.isEditMode = true;
      this.form = { ...producto };
    },
    closeModal() {
      this.isModalOpen = false;
      this.resetForm();
    },
    resetForm() {
    this.form = {
      id_producto: null,
      nombre_producto: '',
      descripcion: '',
      marca: '',
      modelo: '',
      precio: '',
      id_categoria: '',
      fotografia_url: '',
      id_descuento: '' // Resetear el descuento
    };
  },
    handleImageUpload(event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.form.fotografia_url = e.target.result.split(',')[1]; // Guardar solo la parte base64
      };

      reader.readAsDataURL(file);
    },
    async submitForm() {
  try {
    const formData = new FormData();
    formData.append('_method', this.isEditMode ? 'PUT' : 'POST'); // Simula el método HTTP para Laravel
    formData.append('nombre_producto', this.form.nombre_producto);
    formData.append('descripcion', this.form.descripcion);
    formData.append('marca', this.form.marca);
    formData.append('modelo', this.form.modelo);
    formData.append('precio', this.form.precio);
    formData.append('id_categoria', this.form.id_categoria);
    formData.append('id_descuento', this.form.id_descuento);
    
    if (this.form.fotografia_url) {
      formData.append('fotografia_url', this.form.fotografia_url); // Base64 data
    }

    const url = this.isEditMode 
      ? `http://127.0.0.1:8000/productos/${this.form.id_producto}`
      : 'http://127.0.0.1:8000/productos';

    // Usa POST en ambos casos y Laravel detectará el _method para manejarlo como PUT
    await axios.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    this.closeModal();
    this.fetchProductos(); // Refresca la lista de productos
  } catch (error) {
    console.error("Error al guardar el producto:", error);
  }
},
async eliminarProducto(producto) {
      if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        const eliminarUrl = `http://127.0.0.1:8000/productos/${producto.id_producto}`;
        const formData = new FormData();
        formData.append('_method', 'DELETE');

        try {
          await axios.post(eliminarUrl, formData);
          this.fetchProductos(); // Refresca la lista de productos
        } catch (error) {
          console.error("Error al eliminar el producto:", error);
        }
      }
    },
  

  },
  created() {
    this.fetchProductos();
    this.fetchCategorias();
    this.fetchDescuentos(); 
  },
};
</script>

<style scoped>
/* Estilos para la tabla */
.product-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.product-table th, .product-table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.product-table th {
  background-color: #f2f2f2;
  color: #333;
}

.product-image {
  width: 80px;
  height: auto;
  border-radius: 5px;
  object-fit: cover;
}

/* Estilos de los botones */
.btn {
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin: 5px;
  transition: background-color 0.3s ease;
}

.btn-primary {
  background-color: #4CAF50;
  color: white;
}

.btn-primary:hover {
  background-color: #45a049;
}

.btn-edit {
  background-color: #2196F3;
  color: white;
}

.btn-edit:hover {
  background-color: #1976D2;
}

.btn-delete {
  background-color: #f44336;
  color: white;
}

.btn-delete:hover {
  background-color: #d32f2f;
}

.btn-submit {
  background-color: #2196F3;
  color: white;
}

.btn-submit:hover {
  background-color: #1976D2;
}

/* Estilos del modal */
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
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 90%;
  max-width: 500px;
  max-height: 80vh; /* Limitar la altura máxima */
  overflow-y: auto; /* Permitir desplazamiento vertical */
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  cursor: pointer;
}

.close:hover, .close:focus {
  color: black;
  text-decoration: none;
}

/* Estilos de los campos del formulario */
.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.image-preview {
  display: block;
  margin-top: 10px;
  width: 100px;
  height: auto;
  border: 1px solid #ddd;
  border-radius: 5px;
}
</style>

