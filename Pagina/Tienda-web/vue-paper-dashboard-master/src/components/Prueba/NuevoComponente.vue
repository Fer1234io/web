<template>  
    <div>
      <h1>CATÁLOGO DE PRODUCTOS</h1>
      <p v-if="userPosition">
        Tu ubicación: Latitud {{ userPosition.lat }}, Longitud {{ userPosition.lng }}
      </p>
      <p v-if="tiendaCercana">
        Tienda más cercana: {{ tiendaCercana.nombre_tienda }}
      </p>
      <div v-if="showModal" class="modal-overlay">
        <div class="modal-content">
          <p>{{ modalMessage }}</p>
          <button @click="closeModal" class="close-modal-btn">Cancelar</button>
          <button v-if="confirmCallback" @click="confirmCallback" class="confirm-modal-btn">
            Aceptar
          </button>
        </div>
      </div>

      <!-- Selector de tienda -->
      <div>
        <select v-model="selectedTienda" @change="handleStoreChange">
    <option value="" disabled>Selecciona una tienda</option>
    <option v-for="tienda in tiendas" :key="tienda.id_tienda" :value="tienda.id_tienda">
      {{ tienda.nombre_tienda }}
    </option>
  </select>
      </div>

      <!-- Productos -->
      <div v-if="productos.length > 0" class="grid-container">
        <div v-for="(producto, index) in productos" :key="index" class="grid-item">
          <h4 class="product-name">{{ producto.nombre }}</h4>

          <img :src="'data:image/jpeg;base64,' + producto.foto" alt="Imagen del producto" class="product-image" />
          <h4 class="descripcion-name">{{ producto.descripcion }}</h4>

          <!-- Ocultar precios si el producto está agotado -->
    <div v-if="producto.existencia > 0">
      <p v-if="producto.descuento > 0" class="product-price">
        Precio antes: <span class="original-price">Q{{ formatNumber(producto.precioNormal) }}</span><br />
        Precio ahora: <span class="discounted-price">Q{{ formatNumber(producto.precioConDescuento) }}</span>
      </p>
      <p v-else class="product-price">Precio: Q{{ formatNumber(producto.precioNormal) }}</p>
    </div>

    <!-- Mostrar solo el mensaje de agotado cuando el producto esté agotado -->
    <p v-else class="out-of-stock">Agotado</p>

    <!-- Cantidad disponible o mensaje de agotado -->
    <p v-if="producto.existencia > 0" class="product-quantity">Cantidad disponible: {{ producto.existencia }}</p>

          <!-- Botón de agregar al carrito -->
          <div>
            <button 
              class="add-to-cart-btn" 
              :disabled="producto.existencia === 0" 
              @click="addToCart(producto)">
              <i class="fa fa-shopping-cart"></i> Agregar al carrito
            </button>
            <span v-if="producto.existencia === 0" class="out-of-stock">Agotado</span>
          </div>
        </div>
      </div>
      <p v-else>No hay productos disponibles en esta tienda.</p>

      <!-- Carrito flotante -->
      <div :class="['cart-sidebar', { open: isCartOpen }]">
    <div class="cart-header">
      <h3>Tu Carrito</h3>
      <button @click="toggleCart" class="close-cart">&times;</button>
    </div>

    <div class="cart-items">
      <!-- Si el carrito está vacío, muestra el mensaje -->
      <div v-if="cart.length === 0" class="empty-cart">
        <img src="https://cdn-icons-png.flaticon.com/512/1170/1170678.png" alt="Carrito vacío" class="empty-cart-icon" />
        <p class="empty-cart-text">Tu carrito está vacío</p>
        <button @click="toggleCart" class="btn-back">Regresar al catálogo</button>
      </div>
      <!-- Si el carrito tiene elementos, muestra los productos -->
      <div v-else>
        <div v-for="(item, index) in cart" :key="index" class="cart-item">
          <img :src="'data:image/jpeg;base64,' + item.foto" alt="Imagen del producto" class="cart-item-image" />
          <div class="cart-item-details">
            <h4>{{ item.nombre }}</h4>
            <p>Cantidad: {{ item.cantidad }}</p>
            <p>Precio Unitario: Q{{ formatNumber(item.precio) }}</p>
            <p>Subtotal: Q{{ formatNumber(item.subtotal) }}</p>
          </div>
          <button @click="removeFromCart(index)" class="remove-item">&times;</button>
        </div>
      </div>
    </div>

    <div class="cart-footer" v-if="cart.length > 0">
      <p><strong>Total:</strong> Q{{ formatNumber(cartTotal) }}</p>
      <button class="checkout-btn" @click="finalizarCompra">Finalizar Compra</button>
    </div>

      </div>

      <!-- Botón flotante de "Ver Carrito" -->
      <button class="floating-cart-btn" @click="toggleCart">
        <i class="fa fa-shopping-cart"></i> Ver Carrito ({{ cart.length }})
      </button>
      <!-- Botón de Cotización -->
    <button class="quotation-btn" @click="toggleQuotationModal">
      <i class="fa fa-file-text"></i> Ver Cotización
    </button>

    <!-- Modal de Cotización -->
    <div v-if="showQuotationModal" class="modal-overlay" @click.self="toggleQuotationModal">
      <div class="modal-content">
        <h3>Cotización de Productos</h3>
        <table class="quotation-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio Unitario</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in cart" :key="item.id_inventario">
              <td>{{ item.nombre }}</td>
              <td>{{ item.cantidad }}</td>
              <td>Q{{ formatNumber(item.precio) }}</td>
              <td>Q{{ formatNumber(item.subtotal) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="quotation-total">
          <p><strong>Total:</strong> Q{{ formatNumber(cartTotal) }}</p>
        </div>
        <button class="btn-close" @click="toggleQuotationModal">Cerrar</button>
      </div>
    </div>

    </div>
    
  </template>

  ### Script:
  javascript
  <script>
  import axios from "axios";

  export default {
    data() {
      return {
        userPosition: null,
        tiendas: [],
        selectedTienda: null,
        productos: [],
        tiendaCercana: null,
        cart: [],
        isCartOpen: false,
        currentStore: null, // Tienda actual asociada al carrito
        showModal: false, // Estado del modal
        modalMessage: "", // Mensaje del modal
        confirmCallback: null, // Callback para confirmar acciones
        showQuotationModal: false, // Estado para mostrar/ocultar el modal de cotización
      };
    },
    computed: {
      cartTotal() {
      return this.cart.reduce((total, item) => total + item.subtotal, 0);
    },
    },
    mounted() {
      this.restoreState(); // Restaurar el estado al cargar el componente
      window.addEventListener("beforeunload", this.handleBeforeUnload); // Advertir al cerrar la pestaña

    const user = localStorage.getItem("userData");
    if (user) {
      this.userData = JSON.parse(user); // Recuperar datos del usuario logueado
    }
  },

  beforeDestroy() {
      window.removeEventListener("beforeunload", this.handleBeforeUnload); // Remover advertencia
    },
    methods: {
      toggleQuotationModal() {
      this.showQuotationModal = !this.showQuotationModal;
    },
      cartTotal() {
    return this.cart.reduce((total, item) => total + item.precio * item.cantidad, 0);
  },

        finalizarCompra() {
        if (this.cart.length === 0) {
          this.openModal(
            "Tu carrito está vacío. Agrega productos antes de finalizar la compra."
          );
          return;
        }

        // Guardar el carrito y la tienda seleccionada en localStorage
        this.saveState();
        const tiendaSeleccionada = this.tiendas.find(
      (tienda) => tienda.id_tienda === this.selectedTienda );

      const nombreTienda = tiendaSeleccionada
      ? tiendaSeleccionada.nombre_tienda
      : "Tienda desconocida";

        // Redirigir al resumen de la compra
        this.$router.push({
      name: "Resumen Compra",
      query: {
        carrito: JSON.stringify(this.cart),
        tienda: nombreTienda,
      },
    });
      },
      async restoreState() {
      const tiendaGuardada = localStorage.getItem("tiendaSeleccionada");
      const carritoGuardado = localStorage.getItem("carrito");

      if (tiendaGuardada) {
        this.selectedTienda = tiendaGuardada;
        this.currentStore = tiendaGuardada;
      }

      if (carritoGuardado) {
        this.cart = JSON.parse(carritoGuardado);
      }

      // Cargar tiendas siempre
      await this.fetchTiendas();

      // Si no hay tienda seleccionada, obtener la más cercana
      if (!tiendaGuardada) {
        await this.getUserLocation();
      }

      // Cargar inventario si ya hay tienda seleccionada
      if (this.selectedTienda) {
        await this.fetchInventario();
      }
    },
      async getUserLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          async (position) => {
            this.userPosition = {
              lat: position.coords.latitude,
              lng: position.coords.longitude,
            };
            await this.fetchClosestStore();
          },
          (error) => {
            this.openModal("No se pudo obtener tu ubicación.");
          }
        );
      } else {
        this.openModal("Geolocalización no soportada en este navegador.");
      }
    },

      async fetchClosestStore() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/tienda-cercana", this.userPosition);
        this.tiendaCercana = response.data.tiendaCercana;

        if (this.tiendaCercana) {
          this.selectedTienda = this.tiendaCercana.id_tienda;
          this.currentStore = this.tiendaCercana.id_tienda;
          localStorage.setItem("tiendaSeleccionada", this.selectedTienda); // Guardar tienda seleccionada
          await this.fetchInventario(); // Cargar inventario de la tienda más cercana
        }
      } catch (error) {
        this.openModal("Ocurrió un error al buscar la tienda más cercana.");
      }
    },
      async fetchTiendas() {
        try {
          const response = await axios.get("http://127.0.0.1:8000/tiendas");
          this.tiendas = response.data; // Actualiza la lista de tiendas
        } catch (error) {
          this.openModal("Ocurrió un error al obtener las tiendas.");
        }
      },
      async fetchInventario() {
      if (!this.selectedTienda) return; // Si no hay tienda seleccionada, salir
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/inventario/tienda/${this.selectedTienda}`
        );
        this.productos = response.data.map((item) => {
          const producto = item.producto || {};
          return {
            idInventario: item.id_inventario,
            id_tienda: item.id_tienda,
            nombre: producto.nombre_producto || "Sin nombre",
            descripcion: producto.descripcion || "Sin descripción",
            categoria:
              producto.categoria?.nombre_categoria || "Sin categoría",
            precioNormal: parseFloat(producto.precio || 0),
            precioConDescuento:
              producto.descuento?.porcentaje > 0
                ? producto.precio -
                  (producto.precio * producto.descuento.porcentaje) / 100
                : producto.precio,
            descuento: producto.descuento?.porcentaje || 0,
            existencia: item.existencia,
            foto: producto.fotografia_url || "",
          };
        });
      } catch (error) {
        console.error("Error al obtener el inventario:", error);
        this.openModal("Ocurrió un error al obtener el inventario.");
      }
    },
      formatNumber(value) {
      if (isNaN(value)) return "0.00";
      return value.toLocaleString("es-GT", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    addToCart(producto) {
        const precioUnitario =
          producto.descuento > 0
            ? producto.precioConDescuento
            : producto.precioNormal;

        const existingProduct = this.cart.find(
          (item) => item.nombre === producto.nombre
        );
        const idInventario = producto.id_inventario;

        console.log("ID de inventario del producto añadido:", idInventario);

        if (existingProduct) {
          if (existingProduct.cantidad + 1 > producto.existencia) {
            this.openModal(
              `No puedes agregar más de ${producto.existencia} unidades de este producto.`
            );
          } else {
            existingProduct.cantidad++;
            existingProduct.subtotal =
              existingProduct.cantidad * existingProduct.precio;
          }
        } else {
          this.cart.push({
            ...producto,
            id_inventario: idInventario,
            cantidad: 1,
            precio: precioUnitario,
            subtotal: precioUnitario,
          });
          console.log("Producto agregado al carrito:", producto);
        }
        console.log("Carrito actualizado:", this.cart);
        this.saveState();
      },

      removeFromCart(index) {
        this.cart.splice(index, 1);
        this.saveState();
      },
      toggleCart() {
        this.isCartOpen = !this.isCartOpen;
      },
      openModal(message, confirm = false) {
        this.modalMessage = message;
        this.showModal = true;
        this.confirmCallback = confirm ? this.handleStoreChange : null;
      },

      closeModal() {
        this.showModal = false;
        this.confirmCallback = null;
      },
      handleStoreChange() {
      if (this.cart.length > 0) {
        this.openModal(
          "Tu carrito será vaciado al cambiar de tienda. ¿Deseas continuar?",
          true
        );
        this.confirmCallback = async () => {
          this.cart = []; // Vacía el carrito
          this.currentStore = this.selectedTienda; // Cambia la tienda actual
          this.saveState(); // Actualiza el estado en localStorage
          await this.fetchInventario(); // Carga el inventario de la nueva tienda
          this.closeModal(); // Cierra el modal
        };
      } else {
        this.currentStore = this.selectedTienda; // Cambia la tienda actual
        this.saveState(); // Actualiza el estado en localStorage
        this.fetchInventario(); // Carga el inventario de la nueva tienda
      }
    },
    resetState() {
      this.cart = []; // Vaciar el carrito
      this.selectedTienda = null; // Reiniciar la tienda seleccionada
      this.currentStore = null; // Reiniciar la tienda actual
      localStorage.removeItem("carrito");
      localStorage.removeItem("tiendaSeleccionada");
      this.fetchTiendas(); // Recargar las tiendas tras el reinicio
    },
    saveState() {
      localStorage.setItem("carrito", JSON.stringify(this.cart));
      localStorage.setItem("tiendaSeleccionada", this.selectedTienda);
    },
      resetState() {
      // Restablecer el estado y limpiar el almacenamiento
      this.cart = [];
      this.selectedTienda = null;
      this.currentStore = null;
      localStorage.removeItem("carrito");
      localStorage.removeItem("tiendaSeleccionada");
      this.fetchTiendas(); // Recargar las tiendas tras el reinicio
    },
      handleBeforeUnload(event) {
        if (this.cart.length > 0) {
          event.preventDefault();
          event.returnValue = "Tienes productos en el carrito. Si sales, se perderán.";
        }
      },
      logout() {
        if (this.cart.length > 0) {
          this.openModal(
            "Tienes productos en el carrito. Si sales, se perderán. ¿Deseas continuar?",
            true
          );
          this.confirmCallback = this.executeLogout;
        } else {
          this.executeLogout();
        }
      },
      /**
      * Ejecución de cierre de sesión
      */
      executeLogout() {
        this.resetState(); // Limpia el carrito y la tienda
        this.$router.push("/login"); // Redirige al login
      },


  confirmarCompra() {
    // Lógica para confirmar la compra
    alert("Compra confirmada. ¡Gracias por tu compra!");

    // Limpiar el carrito y la tienda seleccionada
    this.cart = [];
    localStorage.removeItem("carrito");
    localStorage.removeItem("tiendaSeleccionada");

    // Redirigir a una página de confirmación o inicio
    this.$router.push("/cliente/NuevoComponente");
  },


    },
  };
  </script>


  ### Estilos
  css
  <style scoped>
  /* Fondo general */
  body {
    background: linear-gradient(to right, #e0eafc, #cfdef3); /* Fondo degradado */
    font-family: "Arial", sans-serif; /* Fuente general */
    color: #2c3e50; /* Color del texto */
  }

  /* Contenedor principal */
  div {
    max-width: 1200px;
    margin: 20px auto; /* Centrar contenido */
    padding: 20px;
    background: #ffffff; /* Fondo blanco */
    border-radius: 10px; /* Bordes redondeados */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Sombra ligera */
  }

  /* Título principal */
  h1 {
    font-size: 2.5em;
    text-align: center;
    margin-bottom: 20px;
    color: #34495e;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
  }

  /* Selector de tienda */
  select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    background: #f7f7f7;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
  }

  /* Contenedor de productos */
  .grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
    background: #f5f5f5; /* Fondo claro */
  }

  /* Tarjeta de producto */
  .grid-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
  }

  .grid-item:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
  }

  .product-image {
    width: 100%;
    max-width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 10px;
    transition: transform 0.3s;
  }

  .grid-item:hover .product-image {
    transform: scale(1.1);
  }

  .product-name {
    font-size: 18px;
    font-weight: bold;
    margin: 10px 0;
    color: #333;
  }

  .descripcion-name {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
  }

  .product-price {
    font-size: 16px;
    margin-bottom: 10px;
    color: #2c3e50;
  }

  .original-price {
    text-decoration: line-through;
    color: #e74c3c;
  }

  .discounted-price {
    color: #27ae60;
    font-weight: bold;
  }

  .product-quantity {
    font-size: 14px;
    color: #777;
  }

  /* Botón de agregar al carrito */
  .add-to-cart-btn {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
    transition: background-color 0.3s;
  }

  .add-to-cart-btn:hover {
    background-color: #2980b9;
  }

  .add-to-cart-btn i {
    margin-right: 5px;
  }

  /* Sidebar del carrito */
  .cart-sidebar {
    position: fixed;
    right: 0;
    top: 0;
    width: 400px;
    height: 100%;
    background: linear-gradient(to bottom, #f8f9fa, #e0eafc); /* Fondo degradado */
    border-left: 3px solid #3498db;
    box-shadow: -4px 0 10px rgba(0, 0, 0, 0.2);
    transform: translateX(100%);
    transition: transform 0.4s ease-in-out;
    z-index: 1000;
    display: flex;
    flex-direction: column;
  }

  .cart-sidebar.open {
    transform: translateX(0);
  }

  .cart-header {
    background: #3498db;
    color: white;
    padding: 20px;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
    border-bottom: 2px solid #2980b9;
    position: relative;
  }

  .close-cart {
    position: absolute;
    right: 20px;
    top: 20px;
    background: none;
    border: none;
    font-size: 24px;
    color: white;
    cursor: pointer;
    transition: transform 0.2s;
  }

  .close-cart:hover {
    transform: scale(1.2);
  }

  .cart-items {
    padding: 20px;
    overflow-y: auto; /* Scroll para los productos */
    flex: 1; /* Ocupa el espacio restante */
  }

  .cart-items::-webkit-scrollbar {
    width: 8px;
  }

  .cart-items::-webkit-scrollbar-thumb {
    background-color: #3498db;
    border-radius: 4px;
  }

  .cart-items::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .cart-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 10px;
    background: #f9f9f9;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .cart-item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 15px;
  }

  .cart-item-details h4 {
    font-size: 16px;
    margin: 0 0 5px 0;
    color: #333;
  }

  .cart-footer {
    padding: 20px;
    background-color: #f4f4f4;
    border-top: 1px solid #ddd;
    text-align: center;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
  }

  .checkout-btn {
    background: #27ae60;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
    font-weight: bold;
  }

  .checkout-btn:hover {
    background: #219150;
  }

  /* Botón flotante "Ver Carrito" */
  .floating-cart-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(to right, #3498db, #2980b9);
    color: white;
    padding: 15px 20px;
    border: none;
    border-radius: 50%;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    font-size: 18px;
    cursor: pointer;
    z-index: 1001;
    transition: background 0.3s ease-in-out, transform 0.2s;
  }

  .floating-cart-btn:hover {
    background: linear-gradient(to right, #2980b9, #1e6eaa);
    transform: scale(1.1);
  }

  /* Ajustes para pantallas pequeñas */
  @media (max-width: 768px) {
    .cart-sidebar {
      width: 100%;
    }

    .cart-header {
      font-size: 18px;
      padding: 15px;
    }

    .cart-item {
      flex-direction: column;
      align-items: flex-start;
    }

    .cart-item-image {
      margin-bottom: 10px;
    }

    .checkout-btn {
      font-size: 16px;
      padding: 10px 20px;
    }
  }
  .out-of-stock {
    color: #e74c3c;
    font-weight: bold;
    margin-top: 10px;
    display: block;
    text-align: center;
  }

  .add-to-cart-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  .empty-cart {
    text-align: center;
    padding: 30px;
  }

  .empty-cart-icon {
    width: 100px;
    margin-bottom: 20px;
  }

  .empty-cart-text {
    font-size: 18px;
    color: #7f8c8d;
    margin-bottom: 20px;
  }

  .btn-back {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  .btn-back:hover {
    background-color: #2980b9;
  }
  .quotation-btn {
  background-color: #27ae60;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  margin: 10px 0;
}

.quotation-btn:hover {
  background-color: #219150;
}

.quotation-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 16px;
  text-align: left;
}

.quotation-table th,
.quotation-table td {
  border: 1px solid #ddd;
  padding: 10px;
}

.quotation-table th {
  background-color: #f4f4f4;
}

.quotation-total {
  text-align: right;
  margin-top: 20px;
  font-size: 18px;
  font-weight: bold;
}

.modal-content h3 {
  margin-bottom: 15px;
}

  </style>

