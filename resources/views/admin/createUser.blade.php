@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <form id="createUserForm" action="{{ route('admin.usuarios.save') }}" method="POST"
            class="max-w-2xl mx-auto gray-bg p-8 rounded-lg shadow-md">
            <div>
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <strong>Erro!</strong> Por favor, Revise os erros abaixo.
                        <ul class="mt-2">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block color-text text-sm font-bold mb-2" for="name">
                        Nome Completo
                    </label>
                    <input type="text" name="name"
                        class="shadow appearance-none border rounded w-full py-2 px-3 color-text leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block color-text text-sm font-bold mb-2" for="name">
                        CPF
                    </label>
                    <input type="number" name="cpf"
                    maxlength="11"
                        class="shadow appearance-none border rounded w-full py-2 px-3 color-text leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                        value="{{ old('cpf') }}" required>
                    @error('cpf')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block color-text text-sm font-bold mb-2" for="email">
                        E-mail
                    </label>
                    <input type="email" name="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 color-text leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                        required value="{{ old('email') }}">
                    @error('email')
                        <p class="texvalidated['password']t-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departamento (opcional) -->
                <div class="">
                    <label class="color-text">Secretaria</label>
                        <select id="departamento_id" name="departamento_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">

                        @foreach (\App\Models\Departamento::all() as $departamento)
                            <option class="option" value="{{ $departamento->id }}">
                                ({{ $departamento->sigla }})
                                {{ $departamento->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>


            </div>


            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Criar Usuário
                </button>
            </div>
        </form>
    </div>
@endsection
