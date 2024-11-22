<template>
    <div>
      <h1>Administrar Roles de Usuarios</h1>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol Actual</th>
            <th>Actualizar Rol</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.id_usuario">
            <td>{{ usuario.nombre }} {{ usuario.apellido }}</td>
            <td>{{ usuario.correo }}</td>
            <td>{{ usuario.rol.nombre_rol || "Sin Rol" }}</td>
            <td>
              <select v-model="usuario.id_rol">
                <option v-for="rol in roles" :key="rol.id_rol" :value="rol.id_rol">
                  {{ rol.nombre_rol }}
                </option>
              </select>
              <button
                @click="enviarActualizar(usuario)"
                :disabled="usuario.rol.id_rol === usuario.id_rol"
              >
                Actualizar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        usuarios: [], // Lista de usuarios
        roles: [], // Lista de roles
      };
    },
    mounted() {
      this.getUsuarios();
      this.getRoles();
    },
    methods: {
      // Obtener lista de usuarios
      getUsuarios() {
        axios
          .get("http://127.0.0.1:8000/usuarios")
          .then((response) => {
            this.usuarios = response.data.filter((usuario) => usuario.estado === 1); // Solo usuarios activos
          })
          .catch((error) => {
            console.error("Error al obtener usuarios:", error);
            alert("No se pudo cargar la lista de usuarios.");
          });
      },
  
      // Obtener lista de roles
      getRoles() {
        axios
          .get("http://127.0.0.1:8000/roles")
          .then((response) => {
            this.roles = response.data;
          })
          .catch((error) => {
            console.error("Error al obtener roles:", error);
            alert("No se pudo cargar la lista de roles.");
          });
      },
  
      // Enviar actualización al backend
      enviarActualizar(usuario) {
        const url = `http://127.0.0.1:8000/usuarios/${usuario.id_usuario}/rol`;
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("id_rol", usuario.id_rol);
  
        axios
          .post(url, formData)
          .then((response) => {
            console.log("Rol actualizado:", response.data);
            this.getUsuarios(); // Recargar lista de usuarios
            alert("Rol actualizado correctamente.");
          })
          .catch((error) => {
            console.error("Error al actualizar el rol:", error);
            alert("No se pudo actualizar el rol.");
          });
      },
    },
  };
  </script>
  
  <style scoped>
  /* Contenedor principal */
  h1 {
    text-align: center;
    margin: 20px 0;
    font-size: 28px;
    color: #333;
  }
  
  /* Tabla */
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }
  
  th,
  td {
    padding: 12px 15px;
    text-align: left;
  }
  
  th {
    background-color: #007bff;
    color: #fff;
    font-size: 16px;
    text-transform: uppercase;
  }
  
  td {
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #ddd;
  }
  
  tr:hover {
    background-color: #f1f1f1;
  }
  
  select {
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    background-color: #fff;
    outline: none;
    transition: border-color 0.3s ease-in-out;
  }
  
  select:focus {
    border-color: #007bff;
  }
  
  button {
    padding: 5px 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease-in-out;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  
  button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  
  /* Responsive design */
  @media (max-width: 768px) {
    table {
      font-size: 12px;
    }
  
    th,
    td {
      padding: 8px;
    }
  
    select,
    button {
      font-size: 12px;
    }
  }
  </style>
  