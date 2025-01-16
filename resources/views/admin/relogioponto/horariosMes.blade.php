@extends('layouts.app')

@section('content')

    <div class=" mx-auto rounded p-5  text-center bg-white dark:bg-gray-900">
        <h1 class="mb-10 mt-10 title text-gray-500 dark:text-gray-400">Registros de {{ $user->name }}</h1>

        <x-sweetalert />
        <form method="GET" action="{{ route('horarios.verificar', $user) }}">
            <div class="flex gap-4 text-center justify-center w-full mb-5">
                <div>
                    <select name="mes" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-gray-700"
                        onchange="this.form.submit()">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('mes', now()->month) == $m ? 'selected' : '' }}>
                                {{ now()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="ano" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-gray-700"
                        onchange="this.form.submit()">
                        @foreach (range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('ano', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <a href="{{ route('registro-ponto.download', $user) }}?mes={{ request('mes') }}&ano={{ request('ano') }}"
                        class="py-3 px-4 block border-gray-200 rounded-lg text-gray-600 dark:text-gray-400">
                        <i class="ri-download-line text-gray-500 dark:text-gray-400"></i>
                    </a>
                </div>
                <div class="text-center">
                    <button type="submit" form="registros"
                        class="py-3 px-4 text-center  block w-full text-gray-600 dark:text-gray-200  rounded-lg text-gray-700">
                        <i class="ri-save-3-fill text-xl"></i>
                    </button>
                </div>
            </div>
        </form>


        <form id="registros" method="POST" action="{{ route('registro-ponto.update-batch', $user) }}">
            @csrf
            @method('PUT')
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table  class="table-auto w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-1/6">Data</th>
                            <th scope="col" class="px-6 py-4 w-1/6">Entrada</th>
                            <th scope="col" class="px-6 py-4 w-1/6">Saída para Almoço</th>
                            <th scope="col" class="px-6 py-4 w-1/6">Retorno do Almoço</th>
                            <th scope="col" class="px-6 py-4 w-1/6">Saída Final</th>
                            <th scope="col" class="px-6 py-4 w-1/6">Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $data => $registrosDia)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][entrada_manha]"
                                        value="{{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora }}"
                                        class="w-full border-gray-300 rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][saida_almoco]"
                                        value="{{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora }}"
                                        class="w-full border-gray-300 rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][retorno_almoco]"
                                        value="{{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora }}"
                                        class="w-full border-gray-300 rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="time" name="registros[{{ $data }}][saida_fim]"
                                        value="{{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora }}"
                                        class="w-full border-gray-300 rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <textarea name="registros[{{ $data }}][observacao]" rows="2" class="w-full border-gray-300 rounded-lg">{{ $registrosDia->first()?->observacao ?? '' }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </form>
    </div>
@endsection
