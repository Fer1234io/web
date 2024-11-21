<template>
    <div class="wrapper">
      <side-bar>
        <template slot="links">
          <!-- Enlaces específicos para el rol de Cliente -->
          <sidebar-link to="/cliente/stats" name="Mi Perfil" icon="ti-user" />
          <sidebar-link to="/cliente/historial" name="Historial de Compras" icon="ti-receipt" />
          <sidebar-link to="/cliente/NuevoComponente" name="Productos" icon="ti-package" />

          <!-- Otros enlaces según sea necesario -->
  
          <!-- Botón de Cerrar Sesión -->
          <li>
            <button @click="logout" class="logout-button">Cerrar Sesión</button>
          </li>
        </template>
  
        <mobile-menu>
          <!-- Menú móvil para el rol de Cliente -->
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
import SideBar from "../../components/SidebarPlugin/SideBar.vue";
  
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
  