var apiEndpoint = '/EMRApp/module/ingreso_cita/function/ajax_functions.php?FUNC=';
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
  mounted: function () { },
  computed: {
    // Computed que actualiza el motivo del textarea
    selectedCitaDesc() {
      // Encuentra la opción seleccionada basándose en el TC_ID
      if (this.cita.tc_id) {
        const selectedOption = this.tipoCitaOptions.find(option => option.TC_ID === this.cita.tc_id);
        return selectedOption ? selectedOption.TC_DESC : '';  // Si hay una opción seleccionada, retorna la descripción
      }
      
    }
  },
  watch: {
    'cita.tc_id'(newValue, oldValue){
      if (newValue == 5) {
        this.cita.cita_seguimiento = "S";
        this.citaSeguimiento = true;
      } else {
        this.cita.cita_seguimiento = "N";
        this.cita.corr_padre = "";
        this.cita.anio_padre = "";
        this.disabledCita = false;
        this.citaSeguimiento = false;
      }
    },
  },
  created() {
    const queryParams = new URLSearchParams(window.location.search);
    if (queryParams.get('pac_corr')) {
      this.cita.pac_corr = queryParams.get('pac_corr');
    }

    if (queryParams.get('pac_docnum')) {
      this.cita.pac_docnum = queryParams.get('pac_docnum');
    }

    if (queryParams.get('cm_corr')) {
      this.cita.corr_padre = queryParams.get('cm_corr');
    }

    if (queryParams.get('cm_anio')) {
      this.cita.anio_padre = queryParams.get('cm_anio');
    }

    if (this.cita.anio_padre && this.cita.corr_padre) {
      this.cita.tc_id = 5;
    }

    this.getPaciente(this.cita.pac_corr,this.cita.pac_docnum);
    this.disabledCitaM();
    this.getTipoCita();
  },
  data() {
    return {
      mostrarAnimacion: false,
      required: true,
      disabled: false,
      disabledCita: false,
      citaSeguimiento: false,
      tipoCitaOptions: {},
      cita: {
        motivo: "",
        diagnostico: "",
        tratamiento: "",
        observaciones: "",
        cita_seguimiento: "N",
        arch_receta: null,
        corr_padre: "",
        anio_padre: "",
        pac_corr: "",
        pac_docnum: "",
        tc_id: "",
      },
      datClinico: {
        pesoLB: "",
        altura: "",
        presionArt: "",
        freqCardiaca: "",
        medGlucosa: "",
      },
      datosPaciente: {},

    };
  },
  methods: {
    getPaciente: async function (corr,docnum) {
      if (!corr || !docnum) {        
        return;
      }

      var raw = JSON.stringify({
        pac_corr: corr,
        pac_docnum: docnum,
      });

      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        body: raw,
        redirect: "follow",
      };

      try {
        this.mostrarAnimacion = true;
        fetch(apiEndpoint + 'getPaciente',requestOptions)
            .then(response => {         
              return response.json();
            })
            .then(respuesta => {
              if (respuesta.estado) {
                this.datosPaciente = respuesta.desc;
                this.disabled = true;
                this.modalSuccess("Paciente obtenido con éxito.");
              } else {
                this.datosPaciente = {};
                this.modalError(respuesta.desc);
              }
              this.mostrarAnimacion = false;
            })
            .catch((error) => {
              this.modalErrorApi(error);
              this.mostrarAnimacion = false;
            });
      } catch (error) {
        this.modalErrorApi(error);
        this.mostrarAnimacion = false;
      } finally {
        this.mostrarAnimacion = false;
      }
    },
    disabledCitaM: function () {
      if (this.cita.corr_padre && this.cita.anio_padre) {
        this.disabledCita = true;
      }
    },
    getTipoCita: function () {
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getTipoCita',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {
          this.tipoCitaOptions = datos;          
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    validarFormulario: function() {
      // VALIDACIONES DE CAMPOS REQUERIDOS
      var forms = document.querySelectorAll('.needs-validation');
      const vm = this;
      // Bucle sobre ellos y evitar la submition
      Array.prototype.slice.call(forms)
        .forEach(function (form) {          
          event.stopPropagation();

          //Agrega la clase que da estilos a los campos valios e invalidos
          form.classList.add('was-validated');

          if (form.checkValidity()) {
            vm.guardarCita();
          }
        });
    },
    guardarCita: async function(){
      //Mostrar animación de carga
      this.mostrarAnimacion = true;

      //Se deshabilita el boton para evitar un doble envío
      btnGuardar = document.querySelector('.btnGuardar');
      btnGuardar.disabled = true;

      try {

        var idUnico = this.generarIdUnico();

        var data = await this.guardarReceta(idUnico);
                
        if (data && data[0] && data[0].archivo && data[0].archivo != "error") {
          this.cita.arch_receta = data[0].archivo;
        } 

        console.log(this.cita.arch_receta);
        
        var raw = JSON.stringify({
          cita: this.cita,
          datClinico: this.datClinico,
        });
        console.log(this.cita);
        console.log(this.datClinico);
        var requestOptions = {
          method: "POST",
          headers: { "Content-Type": "application/json; charset=utf-8" },
          body: raw,
          redirect: "follow",
        };

        fetch(apiEndpoint + 'guardarCita',requestOptions)
          .then(response => {         
            return response.json();
          })
          .then(respuesta => {
            if (respuesta.estado) {
              this.modalSuccess(respuesta.desc).then(() => {
                location.reload(true);
              });
            } else {
              btnGuardar.disabled = false;
              this.modalError(respuesta.desc);
            }
          })
          .catch((error) => {
            this.modalErrorApi(error);
            btnGuardar.disabled = false;
            this.mostrarAnimacion = false;
          });
        
      } catch (error) {
        this.modalErrorApi(error)
      } finally {
        this.mostrarAnimacion = false;
      }
    },
    generarIdUnico: function() {
      return Date.now().toString(36) + Math.random().toString(36).substr(2);
    },    
    guardarReceta: async function (idUnico) {
      const formData = new FormData();
      const fileInputs = document.querySelectorAll('#formFile');
      let archivoCargado = false;
      
      let fileName;
      fileInputs.forEach(fileInput => {
        // Asegúrate de que el input tenga archivos seleccionados
        if (fileInput.files && fileInput.files.length > 0) {
            archivoCargado = true;
    
            // Obtén el nombre del archivo seleccionado
            const fullFileName = fileInput.files[0].name; // Nombre completo con extensión
            const fileNameWithoutExtension = fullFileName.split('.').slice(0, -1).join('.'); // Solo el nombre sin la extensión
    
            fileName = fileNameWithoutExtension + "-" + idUnico; // Usa el nombre sin extensión
            
            formData.append('ARCHIVO[]', fileInput.files[0]);
            formData.append('fileName[]', fileName);
        }
    });
      

      if (!archivoCargado) {
        return;
      }
      
      formData.append('ROOT', 'receta_medica');
      
      return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: '/EMRApp/API/guardarArchivos.php',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {                        
            },
            complete: function() {         
            },
            success: function(response) {
                var respuestas = JSON.parse(response);     
                resolve(respuestas); // Devuelve la respuesta con resolve
            },
            error: function(request, status, error) {
                // Manejar errores               
                var respuestaAPI = JSON.parse(request.responseText);
                this.mostrarAnimacion = false;

                reject(respuestaAPI); // Devuelve el error con reject
            }
        });
    });
    },
    getEstCivil: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getEstadoCivil',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {
          this.estadoCivilOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    getTipoSangre: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getTipoSangre',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {          
          this.tipoSangreOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    getNacionalidad: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getNacionalidad',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {                
          this.nacOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
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