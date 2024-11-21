<template>
    <div class="wrapper">
      <side-bar>
        <template slot="links">
          <!-- Enlaces específicos para el rol de Gerente -->
          <sidebar-link to="/gerente/notifications" name="Notifications" icon="ti-bell" />
          <sidebar-link to="/gerente/stats" name="User Stats" icon="ti-stats-up" />
          <sidebar-link to="/gerente/GerenteUsuario" name="Administrar Usuarios" icon="ti-user" />
          <!-- Otros enlaces según sea necesario -->
  
          <!-- Botón de Cerrar Sesión -->
          <li>
            <button @click="logout" class="logout-button">Cerrar Sesión</button>
          </li>
        </template>
  
        <mobile-menu>
          <!-- Menú móvil para el rol de Gerente -->
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
  