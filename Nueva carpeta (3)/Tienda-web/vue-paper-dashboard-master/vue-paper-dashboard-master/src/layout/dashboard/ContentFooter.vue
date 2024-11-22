<template>
  <footer class="footer">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
      <div class="footer-brand">
        <img src="@/assets/img/joseph.png" alt="Logo" class="footer-logo" />
        <span>Store Online S.A</span>
      </div>
      <div class="footer-copyright">
        &copy; {{ new Date().getFullYear() }} Diseñado por
        <a href="https://www.creative-tim.com/?ref=pdf-vuejs" target="_blank">
          Jose Acabal</a
        >.
      </div>
    </div>
  </footer>
</template>

<script>
export default {};
</script>

<style scoped>
/* Footer */
.footer {
  background: linear-gradient(45deg, #6a11cb, #2575fc); /* Igual al navbar */
  color: #ffffff;
  padding: 20px;
  font-size: 14px;
  width: 100%;
  border-top: 2px solid rgba(255, 255, 255, 0.2);
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 10px;
}

.footer-logo {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #ffffff;
}

.footer-brand span {
  font-size: 18px;
  font-weight: bold;
}

.footer-copyright {
  font-size: 14px;
  text-align: center;
  margin-top: 10px;
}

.footer a {
  color: #ffdd59;
  text-decoration: none;
  font-weight: bold;
}

.footer a:hover {
  text-decoration: underline;
}

/* Contenedor Principal */
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* Altura mínima de toda la página */
  margin: 0;
}

main {
  flex: 1; /* Hace que el contenido principal ocupe todo el espacio disponible */
}

.container-fluid {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

@media (min-width: 768px) {
  .container-fluid {
    flex-direction: row;
    justify-content: space-between;
    text-align: left;
  }
}

</style>
