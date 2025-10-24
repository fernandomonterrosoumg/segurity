var apiEndpoint = '/EMRApp/module/consulta_pacientes/function/ajax_functions.php?FUNC=';
var apiEndpoint2 = '/EMRApp/module/ingreso_paciente/function/ajax_functions.php?FUNC=';
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
    this.getEstCivil();
    this.getNacionalidad();
  },
  data() {
    return {
        mostrarAnimacion: false,
        DOC_ID: null,
        PAC_CORR: null,
        paciente: null,
        disabled: true,
        estadoCivilOptions: null,
        nacOptions: null,
        genero: null,
        required: false,
        timestamp: Date.now(),
    };
  },
  created() {
    const queryParams = new URLSearchParams(window.location.search);
    this.DOC_ID = queryParams.get('DOC');
    this.PAC_CORR = queryParams.get('CORR');    
    this.getPaciente();
  },
  methods: {
    getPaciente: function (){
        try {

            var raw = JSON.stringify({
                doc_id: this.DOC_ID,
                pac_corr: this.PAC_CORR,
            });

            var requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json; charset=utf-8" },
                body: raw,
                redirect: "follow",
            };
    
            fetch(apiEndpoint + 'getPaciente',requestOptions)
            .then(response => {         
                return response.json();
            })
            .then(respuesta => {
                if (respuesta.estado) {
                    console.log(respuesta.desc[0]);                  
                    this.paciente = respuesta.desc[0];
                    if (this.paciente.DC_GENERO == "H") {
                      this.genero = "HOMBRE";
                    } else {
                      this.genero = "MUJER";
                    }
                    this.timestamp = Date.now();
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
    updPaciente: async function () {
      try {

        this.mostrarAnimacion = true;

        var data = await this.guardarFoto(this.paciente.PAC_DOCNUM + '-' + this.paciente.PAC_CORR);

        if (data && data[0] && data[0].archivo && data[0].archivo != "error") {
          this.paciente.PAC_FOTO = data[0].archivo;
        } 
          
        var raw = JSON.stringify({
          paciente: this.paciente,
        });

        var requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json; charset=utf-8" },
            body: raw,
            redirect: "follow",
        };
          
        fetch(apiEndpoint + 'updPaciente',requestOptions)
        .then(response => {         
            return response.json();
        })
        .then(respuesta => {
            if (respuesta.estado) {
              this.modalSuccess(respuesta.desc);  
              this.getPaciente();
              this.disabled = true;
            } else {
                this.modalError(respuesta.desc);
            }
        })
        .catch((error) => {
            this.modalErrorApi(error);
            this.mostrarAnimacion = false;
        });
    
    
    } catch (error) {
      this.mostrarAnimacion = false;
      this.modalErrorApi(error);
    } finally {
      this.mostrarAnimacion = false;
    }
    },
    guardarFoto: async function (nombre) {
      const formData = new FormData();
      const fileInputs = document.querySelectorAll('#formFile');
      let archivoCargado = false;
      

      fileInputs.forEach(fileLput => {
          // Añade el archivo de cada elemento al FormData
          if (fileLput.files && fileLput.files.length > 0) {
              archivoCargado = true;
              formData.append('ARCHIVO[]', fileLput.files[0]);
              formData.append('fileName[]', nombre);
          }
      });

      if (!archivoCargado) {
        return;
      }
      
      formData.append('ROOT', 'foto_paciente');
      
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

      fetch(apiEndpoint2 + 'getEstadoCivil',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {
          this.estadoCivilOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    getNacionalidad: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint2 + 'getNacionalidad',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {                
          this.nacOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    ingresarCita: function () {
      window.location.href = '/EMRApp/ingresoCita.php?pac_corr='+ this.paciente.PAC_CORR +'&pac_docnum=' + this.paciente.PAC_DOCNUM;
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