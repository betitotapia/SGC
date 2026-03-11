<div class="d-flex align-items-center justify-content-between mt-4 mb-2">
    <h4 class="m-0">Monitoreo, valoración de eficacia y cierre de la acción</h4>

    @can('quality.plans.update')
        <a class="btn btn-primary btn-sm" href="{{ route('quality.monitorings.create', $plan) }}">
            Agregar monitoreo
        </a>
    @endcan
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Periodo</th>
                        <th>Actividad a monitorear</th>
                        <th>Responsable</th>
                        <th>Eficaz</th>
                        <th>Meta objetivo</th>
                        <th>Objetivo alcanzado (%)</th>
                        <th>Fecha cierre acción</th>
                        <th>Resultado final</th>
                        @can('quality.plans.update')
                            <th class="text-right">Acciones</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($plan->monitorings as $monitoring)
                        <tr>
                            <td>{{ $monitoring->period }}</td>
                            <td>{{ $monitoring->activity_to_monitor }}</td>
                            <td>{{ $monitoring->responsible_name }}</td>
                            <td>
                                @if($monitoring->is_effective === true)
                                    <span class="badge badge-success">SI</span>
                                @elseif($monitoring->is_effective === false)
                                    <span class="badge badge-danger">NO</span>
                                @else
                                    <span class="badge badge-secondary">N/D</span>
                                @endif
                            </td>
                            <td>{{ $monitoring->target_goal }}</td>
                            <td>{{ $monitoring->goal_percentage }}%</td>
                            <td>{{ optional($monitoring->action_close_date)->format('Y-m-d') }}</td>
                            <td>{{ $monitoring->final_result }}</td>

                            @can('quality.plans.update')
                                <td class="text-right">
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="{{ route('quality.monitorings.edit', [$plan, $monitoring]) }}">
                                        Editar
                                    </a>

                                    <form method="POST"
                                          action="{{ route('quality.monitorings.destroy', [$plan, $monitoring]) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar monitoreo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Sin monitoreos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
