<template>
    <div class="wrapper">
      <side-bar>
        <template slot="links">
          <sidebar-link to="/admin/dashboard" name="Dashboard" icon="ti-panel" />
          <sidebar-link to="/admin/stats" name="User Profile" icon="ti-user" />
          <sidebar-link to="/admin/table-list" name="Table List" icon="ti-view-list-alt" />
          <sidebar-link to="/admin/ProductosAdministrador" name="Productos" icon="ti-package" />
          <sidebar-link to="/admin/InventarioAdministrador" name="Inventario" icon="ti-package" />
          <!-- Otros enlaces específicos para el rol de Administrador -->
  
          <!-- Botón de Cerrar Sesión -->
          <li>
            <button @click="logout" class="logout-button">Cerrar Sesión</button>
          </li>
        </template>
        <mobile-menu>
          <!-- Menú móvil para el rol de Administrador -->
        </mobile-menu>
      </side-bar>
  
      <div class="main-panel">
        <top-navbar></top-navbar>
        <dashboard-content @click.native="toggleSidebar"></dashboard-content>
        <content-footer></content-footer>
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
    methods: {
      toggleSidebar() {
        if (this.$sidebar.showSidebar) {
          this.$sidebar.displaySidebar(false);
        }
      },
      logout() {
        // Eliminar los datos de autenticación del almacenamiento local
        localStorage.removeItem("role");
        localStorage.removeItem("userData");
  
        // Redirigir al usuario a la página de login
        this.$router.push("/login");
      }
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
  </style>
  