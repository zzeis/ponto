@extends('layouts.app')

@section('content')
    <div class="allmid mt-10 text-center ">
        <div class="gray-bg  text-center w-full flex flex-col items-center p-10 color-text">
            <form method="GET" action="{{ route('admin.listaEstagiarios') }}">
                <input type="text" class="input-search rounded" name="search" placeholder="Buscar estagiário">
                <button type="submit" class="pl-2 text-xl"><i class="ri-menu-search-line"></i></button>
            </form>

            @foreach ($estagiarios as $estagiario)
                <div class="flex gap-2 mt-5">
                    <p class="bg-gray-200 p-2 rounded text-gray-800"> {{ $estagiario->name }}</p>
                    <form action="" method="POST">
                        @method('PUT')
                        <button type="submit" class="bg-gray-500 p-2 rounded">
                            {{ $estagiario->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
                    <a class="bg-gray-600 p-2 rounded" href="{{ route('admin.horarios.verificar', $estagiario) }}">
                        Verificar Horários
                    </a>
                </div>
            @endforeach
            <!-- Links de paginação -->
            <div class="d-flex justify-content-center">
                {{ $estagiarios->links() }}
            </div>
        </div>
    </div>
@endsection
