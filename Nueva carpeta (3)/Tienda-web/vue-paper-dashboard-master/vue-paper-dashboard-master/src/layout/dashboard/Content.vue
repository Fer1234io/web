<template>
  <div class="content">
    <div class="container-fluid">
      <transition name="fade" mode="out-in">
        <!-- Contenido dinámico -->
        <router-view></router-view>
      </transition>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style scoped>
/* Transiciones */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.fade-enter,
.fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0;
  transform: translateY(10px);
}

/* Estilo base del contenedor principal */
.content {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #eef2f3, #ffffff);
  font-family: "Roboto", sans-serif;
  color: #333;
  padding: 20px;
  box-sizing: border-box;
}

/* Contenedor fluido */
.container-fluid {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  background: #ffffff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  padding: 20px;
  animation: fadeIn 0.5s ease-in-out;
}

/* Animación de entrada */
@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: scale(0.95);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsividad */
@media (max-width: 768px) {
  .container-fluid {
    padding: 15px;
  }

  .content {
    padding: 10px;
  }
}

@media (max-width: 480px) {
  .container-fluid {
    padding: 10px;
    box-shadow: none;
  }
}
</style>
