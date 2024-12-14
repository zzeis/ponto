<table id="table-mes" >

    <thead>
        <th>Data</th>
        <th>Entrada</th>
        <th>Saida Almoço</th>
        <th>Retorno Almoço</th>
        <th>Saida final</th>
    </thead>
    @foreach ($registros as $data => $registrosDia)
        <tr>
            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</td>
            <td class="px-4 py-2 text-blue-600">
                {{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora ?? '-' }}
            </td>
            <td class="px-4 py-2 text-blue-600">
                {{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora ?? '-' }}
            </td>
            <td class="px-4 py-2 text-blue-600">
                {{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora ?? '-' }}
            </td>
            <td class="px-4 py-2 text-blue-600">
                {{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora ?? '-' }}
            </td>

        </tr>
    @endforeach
</table>
