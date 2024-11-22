<template>
  <div class="crear-cuenta-page">
    <div class="crear-cuenta-card">
      <h2>¡Regístrate en Store Online S.A!</h2>
      <p>Disfruta de nuestras ofertas exclusivas y novedades tecnológicas.</p>
      <form @submit.prevent="crearCuenta">
        <div class="form-grid">
          <!-- Campo: Nombre -->
          <div class="form-group">
            <label>Nombre:</label>
            <input type="text" v-model="nombre" required placeholder="Nombre" />
          </div>

          <!-- Campo: Apellido -->
          <div class="form-group">
            <label>Apellido:</label>
            <input type="text" v-model="apellido" required placeholder="Apellido" />
          </div>

          <!-- Campo: Correo -->
          <div class="form-group">
            <label>Correo Electrónico:</label>
            <input
              type="email"
              v-model="correo"
              required
              placeholder="Correo Electrónico"
              @blur="validateCorreo"
            />
            <small v-if="correoError" class="error">{{ correoError }}</small>
          </div>

          <!-- Campo: Nombre de Usuario -->
          <div class="form-group">
            <label>Usuario:</label>
            <input
              type="text"
              v-model="nombre_usuario"
              required
              placeholder="Nombre de Usuario"
            />
          </div>

          <!-- Campo: Contraseña -->
          <div class="form-group">
            <label>Contraseña:</label>
            <input
              type="password"
              v-model="contraseña"
              required
              placeholder="Contraseña"
            />
          </div>

          <!-- Campo: Confirmar Contraseña -->
          <div class="form-group">
            <label>Confirmar Contraseña:</label>
            <input
              type="password"
              v-model="confirmarContraseña"
              required
              placeholder="Confirmar Contraseña"
            />
            <small v-if="contraseñaError" class="error">{{ contraseñaError }}</small>
          </div>

          <!-- Campo: Dirección -->
          <div class="form-group full-width">
            <label>Dirección:</label>
            <input type="text" v-model="direccion" required placeholder="Dirección" />
          </div>

          <!-- Campo: Teléfono -->
          <div class="form-group full-width">
            <label>Teléfono:</label>
            <input
              type="text"
              v-model="numero_telefono"
              required
              placeholder="Teléfono"
              @input="validateTelefono"
              maxlength="8"
            />
            <small v-if="telefonoError" class="error">{{ telefonoError }}</small>
          </div>
        </div>

        <!-- Botones -->
        <div class="buttons">
          <button type="submit" class="btn-primary">Crear Cuenta</button>
          <button type="button" @click="regresarLogin" class="btn-secondary">Regresar</button>
        </div>
      </form>
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
      telefonoError: null,
    };
  },
  methods: {
    async crearCuenta() {
      if (this.contraseña !== this.confirmarContraseña) {
        this.contraseñaError = "Las contraseñas no coinciden.";
        return;
      }
      this.contraseñaError = null;

      if (this.numero_telefono.length !== 8) {
        this.telefonoError = "El número de teléfono debe tener 8 dígitos.";
        return;
      }
      this.telefonoError = null;

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

        await axios.post("http://localhost:8000/usuarios", nuevoUsuario);

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
    validateTelefono() {
      const phoneRegex = /^\d{8}$/;
      if (!phoneRegex.test(this.numero_telefono)) {
        this.telefonoError = "El número de teléfono debe tener 8 dígitos.";
      } else {
        this.telefonoError = null;
      }
    },
  },
};
</script>


<style scoped>
.crear-cuenta-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
  padding: 20px;
}

.crear-cuenta-card {
  width: 100%;
  max-width: 600px;
  padding: 20px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  font-family: "Roboto", sans-serif;
  text-align: center;
}

h2 {
  color: #2c3e50;
  margin-bottom: 10px;
}

p {
  color: #7f8c8d;
  margin-bottom: 20px;
  font-size: 14px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.full-width {
  grid-column: span 2;
}

label {
  font-size: 13px;
  margin-bottom: 5px;
  color: #34495e;
}

input {
  padding: 10px;
  border: 1px solid #dcdcdc;
  border-radius: 6px;
  font-size: 13px;
}

input:focus {
  outline: none;
  border-color: #3498db;
}

.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

button {
  width: 48%;
  padding: 10px;
  font-size: 13px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-primary {
  background: #1abc9c;
  color: #fff;
}

.btn-primary:hover {
  background: #16a085;
}

.btn-secondary {
  background: #3498db;
  color: #fff;
}

.btn-secondary:hover {
  background: #2980b9;
}

.error {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 5px;
}
</style>
