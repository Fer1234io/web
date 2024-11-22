<template>
  <div
    class="sidebar"
    :data-background-color="backgroundColor"
    :data-active-color="activeColor"
  >
    <div class="sidebar-wrapper" id="style-3">
      <!-- Logo -->
      <div class="logo">
        <a href="#" class="simple-text">
          <div class="logo-img">
            <img src="@/assets/img/joseph.png" alt="Logo" />
          </div>
          {{ title }}
        </a>
      </div>

      <!-- Links -->
      <ul class="nav">
        <slot name="links">
          <sidebar-link
            v-for="(link, index) in sidebarLinks"
            :key="index"
            :to="link.path"
            :name="link.name"
            :icon="link.icon"
          >
          </sidebar-link>
        </slot>
      </ul>

      <!-- Moving Arrow -->
      <moving-arrow :move-y="arrowMovePx"></moving-arrow>
    </div>
  </div>
</template>

<script>
import MovingArrow from "./MovingArrow.vue";
import SidebarLink from "./SidebarLink";

export default {
  props: {
    title: {
      type: String,
      default: "Store Online S.A",
    },
    backgroundColor: {
  type: String,
  default: "black", // Cambia el valor predeterminado al color deseado
  validator: (value) => ["white", "black", "darkblue", "custom"].includes(value), // Agrega 'custom' o el nombre del color personalizado
},

    activeColor: {
      type: String,
      default: "primary",
      validator: (value) =>
        ["primary", "info", "success", "warning", "danger"].includes(value),
    },
    sidebarLinks: {
      type: Array,
      default: () => [],
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
  },
  provide() {
    return {
      autoClose: this.autoClose,
      addLink: this.addLink,
      removeLink: this.removeLink,
    };
  },
  components: {
    MovingArrow,
    SidebarLink,
  },
  computed: {
    arrowMovePx() {
      return this.linkHeight * this.activeLinkIndex;
    },
  },
  data() {
    return {
      linkHeight: 65,
      activeLinkIndex: 0,
    };
  },
  methods: {
    findActiveLink() {
      this.links.forEach((link, index) => {
        if (link.isActive()) {
          this.activeLinkIndex = index;
        }
      });
    },
    addLink(link) {
      const index = this.$slots.links.indexOf(link.$vnode);
      this.links.splice(index, 0, link);
    },
    removeLink(link) {
      const index = this.links.indexOf(link);
      if (index > -1) {
        this.links.splice(index, 1);
      }
    },
  },
  mounted() {
    this.$watch("$route", this.findActiveLink, {
      immediate: true,
    });
  },
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  background: linear-gradient(45deg, #2c3e50, #34495e); /* Color similar al footer */
  color: #ecf0f1; /* Color del texto */
  height: 100vh;
  position: fixed;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2); /* Sombra */
  overflow: auto;
  transition: all 0.3s ease;
}

.sidebar-wrapper {
  padding: 15px 10px;
}

.logo {
  text-align: center;
  margin-bottom: 20px;
}

.logo-img img {
  max-width: 80px;
  border-radius: 50%;
  border: 2px solid #ecf0f1;
}

.nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav li {
  margin: 10px 0;
}

.nav li a {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 5px;
  color: #ecf0f1;
  text-decoration: none;
  transition: all 0.3s ease;
}

.nav li a:hover,
.nav li a.active {
  background-color: rgba(255, 255, 255, 0.1); /* Color hover */
  color: #fff;
}

.nav li a i {
  margin-right: 10px;
  font-size: 18px;
}

@media (max-width: 768px) {
  .sidebar {
    width: 200px;
  }
  .nav li a {
    font-size: 14px;
    padding: 8px 10px;
  }
}

</style>

