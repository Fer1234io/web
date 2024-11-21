<template>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form @submit.prevent="login">
            <div class="form-group">
                <label>Correo:</label>
                <input type="email" v-model="username" required />
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" v-model="password" required />
            </div>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            username: "",
            password: "",
        };
    },
    methods: {
        async login() {
            try {
                const response = await axios.post("http://localhost:8000/login", {
                    correo: this.username,
                    contraseña: this.password,
                });

                if (response.data.rol) {
                    localStorage.setItem("role", response.data.rol);
                    localStorage.setItem("userData", JSON.stringify(response.data));

                    // Redirige al layout específico según el rol del usuario
                    switch (response.data.rol) {
                        case "Administrador":
                            this.$router.push("/admin/dashboard");
                            break;
                        case "Cliente":
                            this.$router.push("/cliente/stats"); // Cambia esto a la ruta correspondiente para "Cliente"
                            break;
                        case "Gerente":
                            this.$router.push("/gerente/notifications");
                            break;
                        default:
                            this.$router.push("/general/nuevo-componente"); // Ruta predeterminada para otros roles
                    }
                } else {
                    alert("Credenciales incorrectas");
                }
            } catch (error) {
                console.error("Error al iniciar sesión:", error);
                alert("Hubo un problema al iniciar sesión. Inténtalo de nuevo.");
            }
        },
    },
};
</script>

<style>
.login-container {
    max-width: 400px;
    margin: auto;
    padding: 2em;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background: #ffffff;
}
</style>
