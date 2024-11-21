<template>
  <div class="user-management-container">
    <h1 class="title">Gestión de Usuarios</h1>

    <!-- Formulario para editar o agregar un usuario -->
    <div class="user-form">
      <h2>{{ id ? "Editar Usuario" : "Agregar Usuario" }}</h2>
      <form @submit.prevent="id ? enviarActualizar() : enviarInsertar()">
        <label>Nombre de Usuario:</label>
        <input type="text" v-model="nombre_usuario" required />

        <label>Correo:</label>
        <input type="email" v-model="correo" required />

        <label>Rol:</label>
        <select v-model="id_rol" required>
          <option v-for="rol in arrayRoles" :key="rol.id_rol" :value="rol.id_rol">
            {{ rol.nombre_rol }}
          </option>
        </select>

        <button type="submit" class="btn-submit">{{ id ? "Actualizar" : "Agregar" }}</button>
        <button type="button" class="btn-cancel" @click="limpiarFormulario">Cancelar</button>
      </form>
    </div>

    <!-- Tabla de usuarios -->
    <table class="user-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in arrayUsuarios" :key="usuario.id_usuario">
          <td>{{ usuario.nombre_usuario }}</td>
          <td>{{ usuario.correo }}</td>
          <td>{{ usuario.rol ? usuario.rol.nombre_rol : "No asignado" }}</td>
          <td>
            <span :class="usuario.estado === 1 ? 'status-active' : 'status-inactive'">
              {{ usuario.estado === 1 ? "Activo" : "Inactivo" }}
            </span>
          </td>
          <td class="actions">
            <button class="btn-edit" @click="Actualizar(usuario)">Editar</button>
            <button class="btn-delete" @click="Eliminar(usuario)">Eliminar</button>
            <button class="btn-confirm-delete" @click="enviarEliminar()" v-if="id === usuario.id_usuario">Confirmar</button>
            <button v-if="usuario.estado === 1" class="btn-toggle-status" @click="toggleEstado(usuario.id_usuario, 0)">Desactivar</button>
            <button v-else class="btn-toggle-status" @click="toggleEstado(usuario.id_usuario, 1)">Activar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      arrayUsuarios: [],
      arrayRoles: [],
      id: 0,
      nombre_usuario: '',
      correo: '',
      id_rol: null,
    };
  },
  mounted() {
    this.getUsuarios();
    this.getRoles();
  },
  methods: {
    // Obtener lista de usuarios
    getUsuarios() {
      axios.get('http://127.0.0.1:8000/usuarios')
        .then(response => {
          this.arrayUsuarios = response.data;
        })
        .catch(error => {
          console.error('Error al obtener usuarios:', error);
        });
    },
    // Obtener lista de roles
    getRoles() {
      axios.get('http://127.0.0.1:8000/roles')
        .then(response => {
          this.arrayRoles = response.data;
        })
        .catch(error => {
          console.error('Error al obtener roles:', error);
        });
    },
    // Preparar datos para editar un usuario
    Actualizar(usuario) {
      this.id = usuario.id_usuario;
      this.nombre_usuario = usuario.nombre_usuario;
      this.correo = usuario.correo;
      this.id_rol = usuario.id_rol;
    },
    // Limpiar el formulario después de la edición o inserción
    limpiarFormulario() {
      this.id = 0;
      this.nombre_usuario = '';
      this.correo = '';
      this.id_rol = null;
    },
    // Enviar actualización de usuario
    enviarActualizar() {
      const actualizarUrl = `http://127.0.0.1:8000/usuarios/${this.id}`;
      const formData = new FormData();
      formData.append('_method', 'PUT');
      formData.append('nombre_usuario', this.nombre_usuario);
      formData.append('correo', this.correo);
      formData.append('id_rol', this.id_rol);

      axios.post(actualizarUrl, formData)
        .then(response => {
          console.log("Usuario actualizado:", response);
          this.getUsuarios();
          this.limpiarFormulario();
        })
        .catch(error => {
          console.error("Error al actualizar usuario:", error);
        });
    },
    // Enviar solicitud para agregar un nuevo usuario
    enviarInsertar() {
      axios.post('http://127.0.0.1:8000/usuarios', {
        nombre_usuario: this.nombre_usuario,
        correo: this.correo,
        id_rol: this.id_rol
      })
        .then(response => {
          console.log("Usuario agregado:", response);
          this.getUsuarios();
          this.limpiarFormulario();
        })
        .catch(error => {
          console.error("Error al agregar usuario:", error);
        });
    },
    // Método para eliminar usuario
    Eliminar(usuario) {
      this.id = usuario.id_usuario;
    },
    enviarEliminar() {
      const eliminarUrl = `http://127.0.0.1:8000/usuarios/${this.id}`;
      axios.delete(eliminarUrl)
        .then(response => {
          console.log("Usuario eliminado:", response);
          this.getUsuarios();
          this.id = 0;
        })
        .catch(error => {
          console.error("Error al eliminar usuario:", error);
        });
    },
    // Método para alternar el estado del usuario
    toggleEstado(id, estado) {
      axios.put(`http://127.0.0.1:8000/api/usuarios/${id}/toggle-estado`, { estado })
        .then(response => {
          console.log(response.data);
          this.getUsuarios();
        })
        .catch(error => {
          console.error("Error al cambiar el estado del usuario:", error);
        });
    },
  }
};
</script>

<style scoped>
.user-management-container {
  padding: 20px;
  font-family: Arial, sans-serif;
  background-color: #f9f9f9;
  border-radius: 8px;
  max-width: 900px;
  margin: auto;
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.user-form {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.user-form h2 {
  margin-bottom: 15px;
}

.user-form label {
  display: block;
  margin-top: 10px;
}

.user-form input, .user-form select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.btn-submit {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 4px;
  margin-right: 10px;
}

.btn-cancel {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 4px;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th, .user-table td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}

.user-table th {
  background-color: #4CAF50;
  color: white;
}

.status-active {
  color: #4CAF50;
  font-weight: bold;
}

.status-inactive {
  color: #e74c3c;
  font-weight: bold;
}
</style>
