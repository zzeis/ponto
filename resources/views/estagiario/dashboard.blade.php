@extends('layouts.app')

@section('content')
    <div class="allmid w-full p-10">
        <a href="{{ route('registro-ponto.index') }}" class="title text-center ">
            <div class="bg-white dark:bg-gray-800 h-full hover:bg-gray-200  w-50 text-center p-10 roudend">
                Relogio Ponto <br> <i class="ri-time-line text-center "></i>

            </div>
        </a>
    </div>
@endsection
