@extends('layouts.app')

@section('title','Reportes')

@section('content')

@php
    $periodoOptions = [
        'Este mes' => 'Este mes',
        'Últimos 3 meses' => 'Últimos 3 meses',
        'Últimos 6 meses' => 'Últimos 6 meses',
        'Este año' => 'Este año',
        'Personalizado' => 'Personalizado',
    ];

    $periodoActual = request('periodo', 'Últimos 6 meses');
    $periodoLabel = $periodoOptions[$periodoActual] ?? 'Últimos 6 meses';

    $metricas = [
        [
            'key' => 'ganancias',
            'detail' => 'Ganancias de tratamientos realizados en el periodo',
            'tone' => 'border-violet-200 bg-violet-50 text-violet-700',
        ],
        [
            'key' => 'pacientes',
            'detail' => 'Pacientes atendidos',
            'tone' => 'border-[#d3a2f2] bg-[#f6e8ff] text-[#8200b8]',
        ],
        [
            'key' => 'citas',
            'detail' => 'Citas registradas',
            'tone' => 'border-[#d3a2f2] bg-[#f6e8ff] text-[#9700cf]',
        ],
        [
            'key' => 'tratamientos',
            'detail' => 'Tratamientos activos',
            'tone' => 'border-violet-200 bg-violet-50 text-violet-700',
        ],
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-4xl font-bold text-slate-900">Reportes</h1>
            <p class="mt-2 max-w-2xl text-slate-500">Revisa ganancias, citas y pacientes del consultorio.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center justify-center gap-2 rounded-lg bg-[#9700cf] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#512569]">
                    Generar PDF
                    <span class="text-xs transition group-open:rotate-180">⌄</span>
                </summary>
                <div class="absolute right-0 z-20 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                    @foreach([
                        'ganancias' => 'Reporte de ganancias',
                        'citas' => 'Reporte de citas',
                        'pacientes' => 'Reporte de pacientes',
                        'tratamientos' => 'Reporte de tratamientos',
                    ] as $exportKey => $exportLabel)
                        <a href="{{ route('reportes.export', array_merge(request()->query(), ['reporte' => $exportKey])) }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]">{{ $exportLabel }} en PDF</a>
                    @endforeach
                </div>
            </details>
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center justify-center gap-2 rounded-lg border border-[#9700cf] bg-white px-5 py-3 text-sm font-semibold text-[#9700cf] shadow-sm transition hover:bg-[#fbf0ff]">
                    Generar Excel
                    <span class="text-xs transition group-open:rotate-180">⌄</span>
                </summary>
                <div class="absolute right-0 z-20 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                    @foreach([
                        'ganancias' => 'Reporte de ganancias',
                        'citas' => 'Reporte de citas',
                        'pacientes' => 'Reporte de pacientes',
                        'tratamientos' => 'Reporte de tratamientos',
                    ] as $exportKey => $exportLabel)
                        <a href="{{ route('reportes.export.excel', array_merge(request()->query(), ['reporte' => $exportKey])) }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]">{{ $exportLabel }} en Excel</a>
                    @endforeach
                </div>
            </details>
        </div>
    </div>

    @include('partials.flash-success')

    <form id="report-filter" action="{{ route('reportes') }}" method="GET" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[minmax(180px,0.8fr)_minmax(150px,0.7fr)_minmax(150px,0.7fr)_auto] lg:items-end">
            <label class="block">
                <span class="text-sm font-medium text-slate-600">Periodo</span>
                <select id="periodo" name="periodo" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-700 outline-none transition focus:border-[#9700cf] focus:ring-2 focus:ring-[#9700cf]/20">
                    @foreach($periodoOptions as $value => $label)
                        <option value="{{ $value }}" {{ $periodoActual === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600">Mes inicial</span>
                <input
                    id="desde"
                    name="desde"
                    type="month"
                    value="{{ request('desde') }}"
                    class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 outline-none transition focus:border-[#9700cf] focus:ring-2 focus:ring-[#9700cf]/20">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600">Mes final</span>
                <input
                    id="hasta"
                    name="hasta"
                    type="month"
                    value="{{ request('hasta') }}"
                    class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 outline-none transition focus:border-[#9700cf] focus:ring-2 focus:ring-[#9700cf]/20">
            </label>

            <div class="flex gap-3">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#9700cf] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#512569] lg:flex-none">
                    Aplicar
                </button>

                @if(count(request()->query()) > 0)
                    <a href="{{ route('reportes') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </div>

        <p class="mt-4 text-sm text-slate-500">
            Periodo actual: <span class="font-semibold text-slate-700">{{ $periodoLabel }}</span>
            @if($periodoActual === 'Personalizado' && preg_match('/^\d{4}-\d{2}$/', request('desde', '')) && preg_match('/^\d{4}-\d{2}$/', request('hasta', '')))
                <span> · {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', request('desde'))->locale('es')->translatedFormat('F Y') }} a {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', request('hasta'))->locale('es')->translatedFormat('F Y') }}</span>
            @endif
        </p>
    </form>

    @if(! $hasData)
        <div class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center shadow-sm">
            <p class="text-lg font-semibold text-slate-900">No hay datos suficientes para este periodo.</p>
            <p class="mt-2 text-sm text-slate-500">Ajusta el periodo o selecciona un rango personalizado valido.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($metricas as $metrica)
                @php
                    $key = $metrica['key'];
                    $trend = $trends[$key];
                    $isPositive = $trend['positive'];
                @endphp
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ $trend['label'] }}</p>
                            <p class="mt-3 text-3xl font-bold text-slate-950">{{ $reportes[$key]['display'] }}</p>
                        </div>
                        <span class="h-3 w-3 rounded-full border {{ $metrica['tone'] }}"></span>
                    </div>
                    <div class="mt-3 flex items-center gap-2 text-sm">
                        <span class="font-semibold {{ $isPositive ? 'text-[#8200b8]' : 'text-fuchsia-700' }}">{{ $isPositive ? '+' : '-' }}{{ $trend['percent'] }}%</span>
                        <span class="text-slate-500">vs periodo anterior</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $metrica['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <div>
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Tratamientos m&aacute;s realizados</h2>
                    <p class="text-sm text-slate-500">Cantidad de veces que se realiz&oacute; cada tratamiento durante el periodo seleccionado.</p>
                </div>

                <div class="space-y-5 p-5">
                    @forelse($treatmentCostBreakdown as $item)
                        <div class="grid items-center gap-3 sm:grid-cols-[minmax(130px,220px)_1fr_110px]">
                            <span class="truncate text-sm font-medium text-slate-600" title="{{ $item['tratamiento'] }}">{{ $item['tratamiento'] }}</span>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full" style="width: {{ max(($item['cantidad'] / $maxTreatmentCount) * 100, 5) }}%; background: linear-gradient(90deg, #7c25ff, #9700cf);"></div>
                            </div>
                            <span class="text-right text-sm font-semibold text-slate-800">{{ $item['cantidad'] }} {{ $item['cantidad'] === 1 ? 'realizado' : 'realizados' }}</span>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-400">No hay tratamientos realizados en este periodo.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.5fr_0.9fr]">
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Ganancias por mes</h2>
                    <p class="text-sm text-slate-500">Comparativo de los últimos meses disponibles.</p>
                </div>

                <div class="space-y-4 p-5">
                    @foreach($grafico as $punto)
                        <div class="grid grid-cols-[52px_1fr_96px] items-center gap-4">
                            <span class="text-sm font-medium text-slate-500">{{ $punto['mes'] }}</span>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-violet-600" style="width: {{ min(max(($punto['valor'] / $maxRevenue) * 100, 5), 100) }}%;"></div>
                            </div>
                            <span class="text-right text-sm font-semibold text-slate-800">${{ number_format($punto['valor'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Lectura rapida</h2>
                <div class="mt-4 space-y-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Ganancia promedio por cita</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">${{ number_format($quickReadings['averageEarningsPerAppointment'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Crecimiento mensual de citas</p>
                        <p class="mt-1 text-xl font-bold text-[#8200b8]">{{ $quickReadings['monthlyAppointmentGrowth'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Tratamientos activos</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ $quickReadings['activeTreatments'] }}</p>
                    </div>
                </div>
            </section>
        </div>
    @endif
</div>

<script>
    (() => {
        const form = document.getElementById('report-filter');
        const period = document.getElementById('periodo');
        const from = document.getElementById('desde');
        const to = document.getElementById('hasta');

        period.addEventListener('change', () => {
            if (period.value !== 'Personalizado') {
                from.value = '';
                to.value = '';
                form.requestSubmit();
            }
        });

        [from, to].forEach((input) => input.addEventListener('change', () => {
            if (!input.value) {
                return;
            }

            period.value = 'Personalizado';
            from.value ||= input.value;
            to.value ||= input.value;
            form.requestSubmit();
        }));
    })();
</script>

@endsection




