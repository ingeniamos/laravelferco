<div class="form-row align-items-center">
  <input type="hidden" id="idIndicador"></input>
  <div class="col-5">
    <label for="indicador1">Indicador</label>
    <input type="text" class="form-control mb-2 mb-sm-0" id="indicador1" placeholder="Nombre indicador">
  </div>
  <div class="col-3">
    <label for="Subgrupo1">Subgrupo</label>
    <select class="form-control select" id="selSubgrupo">
    </select>
  </div>
  <div class="col-4">
    <label for="responsable">Responsable</label>
    <select class="form-control select" id="responsable">
    </select>
  </div>
</div>
<div class="form-row align-items-center">
  <div class="col-3">
    <label for="valor1">Valor objetivo</label>
    <input type="number" class="form-control mb-2 mb-sm-0" id="valor1">
  </div>
  <div class="col-2">
    <label for="selEscala1">Escala</label>
    <select class="form-control" id="selEscala1">
      <option>-</option>
      <option>1</option>
      <option>10</option>
      <option>100</option>
      <option>1000</option>
    </select>
  </div>  
  <div class="col-3">
    <label for="selUnidad">Unidad</label>
    <select class="form-control select" id="selUnidad">
    </select>
  </div>
  <div class="col-4">
    <label for="fecha_inicio">Fecha inicio</label>
    <input type="date" class="form-control mb-2 mb-sm-0" id="fecha_inicio">
  </div>

</div>
<div class="form-row align-items-center">
  <div class="col-8">
    <label for="query">Consulta para obtener valor real</label>
    <input type="text" class="form-control mb-2 mb-sm-0" id="query">
  </div>
  <div class="col-4">
    <label for="fecha_fin">Fecha final</label>
    <input type="date" class="form-control mb-2 mb-sm-0" id="fecha_fin">
  </div>
</div>