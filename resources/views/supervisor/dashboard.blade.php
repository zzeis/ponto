@extends('layouts.app')

@section('content')
    <div class="allmid mt-10">

        <div class="flex bg-white  dark:bg-gray-800 rounded justify-between  text-gray-500 dark:text-gray-400">

            <a href="{{ route('listaEstagiarios') }}"
                class="title hover:bg-gray-200 text-center p-10 text-gray-500 dark:text-gray-400">
                <i class="ri-time-line"></i>
                <br>
                Gerenciamento
            </a>

         
        </div>
    </div>
@endsection
