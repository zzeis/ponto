@extends('layouts.app')

@section('content')
    <div class="allmid mt-10">

        <div class="flex bg-white  dark:bg-gray-800 rounded justify-between  text-gray-500 dark:text-gray-400">
          
                <a href="{{ route('admin.listaEstagiarios') }}"
                    class="title hover:bg-gray-200 text-center p-10 text-gray-500 dark:text-gray-400">
                    <i class="ri-time-line"></i>
                    <br>
                    Gerenciamento
                </a>
           
            <a href="{{ route('admin.usuarios.criar') }}" 
            class="title  hover:bg-gray-200  text-center p-10 text-gray-500 dark:text-gray-400">
                <i class="ri-user-add-line"></i>
                <br>
                Criar Usuario
            </a>
        </div>
    </div>
@endsection
