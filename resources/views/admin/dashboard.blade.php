@extends('layouts.app')

@section('content')
    <div class="allmid mt-10">

        <div class="flex justify-between gap-10">
            <a href="{{ route('admin.listaEstagiarios') }}" class="title text-center p-10">
                <i class="ri-time-line"></i>
                <br>
                Gerenciamento

            </a>
            <a href="{{ route('admin.usuarios.criar') }}" class="title text-center p-10">
                <i class="ri-user-add-line"></i>
                <br>
                Criar Usuario
            </a>
        </div>
    </div>
@endsection
