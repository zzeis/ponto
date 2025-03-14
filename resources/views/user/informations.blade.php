@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <form id="createUserForm" action="{{ route('admin.usuarios.update', $user->id) }}" method="POST"
            class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
            @csrf
            @method('PUT')
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

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-600 dark:text-gray-400 text-sm font-bold mb-2" for="name">
                        Nome Completo
                    </label>
                    <input type="text" name="name"
                        class=" text-gray-600 dark:text-gray-400 shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                        value="{{ $user->name }}" required>
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
                        value="{{ $user->cpf }}" required>
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
                        value="{{ $user->employee_code }}" required>
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
                        required value="{{ $user->email }}">
                    @error('email')
                        <p class="texvalidated['password']t-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departamento (opcional) -->
                <div class="">
                    <label class="text-gray-600 dark:text-gray-400 text-sm font-bold mb-2">Secretaria</label>
                    <select id="departamento_id" name="departamento_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">

                        <option value="{{ $user->departamento_id }}" class="option">{{ $departamento->sigla }}
                            {{ $departamento->nome }} </option>
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
                        value="{{ $user->local }}" required>
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
                        <select id="nivel_acesso" name="nivel_acesso"
                            class="shadow text-gray-600 dark:text-gray-400 appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror">
                            <option value="{{$user->nivel_acesso}}" {{ old('role') === $user->nivel_acesso ? 'selected' : '' }}>{{$user->nivel_acesso}}
                            </option>
                            <option value="estagiario" {{ old('role') === 'estagiario' ? 'selected' : '' }}>Estagiário
                            </option>
                            <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor
                            </option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
                <div id="horarios" class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="entrada_manha" value="Entrada Manhã" />
                        <x-text-input
                            value="{{ optional($horarios)->entrada_manha ? \Carbon\Carbon::parse($horarios->entrada_manha)->format('H:i') : '' }}"
                            id="entrada_manha" name="entrada_manha" type="time" />
                    </div>



                    <div>
                        <x-input-label for="saida_manha" value="Saída Almoço" />
                        <x-text-input
                            value="{{ optional($horarios)->saida_manha ? \Carbon\Carbon::parse($horarios->saida_manha)->format('H:i') : '' }}"
                            id="saida_manha" name="saida_manha" type="time" />
                    </div>

                    <div>
                        <x-input-label for="entrada_tarde" value="Retorno Almoço" />
                        <x-text-input
                            value="{{ optional($horarios)->entrada_tarde ? \Carbon\Carbon::parse($horarios->entrada_tarde)->format('H:i') : '' }}"
                            id="entrada_tarde" name="entrada_tarde" type="time" />
                    </div>

                    <div>
                        <x-input-label for="saida_tarde" value="Saída" />
                        <x-text-input
                            value="{{ optional($horarios)->saida_tarde ? \Carbon\Carbon::parse($horarios->saida_tarde)->format('H:i') : '' }}"
                            id="saida_tarde" name="saida_tarde" type="time" />
                    </div>
                </div>


            </div>


            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('nivel_acesso').addEventListener('change', function() {
            var horariosDiv = document.getElementById('horarios');
            if (this.value === 'estagiario') {
                horariosDiv.classList.add('flex', 'grid', 'grid-cols-2', 'gap-4');
                horariosDiv.style.display = 'grid';


            } else {
                horariosDiv.style.display = 'none';
            }
        })

        window.onload = function() {
            var nivelAcesso = document.getElementById('nivel_acesso').value;
            var horariosDiv = document.getElementById('horarios');
            if (nivelAcesso === 'estagiario') {
                horariosDiv.style.display = 'grid';
                horariosDiv.classList.add('grid', 'grid-cols-2', 'gap-4');
            } else {
                horariosDiv.style.display = 'none';
                horariosDiv.classList.remove('grid', 'grid-cols-2', 'gap-4');
            }
        }
    </script>
@endsection
