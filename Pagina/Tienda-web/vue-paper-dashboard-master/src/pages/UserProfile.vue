<template>
  <div class="row profile-container">
    <!-- Tarjeta del Usuario -->
    <div class="col-xl-4 col-lg-5 col-md-6 user-card-container">
      <div class="user-card">
        <img
          :src="userData.fotografia_url ? 'data:image/jpeg;base64,' + userData.fotografia_url : defaultImage"
          alt="User Image"
          class="user-image"
        />
        <h3>{{ userData.nombre || "Nombre no disponible" }} {{ userData.apellido || "" }}</h3>
        <p class="user-role">{{ userData.rol || "Rol no asignado" }}</p>
        <p>{{ userData.correo || "Correo no disponible" }}</p>
        <button class="btn btn-primary edit-button" @click="enableEditing">
          {{ isEditing ? "Cancelar Edición" : "Editar Información" }}
        </button>
      </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="col-xl-8 col-lg-7 col-md-6 edit-profile-container" v-if="isEditing">
      <form @submit.prevent="enviarActualizar">
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input type="text" v-model="userData.nombre" required />
        </div>
        <div class="form-group">
          <label for="apellido">Apellido:</label>
          <input type="text" v-model="userData.apellido" required />
        </div>
        <div class="form-group">
          <label for="correo">Correo:</label>
          <input type="email" v-model="userData.correo" required />
        </div>
        <div class="form-group">
          <label for="telefono">Teléfono:</label>
          <input type="text" v-model="userData.numero_telefono" maxlength="8" pattern="^[0-9]{8}$" required />
        </div>
        <div class="form-group">
          <label for="direccion">Dirección:</label>
          <input type="text" v-model="userData.direccion" required />
        </div>
        <div class="form-group">
          <label for="fotografia_url">Imagen:</label>
          <input type="file" @change="handleImageUpload" />
          <img
            v-if="userData.fotografia_url"
            :src="'data:image/jpeg;base64,' + userData.fotografia_url"
            alt="Vista previa de imagen"
            class="image-preview"
          />
        </div>
        <!-- Cambio de Contraseña -->
        <div class="form-group">
          <button type="button" class="btn btn-secondary" @click="togglePasswordChange">
            {{ changePassword ? "Cancelar Cambio de Contraseña" : "¿Cambiar Contraseña?" }}
          </button>
        </div>
        <div v-if="changePassword" class="form-group">
          <label for="new_password">Nueva Contraseña:</label>
          <input type="password" v-model="password" minlength="6" placeholder="Nueva Contraseña" required />
          <label for="confirm_password">Confirmar Contraseña:</label>
          <input type="password" v-model="confirmPassword" minlength="6" placeholder="Confirmar Contraseña" required />
          <small v-if="password !== confirmPassword && confirmPassword">
            Las contraseñas no coinciden.
          </small>
        </div>

        <button type="submit" class="btn btn-submit" :disabled="!canSave">Guardar</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      userData: {
        id_usuario: null,
        nombre: "",
        apellido: "",
        correo: "",
        numero_telefono: "",
        direccion: "",
        rol: "",
        fotografia_url: "",
      },
      defaultImage: "https://via.placeholder.com/150", 
      isEditing: false,
      changePassword: false, 
      password: "", 
      confirmPassword: "", 
    };
  },
  computed: {
    canSave() {
      return !this.changePassword || (this.password && this.password === this.confirmPassword);
    },
  },
  mounted() {
  console.log("Datos iniciales del usuario:", this.userData);
  this.getUserData();
},
  methods: {
    getUserData() {
  const user = localStorage.getItem("userData");

  if (user) {
    this.userData = JSON.parse(user); // Asignar datos desde localStorage
    console.log("Datos actualizados después de guardar:", this.userData);
  } else {
    console.error("No se encontraron datos de usuario en localStorage.");
    alert("Tu sesión ha expirado. Por favor, inicia sesión de nuevo.");
    this.$router.push("/login");
  }
}
,


    enableEditing() {
      this.isEditing = !this.isEditing;
      if (!this.isEditing) {
        this.resetPasswordFields();
      }
    },
    togglePasswordChange() {
      this.changePassword = !this.changePassword;
      if (!this.changePassword) {
        this.resetPasswordFields();
      }
    },
    resetPasswordFields() {
      this.password = "";
      this.confirmPassword = "";
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.userData.fotografia_url = e.target.result.split(",")[1];
      };

      reader.readAsDataURL(file);
    },
    async enviarActualizar() {
  const actualizarUrl = `http://127.0.0.1:8000/usuarios/${this.userData.id_usuario}`;
  const formData = new FormData();
  formData.append("_method", "PUT");
  formData.append("nombre", this.userData.nombre);
  formData.append("apellido", this.userData.apellido);
  formData.append("correo", this.userData.correo);
  formData.append("numero_telefono", this.userData.numero_telefono);
  formData.append("direccion", this.userData.direccion);

  if (this.userData.fotografia_url) {
    formData.append("imagen", this.userData.fotografia_url);
  }

  if (this.changePassword && this.password) {
    formData.append("contraseña", this.password); 
  }

  try {
    const response = await axios.post(actualizarUrl, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    this.userData = { ...this.userData, ...response.data.usuario };
    localStorage.setItem("userData", JSON.stringify(this.userData)); 

    this.isEditing = false;
    this.resetPasswordFields();
    alert("Perfil actualizado correctamente");
  } catch (error) {
    console.error("Error al actualizar el perfil:", error);
    alert("No se pudo actualizar el perfil");
  }
},

  },
};
</script>

<style scoped>
.profile-container {
  margin: 2em auto;
  max-width: 1200px;
  padding: 1.5em;
  background: #f8f9fa;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.user-card-container {
  margin-bottom: 1em;
}

.edit-profile-container {
  background: #ffffff;
  padding: 2em;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.user-card {
  text-align: center;
  background: #ffffff;
  padding: 1.5em;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.user-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 1em;
  object-fit: cover;
}

.image-preview {
  display: block;
  margin-top: 10px;
  width: 100px;
  height: auto;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.btn-submit {
  background-color: #2196F3;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-submit:hover {
  background-color: #1976D2;
}
</style>
