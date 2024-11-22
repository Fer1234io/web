<template>
    <div class="descuentos-container">
      <h1>Gestión de Descuentos</h1>
  
      <!-- Botón para cambiar vista -->
      <div class="toggle-view">
        <button @click="toggleView">
          {{ showActive ? "Ver Descuentos Inactivos" : "Ver Descuentos Activos" }}
        </button>
      </div>
  
      <!-- Formulario para agregar/editar descuentos -->
      <div class="form-container">
        <h2 v-if="editMode">Editar Descuento</h2>
        <h2 v-else>Agregar Nuevo Descuento</h2>
        <form @submit.prevent="handleSubmit">
          <label for="nombre">Nombre:</label>
          <input type="text" id="nombre" v-model="form.nombre" placeholder="Nombre del descuento" required />
  
          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" v-model="form.descripcion" placeholder="Descripción del descuento"></textarea>
  
          <label for="porcentaje">Porcentaje:</label>
          <input type="number" id="porcentaje" v-model="form.porcentaje" min="0" max="100" placeholder="Porcentaje de descuento" required />
  
          <div class="form-buttons">
            <button type="submit" class="btn-primary">{{ editMode ? "Actualizar" : "Crear" }}</button>
            <button type="button" v-if="editMode" @click="resetForm" class="btn-secondary">Cancelar</button>
          </div>
        </form>
      </div>
  
      <!-- Listado de descuentos -->
      <div class="list-container">
        <h2 v-if="showActive">Descuentos Activos</h2>
        <h2 v-else>Descuentos Inactivos</h2>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Porcentaje</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="descuento in descuentos" :key="descuento.id_descuento">
              <td>{{ descuento.id_descuento }}</td>
              <td>{{ descuento.nombre }}</td>
              <td>{{ descuento.descripcion || "Sin descripción" }}</td>
              <td>{{ descuento.porcentaje }}%</td>
              <td class="actions">
                <button v-if="showActive" @click="editDescuento(descuento)" class="btn-edit">Editar</button>
                <button v-if="showActive" @click="desactivarDescuento(descuento.id_descuento)" class="btn-danger">Desactivar</button>
                <button v-else @click="reactivarDescuento(descuento.id_descuento)" class="btn-reactivate">Reactivar</button>
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
        descuentos: [], // Lista de descuentos (activos o inactivos)
        showActive: true, // Cambiar entre descuentos activos e inactivos
        form: {
          id_descuento: null,
          nombre: "",
          descripcion: "",
          porcentaje: null,
        },
        editMode: false, // Modo edición
      };
    },
    mounted() {
      this.fetchDescuentos();
    },
    methods: {
      // Obtener la lista de descuentos
      async fetchDescuentos() {
        const endpoint = this.showActive
          ? "http://127.0.0.1:8000/descuentos"
          : "http://127.0.0.1:8000/descuentos-inactivos";
  
        try {
          const response = await axios.get(endpoint);
          this.descuentos = response.data;
        } catch (error) {
          console.error("Error al cargar los descuentos:", error);
          alert("Ocurrió un error al cargar los descuentos.");
        }
      },
  
      // Enviar los datos del formulario (Crear o Actualizar)
      async handleSubmit() {
        try {
          if (this.editMode) {
            // Actualizar descuento
            const actualizarUrl = `http://127.0.0.1:8000/descuentos/${this.form.id_descuento}`;
            const formData = new FormData();
            formData.append("_method", "PUT");
            formData.append("nombre", this.form.nombre);
            formData.append("descripcion", this.form.descripcion);
            formData.append("porcentaje", this.form.porcentaje);
  
            await axios.post(actualizarUrl, formData);
            alert("Descuento actualizado con éxito");
          } else {
            // Crear nuevo descuento
            await axios.post("http://127.0.0.1:8000/descuentos", this.form);
            alert("Descuento creado con éxito");
          }
  
          this.resetForm();
          this.fetchDescuentos(); // Recargar la lista de descuentos
        } catch (error) {
          console.error("Error al guardar el descuento:", error);
          alert("Ocurrió un error al guardar el descuento.");
        }
      },
  
      // Editar un descuento (cargar datos al formulario)
      editDescuento(descuento) {
        this.form = { ...descuento };
        this.editMode = true;
      },
  
      // Desactivar descuento
      async desactivarDescuento(id_descuento) {
        if (confirm("¿Estás seguro de que deseas desactivar este descuento?")) {
          try {
            const eliminarUrl = `http://127.0.0.1:8000/descuentos/${id_descuento}`;
            const formData = new FormData();
            formData.append("_method", "DELETE");
  
            await axios.post(eliminarUrl, formData);
            alert("Descuento desactivado con éxito");
            this.fetchDescuentos();
          } catch (error) {
            console.error("Error al desactivar el descuento:", error);
            alert("Ocurrió un error al desactivar el descuento.");
          }
        }
      },
  
      // Reactivar descuento
      async reactivarDescuento(id_descuento) {
        if (confirm("¿Estás seguro de que deseas reactivar este descuento?")) {
          try {
            const reactivarUrl = `http://127.0.0.1:8000/descuentos-reactivar/${id_descuento}`;
            await axios.post(reactivarUrl);
            alert("Descuento reactivado con éxito");
            this.fetchDescuentos();
          } catch (error) {
            console.error("Error al reactivar el descuento:", error);
            alert("Ocurrió un error al reactivar el descuento.");
          }
        }
      },
  
      // Cambiar entre descuentos activos e inactivos
      toggleView() {
        this.showActive = !this.showActive;
        this.fetchDescuentos();
      },
  
      // Restablecer el formulario
      resetForm() {
        this.form = {
          id_descuento: null,
          nombre: "",
          descripcion: "",
          porcentaje: null,
        };
        this.editMode = false;
      },
    },
  };
  </script>
    
<style scoped>
/* Contenedor general */
.descuentos-container {
  padding: 20px;
  font-family: "Arial", sans-serif;
  background: linear-gradient(to right, #f9f9f9, #ffffff);
}

/* Encabezado */
h1,
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

/* Botón para cambiar vista */
.toggle-view {
  text-align: center;
  margin-bottom: 20px;
}

.toggle-view button {
  background-color: #007bff;
  color: white;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.toggle-view button:hover {
  background-color: #0056b3;
}

/* Formulario */
.form-container {
  margin: 0 auto 30px;
  max-width: 500px;
  background-color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  display: block;
  color: #555;
}

input,
textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 16px;
}

textarea {
  resize: none;
}

.form-buttons {
  display: flex;
  justify-content: space-between;
}

.btn-primary {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

.btn-primary:hover {
  background-color: #218838;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

/* Tabla */
.list-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th,
td {
  padding: 15px;
  text-align: left;
  border: 1px solid #ddd;
}

th {
  background-color: #007bff;
  color: white;
}

.actions button {
  margin-right: 5px;
  padding: 8px 12px;
  font-size: 14px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-edit {
  background-color: #ffc107;
  color: white;
}

.btn-edit:hover {
  background-color: #e0a800;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-danger:hover {
  background-color: #c82333;
}

.btn-reactivate {
  background-color: #17a2b8;
  color: white;
}

.btn-reactivate:hover {
  background-color: #138496;
}

/* Responsividad */
@media (max-width: 768px) {
  .form-container {
    width: 90%;
  }

  th,
  td {
    font-size: 14px;
  }

  .actions button {
    padding: 5px 10px;
    font-size: 12px;
  }
}
</style>