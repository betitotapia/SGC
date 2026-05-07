<div class="row">

  {{-- Departamento --}}
  <div class="col-md-6">
    <div class="form-group">
      <label>Departamento *</label>
      <select class="form-control @error('department_id') is-invalid @enderror"
              name="department_id" required>
        <option value="">Seleccione</option>
        @foreach($departments as $d)
          <option value="{{ $d->id }}"
                  @selected(old('department_id', $approvalTemplate->department_id ?? request('department_id')) == $d->id)>
            {{ $d->name }}
          </option>
        @endforeach
      </select>
      @error('department_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Tipo de documento --}}
  <div class="col-md-6">
    <div class="form-group">
      <label>Tipo de documento</label>
      <select class="form-control @error('document_type') is-invalid @enderror" name="document_type">
        <option value="">— Todos los tipos —</option>
        @foreach(\App\Models\Document::TYPE_LABELS as $value => $label)
          <option value="{{ $value }}"
                  @selected(old('document_type', $approvalTemplate->document_type ?? '') === $value)>
            {{ $label }}
          </option>
        @endforeach
      </select>
      <small class="text-muted">
        Deja en blanco para que aplique a todos los tipos de documento del departamento.
      </small>
      @error('document_type')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Firmante --}}
  <div class="col-md-6">
    <div class="form-group">
      <label>Firmante *</label>
      <select class="form-control @error('user_id') is-invalid @enderror" name="user_id" required>
        <option value="">Seleccione un usuario</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}"
                  @selected(old('user_id', $approvalTemplate->user_id ?? '') == $u->id)>
            {{ $u->name }} — {{ $u->email }}
          </option>
        @endforeach
      </select>
      @error('user_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Rol en la aprobación --}}
  <div class="col-md-3">
    <div class="form-group">
      <label>Rol *</label>
      <select class="form-control @error('role_in_approval') is-invalid @enderror"
              name="role_in_approval" required>
        <option value="">Seleccione</option>
        @foreach(\App\Models\DocumentApproval::ROLE_LABELS as $value => $label)
          <option value="{{ $value }}"
                  @selected(old('role_in_approval', $approvalTemplate->role_in_approval ?? '') === $value)>
            {{ $label }}
          </option>
        @endforeach
      </select>
      @error('role_in_approval')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Orden de firma --}}
  <div class="col-md-3">
    <div class="form-group">
      <label>Orden de firma *</label>
      <input type="number"
             class="form-control @error('order') is-invalid @enderror"
             name="order"
             min="1" max="10"
             value="{{ old('order', $approvalTemplate->order ?? 1) }}"
             required>
      <small class="text-muted">1 = firma primero</small>
      @error('order')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

</div>

<div class="alert alert-light border mt-2">
  <i class="fas fa-lightbulb text-warning mr-1"></i>
  <strong>Ejemplo típico para un Procedimiento:</strong>
  <ul class="mb-0 mt-1 small">
    <li>Orden 1 — Elaboró (autor) — el redactor del procedimiento</li>
    <li>Orden 2 — Revisó (revisor) — el coordinador o jefe de área</li>
    <li>Orden 3 — Autorizó (aprobador) — el gerente</li>
  </ul>
</div>
