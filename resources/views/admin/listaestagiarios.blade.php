@extends('layouts.app')

@section('content')
    <div class="allmid text-center h-full ">
        <div class=" bg-white dark:bg-gray-800 h-screen  text-center w-full flex flex-col items-center p-10 color-text">
            <form method="GET" action="{{ route('listaEstagiarios') }}">
                <input type="text" class=" text-gray-500 dark:text-gray-400 input-search rounded" name="search" placeholder="Buscar estagiário">
                <button type="submit" class="pl-2 text-xl text-gray-500 dark:text-gray-400"><i
                        class="ri-menu-search-line"></i></button>
            </form>

            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow sm:rounded-lg bg-white dark:bg-gray-800">
                        <table class="min-w-full border-collapse border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">Nome</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">Status</th>
                                    <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estagiarios as $estagiario)
                                    <tr
                                        class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                                            {{ $estagiario->name }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                                            {!! $estagiario->is_active ? '<i class="ri-check-line"></i>' : '<i class="ri-lock-line"></i>' !!}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 flex gap-2">
                                            @if (auth()->user()->nivel_acesso === 'admin')
                                            <!-- Só exibe para administradores -->
                                            <form  action="{{ route('admin.usuarios.status', $estagiario) }}" method="POST">
                                                @method('PUT')
                                                @csrf
                                                <button type="submit"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded">
                                                    {!! $estagiario->is_active ? '<i class="ri-lock-line"></i>' : '<i class="ri-check-fill"></i>' !!}
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.user.informations', $estagiario) }}"
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">
                                            <i class="ri-user-settings-line"></i>
                                            @endif

                                            <a href="{{ route('horarios.verificar', $estagiario) }}"
                                                class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">
                                                <i class="ri-file-edit-line"></i>
                                            </a>

                                            
                                        </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Links de paginação -->
            <div class=" flex justify-content-center mt-4 text-center">
                {{ $estagiarios->links() }}
            </div>
        </div>
    </div>
@endsection
