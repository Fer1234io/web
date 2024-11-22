<template>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <!-- Logo o nombre de la ruta -->
      <a class="navbar-brand" href="#">{{ routeName }}</a>

      <!-- Botón para colapsar el sidebar (responsive) -->
      <button
        class="navbar-toggler navbar-burger"
        type="button"
        @click="toggleSidebar"
        :aria-expanded="$sidebar.showSidebar"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-bar"></span>
        <span class="navbar-toggler-bar"></span>
        <span class="navbar-toggler-bar"></span>
      </button>

      <!-- Espacio vacío para un diseño limpio -->
      <div class="collapse navbar-collapse"></div>
    </div>
  </nav>
</template>

<script>
export default {
  computed: {
    routeName() {
      const { name } = this.$route;
      return this.capitalizeFirstLetter(name);
    },
  },
  methods: {
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
    },
  },
};
</script>

<style scoped>
/* Estilo del Navbar */
.navbar-custom {
  background: linear-gradient(45deg, #6a11cb, #2575fc); /* Mantener el mismo color */
  color: #ffffff;
  padding: 10px 15px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Estilo del logo o título */
.navbar-custom .navbar-brand {
  font-size: 1.5em;
  font-weight: bold;
  color: #ffffff;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
  transition: color 0.3s ease-in-out;
}

.navbar-custom .navbar-brand:hover {
  color: #ffdd59;
}

/* Botón de colapso */
.navbar-custom .navbar-toggler {
  border: none;
  background: transparent;
}

.navbar-custom .navbar-toggler-bar {
  width: 25px;
  height: 3px;
  background-color: #ffffff;
  margin: 4px 0;
  transition: all 0.2s ease-in-out;
}

.navbar-custom .navbar-toggler:hover .navbar-toggler-bar {
  background-color: #ffdd59;
}

/* Responsive */
@media (max-width: 768px) {
  .navbar-custom .navbar-brand {
    font-size: 1.2em;
  }
}
</style>
