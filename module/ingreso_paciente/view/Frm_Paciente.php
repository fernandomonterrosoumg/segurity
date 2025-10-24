<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EMRApp</title>

    <link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
    <link rel="stylesheet" href="/EMRApp/module/ingreso_paciente/css/Frm_Paciente.css">
    <link rel="icon" type="image/webp" href="/EMRApp/include/Images/esculapio1.webp">
  </head>
  <body>
    <div id="app">
      <form class="needs-validation" action="" novalidate>
      <div class="card" style="z-index: 0 !important;">
        <div class="card-header" style="padding: 12px;">

          <div class="row" style="margin: 0;">
            <div class="col-6"><img src="/EMRApp/include/Images/esculapio1.webp" alt="" width="116px" class=""></div>
            <div class="col-6 title-p-1">
              <h1 class="title-card" style="margin-right: 10px;">Ingreso de Paciente</h1>
              <img src="/EMRApp/include/Images/paciente.png" alt="" width="50px">
            </div>
          </div>
          
        </div>
        
        <div class="card-body">
            
          <div class="form-seccion-1">
            <div class="form-title">
              <h2>Datos Personales</h2>
            </div>
            
            <div class="mb-3">
              <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Nombres del Paciente <span style="color: red; font-weight: bold;">*</span></label>
              <div class="row">
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Primer Nombre" v-model="paciente.nombre1" :required="required">
                </div>
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom02" placeholder="Segundo Nombre (Opcional)" v-model="paciente.nombre2" >
                </div>
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom03" placeholder="Tercer Nombre (Opcional)" v-model="paciente.nombre3">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="validationCustom04 validationCustom05 validationCustom06" class="form-label">Apellidos del Paciente <span style="color: red; font-weight: bold;">*</span></label>
              <div class="row">
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom04" placeholder="Primer Apellido" v-model="paciente.apellido1" :required="required">
                </div>
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom05" placeholder="Segundo Apellido (Opcional)" v-model="paciente.apellido2" >
                </div>
                <div class="col">                  
                  <input type="text" class="form-control" id="validationCustom06" placeholder="Apellido de Casado (Opcional)" v-model="paciente.apellidoC">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom07" class="form-label">Documento de Identificación <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="text" class="form-control" id="validationCustom07" placeholder="Ingresar correctamente, ya que no se admiten cambios." v-model="paciente.docId" :required="required">
                </div>
                <div class="col">
                  <label for="formDate" class="form-label">Fecha de Naciemiento <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="date" class="form-control" id="formDate" v-model="paciente.fecNac" :required="required">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="formFile" class="form-label">Foto del Paciente</label>
                  <input class="form-control" type="file" id="formFile" accept="image/*">
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom08" class="form-label">Ocupación</label>
                  <input type="text" class="form-control" id="validationCustom08" placeholder="Ej. Programador" v-model="paciente.ocupacion">
                </div>
                <div class="col">
                  <label for="validationCustom09" class="form-label">Nacionalidad  <span style="color: red; font-weight: bold;">*</span></label>
                  <select class="form-select" id="validationCustom09 autoSizingSelect" v-model="paciente.nacionalidadId" :required="required">
                    <option disabled selected value="">--Seleccionar--</option>
                    <option v-for="nacOption in nacOptions" :value="nacOption.NAC_ID">{{nacOption.NAC_DESC}}</option>
                  </select>
                </div>
                <div class="col">
                  <label for="validationCustom10" class="form-label">Estado Civil <span style="color: red; font-weight: bold;">*</span></label>
                  <select class="form-select" id="validationCustom10 autoSizingSelect" v-model="paciente.estadoCivil" :required="required">
                    <option disabled selected value="">--Seleccionar--</option>
                    <option v-for="ECOption in estadoCivilOptions" :value="ECOption.EC_ID">{{ECOption.EC_DESC}}</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

          <hr>

          <div class="form-seccion-2">
            <div class="form-title">
              <h2>Datos de Contacto</h2>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom11" class="form-label">Teléfono del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="number" min="0" class="form-control" id="validationCustom11" placeholder="Ej. 15353242" v-model="paciente.telefono" :required="required">
                </div>
                <div class="col">
                  <label for="inputEmail4" class="form-label">Correo del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="email" class="form-control" id="inputEmail4" placeholder="Ej. ejemplo@email.com" v-model="paciente.correo" :required="required">
                </div>
                <div class="col">
                  <label for="validationCustom12" class="form-label">Contacto de Emergencia</label>
                  <input type="number" min="0" class="form-control" id="validationCustom12" v-model="paciente.telEmergencia" placeholder="Ej. 12345678">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom13" class="form-label">Dirección de Domicilio</label>
                  <input type="text" class="form-control" id="validationCustom13" v-model="paciente.direccion" placeholder="Ej. 7a Ave. 'A' 17-78 Col. Reyna Barrios Z. 13">
                </div>
              </div>
            </div>
          </div>

          <hr>

          <div class="form-seccion-3">
            <div class="form-title">
              <h2>Datos Vitales y Biométricos</h2>
              <div class="mb-3">
                <div class="row">

                  <div class="col">
                    <label for="validationCustom14" class="form-label">Genero <span style="color: red; font-weight: bold;">*</span></label>
                    <div class="btn-group form-control genero" role="group" aria-label="Basic checkbox toggle button group" id="validationCustom14" :required="required">
                      <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" value="H" v-model="datClinico.genero" checked>
                      <label class="btn btn-outline-info" for="btnradio1"><strong>Hombre</strong> <img src="/EMRApp/include/Images/hombre.png" alt="hombre" class="generoH"></label>

                      <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" value="M" v-model="datClinico.genero">
                      <label class="btn btn-outline-warning" for="btnradio2"><strong>Mujer</strong> <img src="/EMRApp/include/Images/mujer.png" alt="mujer" class="generoM"></label>
                    </div>
                  </div>

                </div>
              </div>

                <div class="mb-3">
                  <div class="row">
                    <div class="col">
                      <label for="validationCustom15" class="form-label">Peso del Paciente</label>
                      <div class="input-group">                      
                        <input type="number" min="0" step="0.01" class="form-control" id="validationCustom15" v-model="datClinico.pesoLB" placeholder="Ej. 145.5">
                        <div class="input-group-text">lb</div>
                      </div>
                    </div>

                    <div class="col">
                      <label for="validationCustom16" class="form-label">Altura del Paciente</label>
                      <div class="input-group">
                        <input type="number" min="0" class="form-control" id="validationCustom16" v-model="datClinico.altura" placeholder="Ej. 170">
                        <div class="input-group-text">cm</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                <div class="row">

                  <div class="col">
                    <label for="validationCustom17" class="form-label">Tipo de Sangre</label>
                    <select class="form-select" id="validationCustom17 autoSizingSelect" v-model="datClinico.tipoSangre">
                      <option disabled selected value="">--Seleccionar--</option>
                      <option v-for="TSOption in tipoSangreOptions" :value="TSOption.TS_ID">{{TSOption.TS_DESC}}{{TSOption.TS_FACTOR_RH}}</option>
                    </select>
                  </div>

                  <div class="col">
                    <label for="validationCustom18" class="form-label">Presión Arterial</label>
                    <div class="input-group">                      
                      <input type="text" min="0" class="form-control" id="validationCustom18" v-model="datClinico.presionArt" placeholder="Ej. 120/80">
                      <div class="input-group-text">mmHg</div>
                    </div>
                  </div>

                  <div class="col">
                    <label for="validationCustom19" class="form-label">Frecuencia Cardiaca</label>
                    <div class="input-group">                      
                      <input type="number" min="0" class="form-control" id="validationCustom19" v-model="datClinico.freqCardiaca" placeholder="Ej. 100">
                      <div class="input-group-text">LPM</div>
                    </div>
                  </div>

                </div>
                </div>

            </div>
          </div>          
          
        </div>


        <div class="card-footer">
          <div class="container">
            <div class="row">
              <div class="col"></div>
              <div class="col-1"><button type="submit" class="btn btn-outline-success btnGuardar" @click.prevent="validarFormulario"><strong>Guardar</strong></button></div>
              <div class="col"></div>
            </div>
          </div>
        </div>
        
      </div>
      </form>

      <!--ANIMACIÓN DE CARGA -->
      <div v-if="this.mostrarAnimacion" class="centrar-sobreponer">
        <div class="spinner-box">
          <div class="blue-orbit leo"></div>

          <div class="green-orbit leo"></div>

          <div class="red-orbit leo"></div>

          <div class="white-orbit w1 leo"></div>
          <div class="white-orbit w2 leo"></div>
          <div class="white-orbit w3 leo"></div>
        </div>
      </div>

    </div>

    <script src="/EMRApp/include/js_lib/JQuery/3.7.1.js"></script>
    <script src="/EMRApp/include/js_lib/bootstrap/5.0.2.js"></script>
    <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
    <script src="/EMRApp/include/js_lib/VUEjs/3.4.33.js"></script>
    <script src="/EMRApp/module/ingreso_paciente/js/Frm_Paciente.js"></script>
  </body>
</html>
