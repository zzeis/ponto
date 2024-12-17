@extends('layouts.app')

@php
    $tiposRegistro = [
        'entrada_manha' => 'Entrada Manhã',
        'saida_almoco' => 'Saída Almoço',
        'retorno_almoco' => 'Retorno Almoço',
        'saida_fim' => 'Saída Fim',
    ];
@endphp



@section('content')
    <div class="allmid mt-10">
        <div class="p-10 bg-white dark:bg-gray-800 text-center rounded">
            <h1 class="text-center mt-10 title text-gray-600 dark:text-gray-400">
                Registrar Horário
            </h1>

            @if (session('success'))
                <div class="relative flex flex-col w-full p-3 text-sm text-gray-600 dark:text-gray-400 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 relative flex w-full p-3 text-center text-sm text-white bg-red-600 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div id="botoes-ponto">
                @foreach ($tiposRegistro as $tipo => $label)
                    <div class="botao-registro
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
                        <form method="POST" action="{{ route('registro-ponto.registrar', $tipo) }}"
                            data-tipo="{{ $tipo }}" onsubmit="return false;" id="form-{{ $tipo }}">
                            @csrf
                            <button type="button"
                                class="btn-ponto text-center mt-4 mb-4 inline-flex items-center px-6 py-3 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-600">
                                <i class="ri-run-line"></i> {{ $label }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('gerarpdf.mes') }}"
                class="botao text-gray-600 dark:text-gray-400 text-center align-items-center">
                <i class="ri-printer-line text-center text-gray-600 dark:text-gray-400"></i>
            </a>

            <div class="mt-8">
                <h3 class="mb-4 text-gray-600 dark:text-gray-400">Registros do Mês</h3>
                <x-registros-tabela :registros="$registros" />
            </div>
        </div>
    </div>

    <style>
        .hidden1 {
            display: none !important;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tabelaElement = document.getElementById('table-mes');

            const registroForms = document.querySelectorAll('.botao-registro form');

            registroForms.forEach(form => {
                // Altere para selecionar o botão
                const botaoSubmit = form.querySelector('button[type="button"]');

                botaoSubmit.addEventListener('click', function(e) {
                    e.preventDefault();

                    const formElement = this.closest('form');
                    const tipo = formElement.getAttribute('data-tipo');
                    const url = formElement.getAttribute('action');
                    const botaoRegistro = formElement.closest('.botao-registro');
                    const textoOriginal = this.innerHTML; // Salva o conteúdo original do botão

                    // Desabilitar botão
                    this.disabled = true;
                    this.innerHTML = `
<i class="ri-loader-4-line animate-spin inline-block mr-2"></i>
Processando...
`;
                    axios.post(url, {}, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registro Enviado',
                                text: 'Seu ponto está sendo processado',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Esconder botão atual e mostrar próximo
                            botaoRegistro.classList.add('hidden1');

                            const proximoTipo = response.data.proximoTipo;
                            if (proximoTipo) {
                                const proximoBotao = document.querySelector(
                                    `.botao-registro[data-tipo="${proximoTipo}"]`);
                                if (proximoBotao) {
                                    proximoBotao.classList.remove('hidden');
                                }
                            }

                            // Atualizar registros
                            atualizarRegistrosPonto(tipo);
                        })
                        .catch(error => {
                            // Restaurar botão
                            console.error('Erro detalhado:', error);
                            this.disabled = false;
                            this.innerHTML =
                                `<i class="ri-run-line"></i> ${this.textContent.replace('Processando...', '')}`;
                            this.innerHTML =
                                textoOriginal; // Restaura o conteúdo original do botão

                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: error.response?.data?.error ||
                                    'Erro ao registrar ponto'
                            });
                        });
                });
            });

            function atualizarRegistrosPonto(tipo) {
                // Adicionar estado de carregamento
                const tabelaElement = document.getElementById('table-mes');
                const loadingHTML = `
        
                <div class="flex flex-col  w-full justify-items-center items-center text-center margin-auto">
                <div class="flex justify-items-center items-center text-center margin-auto">
                    <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg><br>
                    Carregando registros...
                </div>
                </div>
         
    `;

                tabelaElement.innerHTML = loadingHTML;


                // Polling para verificar o registro
                const interval = setInterval(() => {
                    axios.get("{{ route('registros.atualizar') }}", {
                            params: {
                                tipo: tipo
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                // Verifique se o registro do tipo atual foi encontrado
                                if (response.data.registroEncontrado) {
                                    clearInterval(interval); // Pare o polling
                                    tabelaElement.innerHTML = response.data.html;
                                }
                            } else {
                                console.error('Não foi possível carregar os registros');
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao atualizar registros', error);
                            tabelaElement.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-red-500">
                            Erro ao carregar registros
                        </td>
                    </tr>
                `;
                            clearInterval(interval); // Pare o polling em caso de erro
                        });
                }, 2000); // Tente a cada 2 segundos
            }
        });
    </script>
@endsection
