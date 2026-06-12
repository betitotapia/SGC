<div class="row">

  <div class="col-md-3">
    <div class="form-group">
      <label>Folio *</label>
      <input class="form-control" name="folio" value="{{ old('folio', $plan->folio) }}" required>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Fecha de apertura</label>
      <input type="date" class="form-control" name="open_date"
             value="{{ old('open_date', optional($plan->open_date)->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Origen *</label>
      <select class="form-control" name="origin" required>
        <option value="">Seleccione</option>
        <option value="Matríz de RYO" @selected(old('origin', $plan->origin) == 'Matríz de RYO')>Matríz de RYO</option>
        <option value="Auditoria" @selected(old('origin', $plan->origin) == 'Auditoria')>Auditoria</option>
        <option value="Funcionamiento del SGC" @selected(old('origin', $plan->origin) == 'Funcionamiento del SGC')>Funcionamiento del SGC</option>
      </select>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
        <label>Tipo de hallazgo *</label>
        <select class="form-control" name="finding_type" required>
            <option value="">Seleccione</option>
            <option value="Oportunidad" @selected(old('finding_type', $plan->finding_type) == 'Oportunidad')>Oportunidad</option>
            <option value="Oportunidad de Mejora (OM)" @selected(old('finding_type', $plan->finding_type) == 'Oportunidad de Mejora (OM)')>Oportunidad de Mejora (OM)</option>
            <option value="Riesgo" @selected(old('finding_type', $plan->finding_type) == 'Riesgo')>Riesgo</option>
            <option value="NC Menor" @selected(old('finding_type', $plan->finding_type) == 'NC Menor')>NC Menor</option>
            <option value="NC Mayor" @selected(old('finding_type', $plan->finding_type) == 'NC Mayor')>NC Mayor</option>
            <option value="Observación" @selected(old('finding_type', $plan->finding_type) == 'Observación')>Observación</option>
        </select>
    </div>
</div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Detectada por</label>
      <input class="form-control" name="detected_by" value="{{ old('detected_by', $plan->detected_by) }}">
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Tipo de auditor</label>
      <select class="form-control" name="auditor_type">
        <option value="">Seleccione</option>
        <option value="INTERNO" @selected(old('auditor_type', $plan->auditor_type) == 'INTERNO')>INTERNO</option>
        <option value="EXTERNO" @selected(old('auditor_type', $plan->auditor_type) == 'EXTERNO')>EXTERNO</option>
      </select>
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Proceso</label>
      <select class="form-control" name="process">
        <option value="">Seleccione</option>
        <option value="PRO-DIR-01 PROCESO DE DIRECCIÓN" @selected(old('process', $plan->process) == 'PRO-DIR-01 PROCESO DE DIRECCIÓN')>PRO-DIR-01 PROCESO DE DIRECCIÓN</option>
        <option value="PRO-SO-01 PROCESO DE SOPORTE ORGANIZACIONAL" @selected(old('process', $plan->process) == 'PRO-SO-01 PROCESO DE SOPORTE ORGANIZACIONAL')>PRO-SO-01 PROCESO DE SOPORTE ORGANIZACIONAL</option>
        <option value="PRO-RH-01 PROCESO DE RECURSOS HUMANOS" @selected(old('process', $plan->process) == 'PRO-RH-01 PROCESO DE RECURSOS HUMANOS')>PRO-RH-01 PROCESO DE RECURSOS HUMANOS</option>
        <option value="PRO-AF-01 PROCESO DE ADMINISTRACIÓN Y FINANZAS" @selected(old('process', $plan->process) == 'PRO-AF-01 PROCESO DE ADMINISTRACIÓN Y FINANZAS')>PRO-AF-01 PROCESO DE ADMINISTRACIÓN Y FINANZAS</option>
        <option value="PRO-COM-01 PROCESO DE COMPRAS" @selected(old('process', $plan->process) == 'PRO-COM-01 PROCESO DE COMPRAS')>PRO-COM-01 PROCESO DE COMPRAS</option>
        <option value="PRO-VNT-01 PROCESO DE VENTAS" @selected(old('process', $plan->process) == 'PRO-VNT-01 PROCESO DE VENTAS')>PRO-VNT-01 PROCESO DE VENTAS</option>
        <option value="PRO-AT-01 PROCESO DE ATENCIÓN A CLIENTES" @selected(old('process', $plan->process) == 'PRO-AT-01 PROCESO DE ATENCIÓN A CLIENTES')>PRO-AT-01 PROCESO DE ATENCIÓN A CLIENTES</option>
        <option value="PRO-AL-01 PROCESO DE ALMACÉN Y LOGÍSTICA" @selected(old('process', $plan->process) == 'PRO-AL-01 PROCESO DE ALMACÉN Y LOGÍSTICA')>PRO-AL-01 PROCESO DE ALMACÉN Y LOGÍSTICA</option>
        <option value="PRO-SMAM-01 PROCESO DE SEG., MED., ANÁLISIS Y MEJORA" @selected(old('process', $plan->process) == 'PRO-SMAM-01 PROCESO DE SEG., MED., ANÁLISIS Y MEJORA')>PRO-SMAM-01 PROCESO DE SEG., MED., ANÁLISIS Y MEJORA</option>
      </select>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Hallazgo *</label>
      <textarea class="form-control" name="finding" rows="3" required>{{ old('finding', $plan->finding) }}</textarea>
    </div>
  </div>

 

  <div class="col-md-4">
    <div class="form-group">
      <label>Departamento</label>
      <select class="form-control" name="department_id">
        <option value="">Seleccione</option>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected(old('department_id', $plan->department_id) == $d->id)>
            {{ $d->name }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label>Responsable (propietario)</label>
      <input class="form-control" name="owner_name" value="{{ old('owner_name', $plan->owner_name) }}">
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
        <label>Email del responsable</label>
        <input type="email"
               class="form-control"
               name="owner_email"
               value="{{ old('owner_email', $plan->owner_email) }}"
               placeholder="responsable@empresa.com">
    </div>
</div>
  <div class="col-md-4">
    <div class="form-group">
      <label>Responsable de soporte / seguimiento</label>
      <select class="form-control" name="owner_id">
        <option value="">-- Sin asignar --</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('owner_id', $plan->owner_id) == $u->id)>
            {{ $u->name }} ({{ $u->email }})
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Fecha compromiso</label>
      <input type="date" class="form-control" name="commitment_date"
             value="{{ old('commitment_date', optional($plan->commitment_date)->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Fecha cierre</label>
      <input type="date" class="form-control" name="close_date"
             value="{{ old('close_date', optional($plan->close_date)->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Status *</label>
      <input class="form-control" name="status" value="{{ old('status', $plan->status ?? 'ABIERTO') }}" required>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <label>Progreso</label>
      <input class="form-control" value="{{ $plan->progress ?? 0 }}%" disabled>
    </div>
  </div>

  <div class="col-12">
    <div class="form-group">
      <label>Observaciones</label>
      <textarea class="form-control" name="notes" rows="2">{{ old('notes', $plan->notes) }}</textarea>
    </div>
  </div>

</div>