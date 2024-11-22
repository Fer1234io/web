<template>
  <div class="user-card">
    <!-- Mostrar la imagen del usuario o una imagen predeterminada -->
    <img
      :src="user.imagen ? 'data:image/png;base64,' + user.imagen : defaultImage"
      alt="User Image"
      class="user-image"
    />
    <h3>{{ user.nombre || "Nombre no disponible" }} {{ user.apellido || "" }}</h3>
    <p class="user-role">{{ user.rol || "Rol no asignado" }}</p>
    <p>{{ user.correo || "Correo no disponible" }}</p>
  </div>
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
};
</script>

<style scoped>
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

.user-role {
  font-weight: bold;
  color: #6c757d;
}
</style>
