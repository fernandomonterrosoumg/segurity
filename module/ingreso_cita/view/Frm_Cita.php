<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMRApp</title>
    <link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
    <link rel="stylesheet" href="/EMRApp/include/css_lib/general.css" />    
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
              <h1 class="title-card" style="margin-right: 10px;">Ingreso de Cita</h1>
              <iconify-icon icon="fluent-color:clipboard-text-edit-20" width="4em"></iconify-icon>
            </div>
          </div>
          
        </div>
        
        <div class="card-body">
            
          <div class="form-seccion-1">
            <div class="form-title">
              <h2>Datos Generales</h2>
            </div>
            
            <div class="mb-3">
              <div class="row">
                <div class="col-2">
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Id de Paciente <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Num. Correlativo" v-model="cita.pac_corr" :required="required" :disabled="disabled" @blur="getPaciente(cita.pac_corr,cita.pac_docnum)">
                </div>
                <div class="col-4">
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Documento de Identificación <span style="color: red; font-weight: bold;">*</span></label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Identificación del paciente" v-model="cita.pac_docnum" :required="required" :disabled="disabled" @blur="getPaciente(cita.pac_corr,cita.pac_docnum)">
                </div>
                <div class="col-6">
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Nombre del paciente</label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Nombre completo" v-model="datosPaciente.NOMBRE" :required="required" disabled>
                </div>
              </div>
            </div>

            <template v-if="(cita.corr_padre && cita.anio_padre) || citaSeguimiento">
              <div class="mb-3">
                <div class="row">              
                  <div class="col">
                    <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Correlativo de Cita Previa <span style="color: red; font-weight: bold;">*</span></label>
                    <input type="text" class="form-control" id="validationCustom01" placeholder="Número correlativo" v-model="cita.corr_padre" :required="required" :disabled="disabledCita" @blur="disabledCitaM()">
                  </div>
                  <div class="col">
                    <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Año de Cita Previa <span style="color: red; font-weight: bold;">*</span></label>
                    <input type="text" class="form-control" id="validationCustom01" placeholder="Número correlativo" v-model="cita.anio_padre" :required="required" :disabled="disabledCita" @blur="disabledCitaM()">
                  </div>
                </div>
              </div>
            </template>

          </div>

          <hr>

          <div class="form-seccion-2">
            <div class="form-title">
              <h2>Datos Vitales y Biométricos</h2>
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
                    <label for="validationCustom22" class="form-label">Nivel de Glucosa</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="validationCustom22" v-model="datClinico.medGlucosa" placeholder="Ej. De 90 a 130">
                      <div class="input-group-text">mg/dl</div>
                    </div>
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

          <hr>

          
          
          <div class="form-seccion-3">
            <div class="form-title">
              <h2>Datos de la Cita</h2>
            </div>
            <br>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                <label for="validationCustom17" class="form-label">Tipo de Cita <span style="color: red; font-weight: bold;">*</span></label>
                  <select class="form-select" id="validationCustom17 autoSizingSelect" v-model="cita.tc_id" :required="required">
                    <option disabled selected value="">--Seleccionar--</option>
                    <option v-for="TCOption in tipoCitaOptions" :value="TCOption.TC_ID">{{TCOption.TC_NOMBRE}}</option>
                  </select>
                </div>
                <div class="col">
                    <textarea class="form-control" disabled id="floatingTextarea" style="height: 100px">{{ selectedCitaDesc }}</textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom11" class="form-label">Motivo de la cita <span style="color: red; font-weight: bold;">*</span></label>
                  <textarea class="form-control" placeholder="Describa los padecimientos del paciente." v-model="cita.motivo" :required="required" id="floatingTextarea" style="height: 100px"></textarea>
                </div>
                <div class="col">
                  <label for="inputEmail4" class="form-label">Diagnóstico <span style="color: red; font-weight: bold;">*</span></label>
                  <textarea class="form-control" placeholder="Diagnostico final del paciente." v-model="cita.diagnostico" :required="required" id="floatingTextarea1" style="height: 100px"></textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom13" class="form-label">Tratamiento </label>
                  <input type="text" class="form-control" id="validationCustom13" placeholder="Tratamiento recomendado para el paciente." v-model="cita.tratamiento">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom13" class="form-label">Observaciones</label>
                  <textarea class="form-control" placeholder="Observaciones adicionales." v-model="cita.observaciones" id="floatingTextarea2" style="height: 100px"></textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="formFile" class="form-label">Receta Médica Firmada</label>
                  <input class="form-control" type="file" id="formFile" accept="image/*,application/pdf">
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
    <script src="/EMRApp/include/js_lib/iconify/2.1.0.js"></script>
    <script src="/EMRApp/module/ingreso_cita/js/Frm_Cita.js"></script>
</body>
</html>