<template>
  <div class="crear-cuenta-page">
    <div class="left-section">
      <div class="welcome-text">
        <h1>¡Únete a nosotros!</h1>
        <p>
          Crea tu cuenta y sé parte de nuestra comunidad. Administra tus datos,
          realiza compras y descubre beneficios exclusivos.
        </p>
      </div>
    </div>
    <div class="right-section">
      <div class="crear-cuenta-container">
        <h2>Crear Cuenta</h2>
        <form @submit.prevent="crearCuenta">
          <!-- Campo: Nombre -->
          <div class="form-group">
            <label>Nombre:</label>
            <input type="text" v-model="nombre" required placeholder="Ingrese su nombre" />
          </div>

          <!-- Campo: Apellido -->
          <div class="form-group">
            <label>Apellido:</label>
            <input type="text" v-model="apellido" required placeholder="Ingrese su apellido" />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label>Correo:</label>
            <input
              type="email"
              v-model="correo"
              required
              placeholder="Ingrese su correo"
              @input="validateCorreo"
            />
            <small v-if="correoError" class="error">{{ correoError }}</small>
          </div>

          <!-- Campo: Nombre de Usuario -->
          <div class="form-group">
            <label>Nombre de Usuario:</label>
            <input
              type="text"
              v-model="nombre_usuario"
              required
              placeholder="Ingrese su nombre de usuario"
            />
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label>Contraseña:</label>
            <input
              type="password"
              v-model="contraseña"
              required
              placeholder="Ingrese su contraseña"
            />
          </div>

          <!-- Campo: Confirmar Contraseña -->
          <div class="form-group">
            <label>Confirmar Contraseña:</label>
            <input
              type="password"
              v-model="confirmarContraseña"
              required
              placeholder="Confirme su contraseña"
            />
            <small v-if="contraseñaError" class="error">{{ contraseñaError }}</small>
          </div>

          <!-- Campo: Dirección -->
          <div class="form-group">
            <label>Dirección:</label>
            <input type="text" v-model="direccion" required placeholder="Ingrese su dirección" />
          </div>

          <!-- Campo: Teléfono -->
          <div class="form-group">
            <label>Teléfono:</label>
            <input
              type="text"
              v-model="numero_telefono"
              required
              placeholder="Ingrese su número de teléfono"
            />
          </div>

          <!-- Botones -->
          <div class="buttons">
            <button type="submit" class="btn-crear-cuenta">Crear Cuenta</button>
            <button type="button" @click="regresarLogin" class="btn-regresar">Regresar</button>
          </div>
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
      nombre: "",
      apellido: "",
      correo: "",
      nombre_usuario: "",
      contraseña: "",
      confirmarContraseña: "",
      direccion: "",
      numero_telefono: "",
      correoError: null,
      contraseñaError: null,
    };
  },
  methods: {
    async crearCuenta() {
      if (this.contraseña !== this.confirmarContraseña) {
        this.contraseñaError = "Las contraseñas no coinciden.";
        return;
      }
      this.contraseñaError = null;

      try {
        const nuevoUsuario = {
          nombre: this.nombre,
          apellido: this.apellido,
          correo: this.correo,
          nombre_usuario: this.nombre_usuario,
          contraseña: this.contraseña,
          direccion: this.direccion,
          numero_telefono: this.numero_telefono,
          id_rol: 3,
        };

        await axios.post("http://localhost:8000/api/usuarios", nuevoUsuario);

        alert("Cuenta creada exitosamente. Redirigiendo al inicio de sesión...");
        this.$router.push("/login");
      } catch (error) {
        if (error.response?.status === 422) {
          const errors = error.response.data.errors || {};
          if (errors.correo) {
            this.correoError = "El correo ya está registrado.";
          }
        } else {
          console.error("Error al crear cuenta:", error.response?.data || error.message);
          alert("Ocurrió un problema al crear la cuenta. Inténtalo de nuevo.");
        }
      }
    },
    regresarLogin() {
      this.$router.push("/login");
    },
    validateCorreo() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+(\.[^\s@]+)?$/;
      if (!emailRegex.test(this.correo)) {
        this.correoError = "El formato del correo no es válido.";
      } else {
        this.correoError = null;
      }
    },
  },
};
</script>

<style scoped>
.crear-cuenta-page {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.left-section {
  width: 50%;
  background: linear-gradient(135deg, #ff7e5f, #feb47b);
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  text-align: center;
  padding: 20px;
}

.welcome-text h1 {
  font-size: 2.5rem;
  margin-bottom: 20px;
}

.welcome-text p {
  font-size: 1.2rem;
}

.right-section {
  width: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f7f7f7;
}

.crear-cuenta-container {
  max-width: 500px;
  width: 100%;
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-size: 14px;
  font-weight: bold;
  color: #333;
}

input {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-top: 8px;
}

input:focus {
  outline: none;
  border-color: #ff7e5f;
}

.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

button {
  width: 48%;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-crear-cuenta {
  background: #28a745;
  color: #fff;
}

.btn-crear-cuenta:hover {
  background: #218838;
}

.btn-regresar {
  background: #007bff;
  color: #fff;
}

.btn-regresar:hover {
  background: #0056b3;
}

.error {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 5px;
}
</style>
