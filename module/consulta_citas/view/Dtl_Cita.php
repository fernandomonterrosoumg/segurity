<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMRApp</title>
    <link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
    <link rel="stylesheet" href="/EMRApp/include/css_lib/general.css" />   
    <link rel="stylesheet" href="/EMRApp/module/consulta_citas/css/consulta_cita.css" />   
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
            <div class="text-container">
              <h1 class="title-card" style="margin-right: 10px;">Cita: {{CM_CORR}}-{{CM_ANIO}}</h1>
              <h3 class="date">Fecha de Cita: {{cita.CM_FECHA_CITA}}</h3>
            </div>
            <iconify-icon icon="fluent-color:receipt-32" width="5em"></iconify-icon>
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
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Id de Paciente </label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Num. Correlativo" v-model="cita.PAC_CORR"  :disabled="disabled">
                </div>
                <div class="col-4">
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Documento de Identificación </label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Identificación del paciente" v-model="cita.PAC_DOCNUM"  :disabled="disabled">
                </div>
                <div class="col-6">
                  <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Nombre del paciente</label>
                  <input type="text" class="form-control" id="validationCustom01" placeholder="Identificación del paciente" v-model="cita.NOMBRE"  disabled>
                </div>
              </div>
            </div>

            <template v-if="cita.CM_CORR_PADRE && cita.CM_ANIO_PADRE">
              <div class="mb-3">
                <div class="row">              
                  <div class="col">
                    <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Correlativo de Cita Previa </label>
                    <input type="text" class="form-control" id="validationCustom01" placeholder="Número correlativo" v-model="cita.CM_CORR_PADRE"  :disabled="disabled">
                  </div>
                  <div class="col">
                    <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Año de Cita Previa </label>
                    <input type="text" class="form-control" id="validationCustom01" placeholder="Número correlativo" v-model="cita.CM_ANIO_PADRE"  :disabled="disabled">
                  </div>
                </div>
              </div>
            </template>

          </div>

          <hr>

          <!--<div class="form-seccion-2">
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

          <hr>-->

          
          
          <div class="form-seccion-3">
            <div class="form-title">
              <h2>Datos de la Cita</h2>
            </div>
            <br>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                <label for="validationCustom17" class="form-label">Tipo de Cita</label>
                    <input type="text" class="form-control" id="validationCustom19" v-model="cita.TC_NOMBRE" :disabled="disabled">
                </div>
                <div class="col">
                    <textarea class="form-control" disabled id="floatingTextarea" style="height: 100px">{{ cita.TC_DESC }}</textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom11" class="form-label">Motivo de la cita </label>
                  <textarea class="form-control" placeholder="Describa los padecimientos del paciente." v-model="cita.CM_MOTIVO_CITA" :disabled="disabled" id="floatingTextarea" style="height: 100px"></textarea>
                </div>
                <div class="col">
                  <label for="inputEmail4" class="form-label">Diagnóstico </label>
                  <textarea class="form-control" placeholder="Diagnostico final del paciente." v-model="cita.CM_DIAGNOSTICO" :disabled="disabled" id="floatingTextarea1" style="height: 100px"></textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom13" class="form-label">Tratamiento </label>
                  <input type="text" class="form-control" id="validationCustom13" placeholder="Tratamiento recomendado para el paciente." :disabled="disabled" v-model="cita.CM_TRATAMIENTO">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="validationCustom13" class="form-label">Observaciones</label>
                  <textarea class="form-control" placeholder="Observaciones adicionales." v-model="cita.CM_OBSEVACIONES" :disabled="disabled" id="floatingTextarea2" style="height: 100px"></textarea>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col">
                  <label for="formFile" class="form-label">Archivo de Receta Médica</label>
                  <br>
                  <template v-if="cita.CM_ARCH_RECETA != null">
                    <a :href="'/EMRApp/adjunto/ArchivosRecetas/' + cita.CM_ARCH_RECETA" target="_blank">Archivo de receta médica</a>
                  </template>
                  <template v-else>
                    <strong>Receta médica no adjuntada</strong>
                  </template>
                </div>
              </div>
            </div>
          </div>
          
        </div>


        <div class="card-footer">
          <div class="container">
            <div class="row">
              <div class="col"></div>
              <div class="col-2">
                <a 
                  :href="'/EMRApp/ingresoCita.php?pac_corr=' + cita.PAC_CORR + '&pac_docnum=' + cita.PAC_DOCNUM + '&cm_corr=' + cita.CM_CORR + '&cm_anio=' + cita.CM_ANIO" class="btn btn-outline-info">
                    Ingresar Cita de Seguimiento
                </a>
              </div>
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
    <script src="/EMRApp/module/consulta_citas/js/Dtl_Cita.js"></script>
</body>
</html>