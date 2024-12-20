@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <form id="createUserForm" action="{{ route('admin.usuarios.save') }}" method="POST"
            class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
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
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="name">
                        Nome Completo
                    </label>
                    <input type="text" name="name"
                        class=" text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="name">
                        CPF
                    </label>
                    <input type="number" name="cpf" maxlength="11"
                        class="text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                        value="{{ old('cpf') }}" required>
                    @error('cpf')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="name">
                        Matricula
                    </label>
                    <input type="number" name="employee_code"
                        class="text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                        value="{{ old('employee_code') }}" required>
                    @error('')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="email">
                        E-mail
                    </label>
                    <input type="email" name="email"
                        class=" text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                        required value="{{ old('email') }}">
                    @error('email')
                        <p class="texvalidated['password']t-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departamento (opcional) -->
                <div class="">
                    <label class="text-gray-600 dark:text-gray-400 text-sm font-bold mb-2">Secretaria</label>
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

                <div>
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="name">
                        Local
                    </label>
                    <input type="text" name="local"
                        class=" text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                        value="{{ old('local') }}" required>
                    @error('local')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Nível de Acesso -->
                @if (auth()->user()->nivel_acesso === 'admin')
                    <!-- Só exibe para administradores -->
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="role">
                            Nível de Acesso
                        </label>
                        <select name="nivel_acesso"
                            class="shadow text-gray-600 dark:text-gray-400 appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror">
                            <option value="estagiario" {{ old('role') === 'estagiario' ? 'selected' : '' }}>Estagiário
                            </option>
                            <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor
                            </option>
                
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                @endif


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
