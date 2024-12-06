<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Pontos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .allmid {
            text-align: center;
            margin: auto;
        }

        img {
            float: left;
        }

        table {
            font-size: 1rem
        }
        table #visto{
            width: 150px
        }
    </style>
</head>

<body>
    <div>
        <img src="data:image/png;base64,{{ $logoBase64 ?? 'logo' }}" width="100px" height="100px" alt="Logo">
    </div>
    <div class="allmid">

        <p>PREFEITURA MUNICIPAL DE IGUAPE</p>

        <span style="font-size: 12px">-Estância Balneária</span>
    </div>

    <h1 style="text-align: center;">Atestado de Frequência<h1>



            <p>Servidor: {{ $user->name ?? 'Nome não disponível' }} </p>



            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Entrada</th>
                        <th>Saída Almoço</th>
                        <th>Retorno Almoço</th>
                        <th>Saída</th>
                        <th id="visto">Visto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registros as $data => $registrosDia)
                        <tr>
                            <td>{{ $data }}</td>
                            <td>{{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora ?? '-' }}</td>
                            <td>{{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora ?? '-' }}</td>
                            <td>{{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora ?? '-' }}</td>
                            <td>{{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora ?? '-' }}</td>
                            <td > </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


</body>

</html>
