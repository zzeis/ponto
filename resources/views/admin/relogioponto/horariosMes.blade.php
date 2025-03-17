@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6 rounded-lg shadow-sm bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-bold mb-8 text-gray-800 dark:text-gray-200">Registros de {{ $user->name }}</h1>

        <!-- Overtime Summary Section -->
        @if (isset($horasExtrasTotal))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Relatório de Horas
                        </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-gray-600 dark:text-gray-300">Total de dias com horas extras:</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $diasComHorasExtras }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-600">
                            <span class="text-gray-600 dark:text-gray-300">Total de horas extras:</span>
                            <span
                                class="font-medium text-gray-800 dark:text-gray-200">{{ number_format($horasExtrasTotal, 2) }}
                                horas</span>
                        </div>
                        @if ($diasExtras > 0)
                            <div
                                class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="text-gray-600 dark:text-gray-300">Equivalente a:</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $diasExtras }} dia(s) e
                                    {{ number_format($horasExtrasRestantes, 2) }} horas</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($detalhamentoHorasExtras) > 0)
                    <div
                        class="bg-white dark:bg-gray-700 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Detalhamento de Horas Extras
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-600 text-left">
                                        <th
                                            class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-200 rounded-tl-lg">
                                            Data</th>
                                        <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-200">Horas
                                            Trabalhadas</th>
                                        <th
                                            class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-200 rounded-tr-lg">
                                            Horas Extras</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detalhamentoHorasExtras as $detalhe)
                                        <tr
                                            class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $detalhe['data'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $detalhe['horasTrabalhadas'] }}</td>
                                            <td
                                                class="px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                {{ $detalhe['horasExtras'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <x-sweetalert />

        <!-- Filter Controls -->
        <form method="GET" id="filtrar" action="{{ route('horarios.verificar', $user) }}" class="mb-6">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="w-full sm:w-auto flex-1 sm:flex-none">
                    <label for="mes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data
                        Inicial</label>
                    <input id="mes" type="date" value="{{ request('data_inicial') }}" name="data_inicial"
                        class="w-full py-2.5 px-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />



                </div>
                <div class="">
                    <label for="mes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data
                        Final</label>
                    <input id="mes" type="date" value="{{ request('data_final') }}" name="data_final"
                        class="w-full py-2.5 px-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />

                </div>

                <div class="flex items-end gap-2 mt-auto">

                    <button form="filtrar"
                        class="flex items-center justify-center h-11 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">

                        <i class="ri-filter-3-line mr-2"></i>
                        <span> Filtrar</span>
                    </button>

                    <a href="{{ route('registro-ponto.download', $user) }}?data_inicial={{ request('data_inicial') }}&data_final={{ request('data_final') }}"
                        class="flex items-center justify-center h-11 px-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                        <i class="ri-download-line mr-2"></i>
                        <span>Download</span>
                    </a>
                    <button type="submit" form="registros"
                        class="flex items-center justify-center h-11 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="ri-save-3-fill mr-2"></i>
                        <span>Salvar</span>
                    </button>
                </div>

            </div>
        </form>

        <!-- Time Entry Table -->
        <form id="registros" method="POST" action="{{ route('registro-ponto.update-batch', $user) }}">
            @csrf
            @method('PUT')
            <div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Data</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Entrada</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Saída para
                                Almoço</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Retorno do
                                Almoço</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Saída Final
                            </th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">Observação
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $data => $registrosDia)
                            <tr
                                class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][entrada_manha]"
                                        value="{{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora }}"
                                        class="w-full py-2 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][saida_almoco]"
                                        value="{{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora }}"
                                        class="w-full py-2 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][retorno_almoco]"
                                        value="{{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora }}"
                                        class="w-full py-2 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][saida_fim]"
                                        value="{{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora }}"
                                        class="w-full py-2 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <textarea name="registros[{{ $data }}][observacao]" rows="2"
                                        class="w-full py-2 px-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ $registrosDia->first()?->observacao ?? '' }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
@endsection
