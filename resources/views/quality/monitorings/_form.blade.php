<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="period" class="form-control"
                   value="{{ old('period', $monitoring->period) }}"
                   placeholder="Ej. Mensual / Trimestral / 30 días">
        </div>
    </div>

    <div class="col-md-5">
        <div class="form-group">
            <label>Actividad a monitorear *</label>
            <textarea name="activity_to_monitor" class="form-control" rows="2" required>{{ old('activity_to_monitor', $monitoring->activity_to_monitor) }}</textarea>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Responsable</label>
            <input type="text" name="responsible_name" class="form-control"
                   value="{{ old('responsible_name', $monitoring->responsible_name) }}">
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>¿Eficaz?</label>
            <select name="is_effective" class="form-control">
                <option value="">Seleccione</option>
                <option value="1" @selected(old('is_effective', $monitoring->is_effective) === true || old('is_effective', $monitoring->is_effective) === 1 || old('is_effective', $monitoring->is_effective) === '1')>SI</option>
                <option value="0" @selected(old('is_effective', $monitoring->is_effective) === false || old('is_effective', $monitoring->is_effective) === 0 || old('is_effective', $monitoring->is_effective) === '0')>NO</option>
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Meta objetivo</label>
            <textarea name="target_goal" class="form-control" rows="2">{{ old('target_goal', $monitoring->target_goal) }}</textarea>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Objetivo alcanzado (%)</label>
            <input type="number" name="goal_percentage" class="form-control"
                   min="0" max="100"
                   value="{{ old('goal_percentage', $monitoring->goal_percentage) }}">
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Fecha cierre acción</label>
            <input type="date" name="action_close_date" class="form-control"
                   value="{{ old('action_close_date', optional($monitoring->action_close_date)->format('Y-m-d')) }}">
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Observaciones</label>
            <textarea name="final_result" class="form-control" rows="3">{{ old('final_result', $monitoring->final_result) }}</textarea>
        </div>
    </div>
</div>
