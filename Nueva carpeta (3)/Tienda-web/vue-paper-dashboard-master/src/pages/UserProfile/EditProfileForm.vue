<template>
  <form>
    <div class="form-group">
      <label for="nombre">Nombre:</label>
      <input type="text" v-model="user.nombre" />
    </div>
    <div class="form-group">
      <label for="apellido">Apellido:</label>
      <input type="text" v-model="user.apellido" />
    </div>
    <div class="form-group">
      <label for="imagen">Imagen:</label>
      <input type="file" @change="handleImageUpload" />
      <!-- Mostrar la imagen actual o una predeterminada -->
      <img
        :src="user.imagen ? 'data:image/png;base64,' + user.imagen : defaultImage"
        alt="User Image"
        class="image-preview"
      />
    </div>
    <button type="button" @click="saveProfile">Guardar</button>
  </form>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      defaultImage: "https://via.placeholder.com/150", // Imagen predeterminada
    };
  },
  methods: {
    handleImageUpload(event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      // Convertir la imagen a base64
      reader.onload = (e) => {
        this.user.imagen = e.target.result.split(",")[1]; // Solo la parte base64
      };

      reader.readAsDataURL(file);
    },
    saveProfile() {
      // Lógica para guardar el perfil (puede hacer una llamada a la API)
      this.$emit("save", this.user);
    },
  },
};
</script>

<style scoped>
.image-preview {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-top: 10px;
  object-fit: cover;
  border: 1px solid #ddd;
}
</style>
