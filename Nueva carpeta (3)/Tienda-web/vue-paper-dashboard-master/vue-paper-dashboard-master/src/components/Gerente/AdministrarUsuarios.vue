<template>
  <div>
    <h1>Gestión de Usuarios</h1>
    <button @click="openAddModal" class="btn btn-primary">Agregar Usuario</button>

    <table v-if="usuarios.length > 0" class="user-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Teléfono</th>
          <th>Nombre de Usuario</th>
          <th>Dirección</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in usuarios" :key="usuario.id_usuario">
          <td>{{ usuario.nombre }} {{ usuario.apellido }}</td>
          <td>{{ usuario.correo }}</td>
          <td>{{ usuario.numero_telefono }}</td>
          <td>{{ usuario.nombre_usuario }}</td>
          <td>{{ usuario.direccion }}</td>
          <td>{{ usuario.rol ? usuario.rol.nombre_rol : "Sin rol" }}</td>
          <td>
            <button @click="openEditModal(usuario)" class="btn btn-edit">
              Editar
            </button>
            <button @click="eliminarUsuario(usuario)" class="btn btn-delete">
              Eliminar
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else>No hay usuarios disponibles.</p>

    <!-- Modal para agregar/editar usuario -->
    <div v-if="isModalOpen" class="modal">
  <div class="modal-content">
    <span @click="closeModal" class="close">&times;</span>
    <h2>{{ isEditMode ? "Editar Usuario" : "Agregar Usuario" }}</h2>
    <form @submit.prevent="submitForm" class="form-container">
      <div class="form-row">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" v-model="form.nombre" placeholder="Ingrese el nombre" required />
        </div>
        <div class="form-group">
          <label for="apellido">Apellido</label>
          <input type="text" v-model="form.apellido" placeholder="Ingrese el apellido" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
    <label for="correo">Correo</label>
    <input 
      type="email" 
      v-model="form.correo" 
      placeholder="ejemplo@correo.com" 
      @input="validateEmail" 
      required 
    />
    <small v-if="emailError" class="error-message">Ingrese un correo válido.</small>
  </div>
        <div class="form-group">
    <label for="numero_telefono">Teléfono</label>
    <input 
      type="text" 
      v-model="form.numero_telefono" 
      maxlength="8" 
      placeholder="Ingrese el número de teléfono" 
      @input="validatePhoneNumber"
      required 
    />
    <small v-if="phoneError" class="error-message">Solo se permiten números de hasta 8 dígitos.</small>
  </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="nombre_usuario">Nombre de Usuario</label>
          <input type="text" v-model="form.nombre_usuario" placeholder="Ingrese el nombre de usuario" required />
        </div>
        <div class="form-group">
          <label for="direccion">Dirección</label>
          <input type="text" v-model="form.direccion" placeholder="Ingrese la dirección" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" v-if="!isEditMode">
          <label for="id_rol">Rol</label>
          <select v-model="form.id_rol" required>
            <option v-for="rol in roles" :key="rol.id_rol" :value="rol.id_rol">
              {{ rol.nombre_rol }}
            </option>
          </select>
        </div>
        <div class="form-group" v-if="!isEditMode">
          <label for="contraseña">Contraseña</label>
          <input type="password" v-model="form.contraseña" placeholder="Ingrese la contraseña" required />
        </div>
      </div>

      <button type="submit" class="btn btn-submit">
        {{ isEditMode ? "Actualizar" : "Guardar" }}
      </button>
    </form>
  </div>
</div>


    <!-- Modal de error -->
<!-- Modal de error -->
<div v-if="errorModal" class="modal">
  <div class="modal-content error">
    <span @click="closeErrorModal" class="close">&times;</span>
    <h2>Error</h2>
    <p>{{ errorMessage }}</p>
    <button @click="closeErrorModal">Cerrar</button>
  </div>
</div>

  </div>
</template>

  
<script>
import axios from "axios";

export default {
  data() {
    return {
      usuarios: [],
      roles: [],
      errorModal: false,
      errorMessage: "",
      emailError: false,
      phoneError: false,
      passwordError: false,
      isModalOpen: false,
      isEditMode: false,
      form: {
        id_usuario: null,
        nombre: "",
        apellido: "",
        correo: "",
        numero_telefono: "",
        nombre_usuario: "",
        direccion: "",
        id_rol: null,
        contraseña: "",
        confirmar_contraseña: "", // Nuevo campo para confirmar contraseña
        imagen: "/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAJQApQMBIgACEQEDEQH/xAAaAAEAAwEBAQAAAAAAAAAAAAAAAQQFAgMH/8QALhABAAIBAgMGBQQDAAAAAAAAAAECAwQREiExBTJBUWFxEzNykbEiU2KhFEJS/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAEC/8QAFhEBAQEAAAAAAAAAAAAAAAAAAAER/9oADAMBAAIRAxEAPwD6oA0wAAAAAADqtZtO1YmZ8oe9dFmt1iI95DFYW50GXwmv3eV9NmpztSdvOOaaY8QFAAAAAAAAAAAAAAErGl005p4rcqfl56fDObLw78o5zPo1q1isRERtEIsiMeOmONqViI9HYI0AAr59NTLziNrecMzJjtjtNbxtLbV9Xg+NjnbleOkrEsZQlCsgAAAAAAAAAAANLs6kRjm+3elbeGj5aens92WoACgAAAMnV04M9uXXm8FztH5lJ/iptRigAAAAAAAAAAJBqaG3Fp6+m8LDN0GXhvNJ/wBvy0ma1AAUAABze0UrNpnlHMGd2haJzxH/ADVVdXtN72vPjzctMAAAAAAAAAAAAJ9p2aWl1MZIil52v+WaJhG4M7Drr1jbJHFHn4rNNZht1tw/VCNasDx/ysP7lXlk12OO5Frf1Aq1aYrEzM7RHizdZqPjTw07kf2882oyZuVp/T5Q8lxm1ACoAAAAAAAAAAA6rWbTtWN58oByLmLQ2nnknh9IWqaTDTpTf35pq4yoiZ6RM+zuMWSemO32bEViOkbeyTVxjfAy/t2+yJx3jrS32bSNjTGGNq2Ol4/VWJ94eGTQ4rd3es+hqYzBYy6XLjjfbijzhXVAAAAAAAABKFnSYPjW3nuR19SiNPprZp3nlTzaWPFTHXasbOqxFY2jlEJZaABQAAAAABW1Gkrljeu1befmsoBjZMdsduG0bS5a2ow1zU2nlMdJZWSs0vNLRzhWccgKgAAADqtZtaKx4zs2MVIx0iseEM7QV4tRE7d2N2olWACNAAAAAAAAAAI8VPtDFvSMkda9fZdcZaRfHau3WAYyEz1mENMAAAALnZvzb/S0Wd2b82/0tFlqAAoAAAAAAAAAAADDt3p90Jt3p90NRgAAABc7N+bf6WiDLUABQAAAAAAAAAAAGHbvT7oBqMAAP//Z", // Imagen predeterminada
      },
    };
  },
  methods: {
    async fetchUsuarios() {
      try {
        const response = await axios.get("http://127.0.0.1:8000/usuarios");
        this.usuarios = response.data;
      } catch (error) {
        console.error("Error al obtener los usuarios:", error);
      }
    },
    async fetchRoles() {
      try {
        const response = await axios.get("http://127.0.0.1:8000/roles");
        this.roles = response.data;
      } catch (error) {
        console.error("Error al obtener los roles:", error);
      }
    },
    openAddModal() {
      this.isModalOpen = true;
      this.isEditMode = false;
      this.resetForm();
    },
    openEditModal(usuario) {
      this.isModalOpen = true;
      this.isEditMode = true;
      this.form = {
        ...usuario,
        contraseña: "", // No mostrar la contraseña
        confirmar_contraseña: "", // Reiniciar confirmación de contraseña
        id_rol: null, // Rol no editable
      };
    },
    closeModal() {
      this.isModalOpen = false;
      this.resetForm();
    },
    closeErrorModal() {
      this.errorModal = false;
      this.errorMessage = "";
    },
    resetForm() {
      this.form = {
        id_usuario: null,
        nombre: "",
        apellido: "",
        correo: "",
        numero_telefono: "",
        nombre_usuario: "",
        direccion: "",
        id_rol: null,
        contraseña: "",
        confirmar_contraseña: "", // Resetear confirmación de contraseña
        imagen: "/9j/4AAQSkZJRgABAQAAAQABAAD/...", // Imagen predeterminada
      };
      this.emailError = false;
      this.phoneError = false;
      this.passwordError = false;
    },
    validateEmail() {
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      this.emailError = !emailRegex.test(this.form.correo);
    },
    validatePhoneNumber() {
      const phoneRegex = /^[0-9]{1,8}$/;
      this.phoneError = !phoneRegex.test(this.form.numero_telefono);
    },
    validatePasswords() {
      this.passwordError =
        this.form.contraseña && this.form.contraseña !== this.form.confirmar_contraseña;
    },
    async submitForm() {
      this.validateEmail();
      this.validatePhoneNumber();
      this.validatePasswords();

      if (this.emailError || this.phoneError || this.passwordError) {
        this.errorMessage = "Por favor, corrija los errores antes de continuar.";
        this.errorModal = true;
        return;
      }

      try {
        const url = this.isEditMode
          ? `http://127.0.0.1:8000/usuarios/${this.form.id_usuario}`
          : "http://127.0.0.1:8000/usuarios";

        const method = this.isEditMode ? "PUT" : "POST";

        await axios.post(url, this.form, {
          headers: { "Content-Type": "application/json" },
          params: { _method: method },
        });

        this.closeModal();
        this.fetchUsuarios();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorMessage =
            error.response.data.errors?.correo?.[0] ||
            "Ocurrió un error de validación.";
          this.errorModal = true;
        } else {
          console.error("Error al guardar el usuario:", error);
        }
      }
    },
    async eliminarUsuario(usuario) {
      if (confirm("¿Estás seguro de que deseas desactivar este usuario?")) {
        try {
          const url = `http://127.0.0.1:8000/usuarios/${usuario.id_usuario}`;
          await axios.post(
            url,
            { _method: "DELETE" },
            { headers: { "Content-Type": "application/json" } }
          );
          this.fetchUsuarios();
        } catch (error) {
          console.error("Error al desactivar el usuario:", error);
        }
      }
    },
  },
  created() {
    this.fetchUsuarios();
    this.fetchRoles();
  },
};
</script>

  
<style scoped>
/* Estilos básicos */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f9f9f9;
  color: #333;
}

button {
  margin: 5px;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 6px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

button.btn-edit {
  background-color: #007bff;
  color: white;
}

button.btn-edit:hover {
  background-color: #0056b3;
}

button.btn-delete {
  background-color: #dc3545;
  color: white;
}

button.btn-delete:hover {
  background-color: #a71d2a;
}

button.btn-primary {
  background-color: #4caf50;
  color: white;
}

button.btn-primary:hover {
  background-color: #45a049;
}

/* Fondo del modal */
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
  background-color: rgba(0, 0, 0, 0.6); /* Fondo oscuro con transparencia */
}

/* Contenido del modal */
.modal-content {
  background: #fff;
  padding: 30px 20px;
  width: 90%;
  max-width: 500px;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease-in-out;
  position: relative;
}

/* Botón de cerrar */
.close {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 20px;
  color: #555;
  cursor: pointer;
  transition: color 0.3s ease;
}

.close:hover {
  color: #000;
}

/* Título del modal */
.modal-content h2 {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #333;
  text-align: center;
}

/* Texto en el modal */
.modal-content p {
  font-size: 16px;
  color: #555;
  text-align: center;
  line-height: 1.5;
}

/* Botón de acción en el modal */
.modal-content button {
  display: block;
  width: 100%;
  padding: 12px;
  font-size: 16px;
  font-weight: bold;
  color: #fff;
  background-color: #4caf50; /* Verde atractivo */
  border: none;
  border-radius: 8px;
  margin-top: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.modal-content button:hover {
  background-color: #45a049;
}

/* Animación de aparición del modal */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Estilos específicos para el modal de error */
.modal-content.error {
  border-left: 5px solid #f44336; /* Rojo */
}

.modal-content.error h2 {
  color: #f44336;
}

.modal-content.error button {
  background-color: #f44336; /* Rojo */
}

.modal-content.error button:hover {
  background-color: #d32f2f; /* Rojo más oscuro */
}

/* Formulario */
.form-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Fila de formularios */
.form-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

/* Estilo de cada grupo de formularios */
.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Etiquetas */
.form-group label {
  font-size: 14px;
  font-weight: bold;
  color: #333;
}

/* Campos de entrada */
.form-group input,
.form-group select {
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  outline: none;
  transition: border-color 0.3s ease;
}

/* Hover en los campos de entrada */
.form-group input:focus,
.form-group select:focus {
  border-color: #4caf50;
}

/* Mensajes de error */
.error-message {
  font-size: 12px;
  color: #f44336;
  margin-top: 5px;
}

/* Botón del formulario */
.btn-submit {
  background-color: #4caf50;
  color: white;
  padding: 12px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  align-self: flex-end;
}

.btn-submit:hover {
  background-color: #45a049;
}
</style>
