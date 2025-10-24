var apiEndpoint =
  "/EMRApp/include/sidebar/function/ajax_functions.php?FUNC=";
var Headers = {
  json: { header: "Content-Type", value: "application/json" },
  form: { header: "Content-Type", value: "application/x-www-form-urlencoded" },
};

const { createApp } = Vue;

const app1 = createApp({
  directives: {
    upper: {
      update(el) {
        el.value = el.value.toUpperCase();
      },
    },
  },
  mounted: function () {
    this.obtenerModulos();
  },
  data() {
    return {
      modulos: null,
    };
  },
  watch: {},
  methods: {
    obtenerModulos: function () {
    
          var requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json; charset=utf-8" },
            redirect: "follow",
          };
    
          fetch(apiEndpoint + "getModulos", requestOptions)
            .then((response) => {
                
              return response.json();
            })
            .then((respuesta) => {
              if (respuesta.estado == 1) {
                console.log(respuesta)
                this.modulos = respuesta.desc;
              } else if(respuesta.estado == 2) {
                this.modalInfo(respuesta.desc)
              } else {
                this.modalError(respuesta.desc);
              }
            })
            .catch((error) => {
              this.modalErrorApi(error);
              this.mostrarAnimacion = false;
            });
    },
    modalErrorApi: function (error) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: `Error: ${error}`,
        footer: null,
      });
    },
    //Modal de error, recibe como parametro el mensaje de error a mostrar
    modalError: function (error) {
      swal.fire({
        title: "Oops...",
        html: `Error: ${error}`,
        icon: "error",
        showConfirmButton: false,
        timer: 5000,
        position: "top-end",
        toast: true,
        width: "auto",
      });
    },
    //Modal de realizado con exito, recibe como parametro el mensaje de exito a mostrar
    modalSuccess: function (mensaje) {
      return swal.fire({
        title: mensaje,
        icon: "success",
        showConfirmButton: false,
        timer: 3000,
        position: "top",
        toast: true,
        width: "17.8rem",
      });
    },
    //Modal de error, recibe como parametro el mensaje de error a mostrar
    modalInfo: function (msj) {
      swal.fire({
        title: "Atención",
        html: `${msj}`,
        icon: "info",
        showConfirmButton: false,
        timer: 5000,
        position: "top-end",
        toast: true,
        width: "auto",
      });
    },
    deplegarSidebar: function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        sidebar.classList.toggle('open');
        // Adjust button icon based on sidebar state
        if (sidebar.classList.contains('open')) {
            toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        }  
    },
    moverIndicador: function (event) {
        const indicator = document.getElementById('indicator');
        const item = event.currentTarget; // El li que disparó el evento
        const itemHeight = item.offsetHeight;
        const offsetTop = item.offsetTop;
        indicator.style.top = `${offsetTop}px`;
        indicator.style.height = `${itemHeight}px`;
    },
  },
});
app1.mount("#app1");
