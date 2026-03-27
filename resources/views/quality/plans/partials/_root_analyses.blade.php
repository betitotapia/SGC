<div class="d-flex align-items-center justify-content-between mt-4 mb-2">
    <h4 class="m-0">Análisis de Causa Raíz</h4>

    @can('quality.plans.update')
        <a class="btn btn-primary btn-sm" href="{{ route('quality.root-analyses.create', $plan) }}">
            Agregar análisis
        </a>
    @endcan
</div>

<div class="card">
    <div class="card-body">
        @forelse($plan->rootAnalyses as $analysis)
            <div class="border rounded p-3 mb-3">
                <div class="mb-3">
                    <strong>Descripción del análisis de causa raíz</strong>
                    <div>{{ $analysis->analysis_description ?: 'Sin descripción' }}</div>
                </div>

                <div class="mb-3">
                    <strong>Equipo de análisis</strong>
                    @if(!empty($analysis->analysis_team))
                        <ul class="mb-0">
                            @foreach($analysis->analysis_team as $member)
                                <li>{{ $member['name'] ?? '' }} @if(!empty($member['position'])) - {{ $member['position'] }} @endif</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Sin integrantes registrados</div>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Evidencias</strong>
                    @if($analysis->files->count())
                        <ul class="mb-0">
                            @foreach($analysis->files as $file)
                                <li>
                                    <a href="{{ asset('storage/'.$file->path) }}" target="_blank">{{ $file->original_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Sin archivos adjuntos</div>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Comentarios</strong>
                    <div>{{ $analysis->comments ?: 'Sin comentarios' }}</div>
                </div>

                @can('quality.plans.update')
                    <div class="text-right">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('quality.root-analyses.edit', [$plan, $analysis]) }}">
                            Editar
                        </a>

                        <form method="POST" action="{{ route('quality.root-analyses.destroy', [$plan, $analysis]) }}"
                              class="d-inline" onsubmit="return confirm('¿Eliminar análisis?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="text-muted">Sin análisis de causa raíz registrados.</div>
        @endforelse
    </div>
</div>
