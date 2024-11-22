<template>
    <div class="login-container">
      <h2>Iniciar Sesión</h2>
      <form @submit.prevent="login">
        <div class="form-group">
          <label>Correo:</label>
          <input type="email" v-model="username" required placeholder="Ingrese su correo" />
        </div>
        <div class="form-group">
          <label>Contraseña:</label>
          <input type="password" v-model="password" required placeholder="Ingrese su contraseña" />
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
      </form>
      <div class="extra-actions">
        <p>¿No tienes cuenta?</p>
        <button @click="crearCuenta" class="btn-create">Crear Cuenta</button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";

  export default {
    data() {
      return {
        username: "",
        password: "",
      };
    },
    methods: {
      async login() {
        try {
          console.log("Intentando iniciar sesión con:", {
            correo: this.username,
            contraseña: this.password,
          });
  
          const response = await axios.post("http://localhost:8000/login", {
            correo: this.username,
            contraseña: this.password,
          });
  
          console.log("Respuesta del servidor:", response.data);
  
          if (response.data.rol) {
            localStorage.setItem(
              "userData",
              JSON.stringify({
                id_usuario: response.data.id_usuario,
                nombre: response.data.nombre,
                apellido: response.data.apellido,
                correo: response.data.correo,
                numero_telefono: response.data.numero_telefono || "",
                direccion: response.data.direccion || "",
                rol: response.data.rol,
                fotografia_url: response.data.fotografia_url || null,
              })
            );
  
            console.log("Datos del usuario almacenados correctamente en localStorage.");
  
            const role = response.data.rol;
            if (role === "Administrador") {
              this.$router.push("/admin/dashboard");
            } else if (role === "Cliente") {
              this.$router.push("/cliente/stats");
            } else if (role === "Gerente") {
              this.$router.push("/gerente/stats");
            } else {
              this.$router.push("/general/nuevo-componente");
            }
          } else {
            alert("Credenciales incorrectas");
          }
        } catch (error) {
          console.error("Error al iniciar sesión:", error.response?.data || error.message);
          alert("Hubo un problema al iniciar sesión. Inténtalo de nuevo.");
        }
      },
 crearCuenta() {
    this.$router.push("/crear-cuenta"); // Redirige al componente CrearCuenta.vue
  },
    },
  };
  </script>
  
  <style scoped>
  /* General styles for the container */
  .login-container {
    max-width: 400px;
    margin: 5vh auto;
    padding: 2em;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    font-family: "Arial", sans-serif;
  }
  
  /* Form header */
  .login-container h2 {
    text-align: center;
    margin-bottom: 1.5em;
    font-weight: bold;
    font-size: 24px;
  }
  
  /* Form group styles */
  .form-group {
    margin-bottom: 1.5em;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 0.5em;
    font-weight: bold;
    font-size: 14px;
  }
  
  .form-group input {
    width: 100%;
    padding: 0.75em;
    border-radius: 8px;
    border: none;
    outline: none;
    font-size: 16px;
    color: #333;
  }
  
  /* Button styles */
  button {
    display: block;
    width: 100%;
    padding: 0.75em;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  /* Login button */
  .btn-login {
    background: #28a745;
    color: #fff;
  }
  
  .btn-login:hover {
    background: #218838;
  }
  
  /* Create account button */
  .extra-actions {
    text-align: center;
    margin-top: 1.5em;
  }
  
  .extra-actions p {
    margin-bottom: 0.5em;
  }
  
  .btn-create {
    background: transparent;
    border: 1px solid #fff;
    color: #fff;
  }
  
  .btn-create:hover {
    background: #fff;
    color: #007bff;
  }
  
  /* Responsive adjustments */
  @media (max-width: 480px) {
    .login-container {
      padding: 1.5em;
    }
  
    button {
      font-size: 14px;
    }
  
    .form-group input {
      font-size: 14px;
    }
  }
  </style>
  