@extends('layouts.app')

@section('content')
    <div class="allmid text-center mt-10">
        <h1 class="mb-10">Horários de {{ $user->name }}</h1>

        <form method="GET" action="{{ route('admin.horarios.verificar', $user) }}">
            <div class="flex gap-4 w-full mb-5">
                <div class="">
                    <select name="mes"
                        class=" py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg w-full text-gray-700">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('mes', now()->month) == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="ano"
                        class=" py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg w-full text-gray-700">
                        @foreach (range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('ano', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="">
                    <button type="submit"
                        class=" py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg w-full text-gray-100">
                        Filtrar</button>

                </div>
                <div>
                    <a href="{{ route('admin.registro-ponto.download', $user) }}?mes={{ request('mes') }}&ano={{ request('ano') }}"
                        class=" py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg w-full text-gray-100">
                        <i class="ri-download-line"></i>
                    </a>
                </div>
            </div>
        </form>

        <table id="table-mes" class="table-auto  border border-gray-400 w-full">
            <thead>
                <tr>
                    <th class=" px-4 py-2 text-gray-100">Data</th>
                    <th class=" px-4 py-2 text-gray-100">Entrada</th>
                    <th class=" px-4 py-2 text-gray-100">Saída para Almoço</th>
                    <th class=" px-4 py-2 text-gray-100">Retorno do Almoço</th>
                    <th class=" px-4 py-2 text-gray-100">Saída Final</th>
                    <th class=" px-4 py-2 text-gray-100">Observação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $data => $registrosDia)
                    <tr>
                        <td class=" px-4 py-2">{{ $data }}</td>
                        <td class=" px-4 py-2">
                            {{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora ?? '-' }}
                        </td>
                        <td class=" px-4 py-2">
                            {{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora ?? '-' }}
                        </td>
                        <td class=" px-4 py-2">
                            {{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora ?? '-' }}
                        </td>
                        <td class=" px-4 py-2">
                            {{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('admin.registro-ponto.observacao', $data) }}"> @csrf
                                <textarea name="observacao" rows="2" class="w-full border-gray-300 rounded-lg">{{ $registrosDia->first()?->observacao ?? '' }}</textarea>
                                <button type="submit" class="mt-2 px-4 py-1 bg-gray-500 text-white rounded">Salvar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endsection
