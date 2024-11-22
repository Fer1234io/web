<template>
  <div class="wrapper">
    <side-bar>
      <template slot="links">

        <sidebar-link to="/cliente/stats" name="Mi Perfil" icon="ti-user" />
        <sidebar-link to="/cliente/historial" name="Historial de Compras" icon="ti-receipt" />
        <sidebar-link to="/cliente/NuevoComponente" name="Productos" icon="ti-package" />
        <sidebar-link to="/cliente/Ubicacion" name="Ubicacion" icon="ti-package" />

        <li>
          <button @click="showLogoutModal = true" class="logout-button">Cerrar Sesión</button>
        </li>
      </template>

      <mobile-menu>

      </mobile-menu>
    </side-bar>

    <div class="main-panel">
      <top-navbar></top-navbar>
      <dashboard-content @click.native="toggleSidebar"></dashboard-content>
      <content-footer></content-footer>
    </div>

    <div v-if="showLogoutModal" class="modal-overlay" @click.self="closeLogoutModal">
      <div class="modal-content">
        <h3>Confirmar Cierre de Sesión</h3>
        <p>¿Estás seguro de que deseas cerrar sesión?</p>
        <div class="modal-buttons">
          <button @click="confirmLogout" class="confirm-button">Sí, Cerrar Sesión</button>
          <button @click="closeLogoutModal" class="cancel-button">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu";

export default {
  data() {
    return {
      userData: {}, // Guardará los datos del usuario
      showLogoutModal: false, // Controla la visibilidad del modal
    };
  },
  components: {
    TopNavbar,
    ContentFooter,
    DashboardContent,
    MobileMenu,
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    },
    closeLogoutModal() {
      this.showLogoutModal = false;
    },
    confirmLogout() {
      // Limpia los datos almacenados en localStorage
      localStorage.clear();

      // Redirige al usuario a la página de login
      this.$router.push("/login");

      // Cierra el modal
      this.closeLogoutModal();
    },
  },
};
</script>

<style scoped>
.logout-button {
  background-color: #e74c3c;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
  width: 100%;
}

.logout-button:hover {
  background-color: #c0392b;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.confirm-button {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.confirm-button:hover {
  background-color: #c0392b;
}

.cancel-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.cancel-button:hover {
  background-color: #2980b9;
}
</style>
