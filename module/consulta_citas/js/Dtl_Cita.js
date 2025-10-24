var apiEndpoint = '/EMRApp/module/consulta_citas/function/ajax_functions.php?FUNC=';
var Headers = {
  json: { header: 'Content-Type', value: 'application/json' },
  form: { header: 'Content-Type', value: 'application/x-www-form-urlencoded' }
};

const { createApp } = Vue;

const app = createApp({
  directives: {
    upper: {
      update(el) {
        el.value = el.value.toUpperCase();
      },
    },
  },
  mounted: function () {
  },
  data() {
    return {
        mostrarAnimacion: false,
        CM_CORR: null,
        CM_ANIO: null,
        cita: null,
        disabled: true,
        required: false,
    };
  },
  created() {
    const queryParams = new URLSearchParams(window.location.search);
    this.CM_CORR = queryParams.get('cm_corr');
    this.CM_ANIO = queryParams.get('cm_anio');    
    this.getCita();
  },
  methods: {
    getCita: function (){
        try {

            var raw = JSON.stringify({
                cm_corr: this.CM_CORR,
                cm_anio: this.CM_ANIO,
            });

            var requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json; charset=utf-8" },
                body: raw,
                redirect: "follow",
            };
    
            fetch(apiEndpoint + 'getCita',requestOptions)
            .then(response => {         
                return response.json();
            })
            .then(respuesta => {
                if (respuesta.estado) {
                    console.log(respuesta.desc[0]);                  
                    this.cita = respuesta.desc[0];
                } else {
                    this.modalError(respuesta.desc);
                }
            })
            .catch((error) => {
                this.modalErrorApi(error);
            });
        
        
        } catch (error) {
        this.modalErrorApi(error)
        } finally {
        }
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
        position: "top-end",
        toast: true,
        width: "17.8rem",
      });
    },
  },
});
app.mount('#app');