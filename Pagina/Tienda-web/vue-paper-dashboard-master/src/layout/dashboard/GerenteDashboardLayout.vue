<template>
  <div class="wrapper">
    <side-bar>
      <template slot="links">
        <!-- Enlaces específicos para el rol de Gerente -->
        <sidebar-link to="/gerente/stats" name="Perfil" icon="ti-stats-up" />
        <sidebar-link to="/gerente/Roles" name="Adminstrar Funciones" icon="ti-fullscreen" />
        <sidebar-link to="/gerente/Usuarios" name="Adminstrar Usuarios" icon="ti-fullscreen" />
        <sidebar-link to="/gerente/Reportes" name="Adminstrar Reportes" icon="ti-fullscreen" />

        <sidebar-link to="/gerente/Vista" name="Ventas" icon="ti-view-list-alt" />
        <sidebar-link to="/gerente/ProductosAdministrador" name="Productos" icon="ti-package" />
        <sidebar-link to="/gerente/InventarioAdministrador" name="Inventario" icon="ti-package" />
        <sidebar-link to="/gerente/Descuentos" name="Descuentos" icon="ti-package" />
        <sidebar-link to="/gerente/TrasladosAdministrador" name="Traslados" icon="ti-package" />
        <sidebar-link to="/gerente/Ubicacion" name="Ubicacion" icon="ti-package" />

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
  components: {
    TopNavbar,
    ContentFooter,
    DashboardContent,
    MobileMenu,
  },
  data() {
    return {
      showLogoutModal: false, 
    };
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
     
      localStorage.removeItem("role");
      localStorage.removeItem("userData");

      this.$router.push("/login");

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
