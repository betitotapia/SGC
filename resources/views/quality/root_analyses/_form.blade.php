<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Descripción del análisis de causa raíz</label>
            <textarea name="analysis_description" class="form-control" rows="4">{{ old('analysis_description', $analysis->analysis_description) }}</textarea>
        </div>
    </div>

    <div class="col-12">
        <label>Equipo de análisis</label>

        <div id="team-container">
            @php
                $oldNames = old('team_names');
                $oldPositions = old('team_positions');

                $teamData = $oldNames
                    ? collect($oldNames)->map(function ($name, $i) use ($oldPositions) {
                        return [
                            'name' => $name,
                            'position' => $oldPositions[$i] ?? '',
                        ];
                    })->all()
                    : ($analysis->analysis_team ?? [['name' => '', 'position' => '']]);
            @endphp

            @foreach($teamData as $member)
                <div class="row mb-2 team-row">
                    <div class="col-md-6">
                        <input type="text"
                               name="team_names[]"
                               class="form-control"
                               placeholder="Nombre"
                               value="{{ $member['name'] ?? '' }}">
                    </div>

                    <div class="col-md-5">
                        <input type="text"
                               name="team_positions[]"
                               class="form-control"
                               placeholder="Cargo"
                               value="{{ $member['position'] ?? '' }}">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-block remove-team-row">-</button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-team-row">
            Agregar integrante
        </button>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Evidencia de carga de archivos</label>
            <input type="file" name="files[]" class="form-control" multiple>
            <small class="text-muted">Puedes adjuntar documentos, imágenes y otros archivos.</small>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Comentarios (opcional)</label>
            <textarea name="comments" class="form-control" rows="3">{{ old('comments', $analysis->comments) }}</textarea>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('team-container');
    const addBtn = document.getElementById('add-team-row');

    addBtn?.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row mb-2 team-row';
        row.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="team_names[]" class="form-control" placeholder="Nombre">
            </div>
            <div class="col-md-5">
                <input type="text" name="team_positions[]" class="form-control" placeholder="Cargo">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-block remove-team-row">-</button>
            </div>
        `;
        container.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-team-row')) {
            const rows = document.querySelectorAll('.team-row');
            if (rows.length > 1) {
                e.target.closest('.team-row').remove();
            }
        }
    });
});
</script>
@endpush