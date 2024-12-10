@extends('layouts.app')

@section('content')
    <div class="allmid  mt-10">
        <div class="gray-bg p-10 text-center rounded">
            <h1 class="text-center mt-10 title">Registrar Horário</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div id="botoes-ponto">
                @php
                    $tiposRegistro = [
                        'entrada_manha' => 'Entrada Manhã',
                        'saida_almoco' => 'Saída Almoço',
                        'retorno_almoco' => 'Retorno Almoço',
                        'saida_fim' => 'Saída Fim',
                    ];
                @endphp

                @foreach ($tiposRegistro as $tipo => $label)
                    <form method="POST" action="{{ route('registro-ponto.registrar', $tipo) }}"
                        class="botao-registro   
                             @if ($tipo == 'entrada_manha' && !in_array('entrada_manha', $registrosHoje)) btn-ativo 
                             @elseif(
                                 ($tipo == 'saida_almoco' &&
                                     in_array('entrada_manha', $registrosHoje) &&
                                     !in_array('saida_almoco', $registrosHoje)) ||
                                     ($tipo == 'retorno_almoco' &&
                                         in_array('saida_almoco', $registrosHoje) &&
                                         !in_array('retorno_almoco', $registrosHoje)) ||
                                     ($tipo == 'saida_fim' && in_array('retorno_almoco', $registrosHoje) && !in_array('saida_fim', $registrosHoje))) btn-ativo 
                             @else hidden @endif"
                        data-tipo="{{ $tipo }}">
                        @csrf
                        <button type="submit"
                            class="btn-ponto text-center mt-4 mb-4  inline-flex items-center px-6 py-3 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-600">
                            <i class="ri-run-line"></i> {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>


            <style>
                .hidden1 {
                    display: none !important;
                }
            </style>


            <a href="{{ route('gerarpdf.mes') }}" class="botao text-center align-items-center "><i
                    class="ri-printer-line text-center"></i></a>





            <div class="mt-8">
                <h3 class="mb-4 text-gray-100">Registros do Mês</h3>
                <table id="table-mes" class="table-auto  border border-gray-400 w-full">
                    <thead>
                        <tr>
                            <th class=" px-4 py-2 text-gray-100">Data</th>
                            <th class=" px-4 py-2 text-gray-100">Entrada</th>
                            <th class=" px-4 py-2 text-gray-100">Saída para Almoço</th>
                            <th class=" px-4 py-2 text-gray-100">Retorno do Almoço</th>
                            <th class=" px-4 py-2 text-gray-100">Saída Final</th>
                            <th class=" px-4 py-2 text-gray-100">Assinatura</th>
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
                                <td class=" px-4 py-2">

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>



        </div>
    </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            const tiposRegistro = @json(array_keys($tiposRegistro));

            $('.botao-registro form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        // Esconde botão atual
                        form.closest('.botao-registro').addClass('hidden1');

                        // Identifica próximo botão na sequência
                        var tipoAtual = form.data('tipo');
                        var indiceAtual = tiposRegistro.indexOf(tipoAtual);

                        if (indiceAtual < tiposRegistro.length - 1) {
                            var proximoTipo = tiposRegistro[indiceAtual + 1];
                            $(`.botao-registro[data-tipo="${proximoTipo}"]`).removeClass(
                                'hidden');
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || 'Erro ao registrar ponto');
                    }
                });
            });
        });
    </script>
@endpush
