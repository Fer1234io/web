<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-illustration">
        <img src="@/assets/img/logo.png" alt="Tech Store Logo" />
        <h1>Bienvenido a Store Online S.A</h1>
        <p>Inicia sesión para acceder a los mejores productos tecnológicos.</p>
      </div>
      <div class="login-form">
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
        const response = await axios.post("http://localhost:8000/login", {
          correo: this.username,
          contraseña: this.password,
        });

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

          const role = response.data.rol;
          if (role === "Administrador") {
            this.$router.push("/admin/stats");
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
        alert("Hubo un problema al iniciar sesión. Inténtalo de nuevo.");
      }
    },
    crearCuenta() {
      this.$router.push("/crear-cuenta");
    },
  },
};
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(to bottom, #e0f7fa, #006064);
  padding: 20px;
}

.login-container {
  display: flex;
  max-width: 900px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.login-illustration {
  width: 50%;
  background: linear-gradient(to bottom, #006064, #004d40);
  color: #ffffff;
  padding: 40px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.login-illustration img {
  max-width: 100%;
  margin-bottom: 20px;
}

.login-illustration h1 {
  font-size: 24px;
  margin-bottom: 10px;
}

.login-illustration p {
  font-size: 16px;
}

.login-form {
  width: 50%;
  padding: 40px;
}

.login-form h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #006064;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
  color: #333;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.form-group input:focus {
  outline: none;
  border-color: #006064;
}

.btn-login {
  width: 100%;
  padding: 10px;
  background: #006064;
  color: #ffffff;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-login:hover {
  background: #004d40;
}

.extra-actions {
  text-align: center;
  margin-top: 15px;
}

.btn-create { 
  background: none;
  color: #006064;
  border: 1px solid #006064;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-create:hover {
  background: #006064;
  color: #ffffff;
}
</style>
