<div class="row">

  {{-- Folio (solo lectura en edición) --}}
  @if($document->exists && $document->folio)
    <div class="col-md-3">
      <div class="form-group">
        <label>Folio</label>
        <input class="form-control" value="{{ $document->folio }}" disabled>
        <small class="text-muted">Generado automáticamente</small>
      </div>
    </div>
  @endif

  {{-- Título --}}
  <div class="col-md-{{ $document->exists ? '9' : '12' }}">
    <div class="form-group">
      <label>Título *</label>
      <input class="form-control @error('title') is-invalid @enderror"
             name="title"
             value="{{ old('title', $document->title) }}"
             placeholder="Ej. Procedimiento de Control de Documentos"
             required>
      @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Tipo --}}
  <div class="col-md-4">
    <div class="form-group">
      <label>Tipo de documento *</label>
      <select class="form-control @error('type') is-invalid @enderror" name="type" required>
        <option value="">Seleccione</option>
        @foreach(\App\Models\Document::TYPE_LABELS as $value => $label)
          <option value="{{ $value }}" @selected(old('type', $document->type) === $value)>{{ $label }}</option>
        @endforeach
      </select>
      @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Departamento --}}
  <div class="col-md-4">
    <div class="form-group">
      <label>Departamento dueño *</label>
      <select class="form-control @error('department_id') is-invalid @enderror" name="department_id" required>
        <option value="">Seleccione</option>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected(old('department_id', $document->department_id) == $d->id)>
            {{ $d->name }}
          </option>
        @endforeach
      </select>
      @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  @if(!$document->exists)
    <div class="col-md-4 d-flex align-items-center">
      <div class="alert alert-info mb-0 w-100 py-2">
        <small>
          <i class="fas fa-info-circle"></i>
          El folio se genera automáticamente al guardar (ej. <strong>PRO-RH-001</strong>)
        </small>
      </div>
    </div>
  @endif

</div>

{{-- ── Firmantes del documento ─────────────────────────────────────────── --}}
<hr>
<h5 class="font-weight-bold mb-1">
  <i class="fas fa-pen-nib mr-1 text-muted"></i>Firmantes
</h5>
<p class="text-muted small mb-3">
  Define quién firmará este documento. Pueden ser personas del área, de calidad y de dirección.
</p>

<div class="row">

  {{-- Elaboró --}}
  <div class="col-md-4">
    <div class="form-group">
      <label>
        <span class="badge badge-secondary mr-1">1</span>
        Elaboró
        <small class="text-muted">(autor del documento)</small>
      </label>
      <select class="form-control @error('elaboro_id') is-invalid @enderror" name="elaboro_id">
        <option value="">— Sin asignar —</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('elaboro_id', $document->elaboro_id) == $u->id)>
            {{ $u->name }}
          </option>
        @endforeach
      </select>
      @error('elaboro_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <input type="text"
             class="form-control form-control-sm @error('elaboro_cargo') is-invalid @enderror"
             name="elaboro_cargo"
             value="{{ old('elaboro_cargo', $document->elaboro_cargo) }}"
             placeholder="Cargo (ej. Gerente de TI)">
      @error('elaboro_cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Revisó --}}
  <div class="col-md-4">
    <div class="form-group">
      <label>
        <span class="badge badge-warning mr-1">2</span>
        Revisó
        <small class="text-muted">(personal de calidad)</small>
      </label>
      <select class="form-control @error('reviso_id') is-invalid @enderror" name="reviso_id">
        <option value="">— Sin asignar —</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('reviso_id', $document->reviso_id) == $u->id)>
            {{ $u->name }}
          </option>
        @endforeach
      </select>
      @error('reviso_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <input type="text"
             class="form-control form-control-sm @error('reviso_cargo') is-invalid @enderror"
             name="reviso_cargo"
             value="{{ old('reviso_cargo', $document->reviso_cargo) }}"
             placeholder="Cargo (ej. Coordinador de Calidad)">
      @error('reviso_cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Autorizó --}}
  <div class="col-md-4">
    <div class="form-group">
      <label>
        <span class="badge badge-success mr-1">3</span>
        Autorizó
        <small class="text-muted">(dirección / gerencia)</small>
      </label>
      <select class="form-control @error('autorizo_id') is-invalid @enderror" name="autorizo_id">
        <option value="">— Sin asignar —</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('autorizo_id', $document->autorizo_id) == $u->id)>
            {{ $u->name }}
          </option>
        @endforeach
      </select>
      @error('autorizo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <input type="text"
             class="form-control form-control-sm @error('autorizo_cargo') is-invalid @enderror"
             name="autorizo_cargo"
             value="{{ old('autorizo_cargo', $document->autorizo_cargo) }}"
             placeholder="Cargo (ej. Director General)">
      @error('autorizo_cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>

</div>
