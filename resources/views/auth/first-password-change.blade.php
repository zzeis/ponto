@extends('layouts.app')

@section('content')
    <div class=" allmid ">
        <div class="row  mt-10 justify-content-center  rounded gray-bg color-text color-text p-10">
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header mb-4">Digita a sua nova senha de acesso</div>

                    <form method="POST" action="{{ route('first.password.update') }}">
                        @csrf

                        <div class="form-group row ">
                            <label for="password" class="col-md-4 col-form-label text-md-right ">Nova
                                Senha</label>

                            <div class="col-md-6 ">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class=" col-md-4 col-form-label text-md-right">Confirme a Nova
                                Senha</label>

                            <div class="col-md-6 mb-4">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mt-2 mb-0 mt-2">
                            <div class="col-md-6 offset-md-4 text-center ">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 hover:text-gray-600">
                                    Alterar Senha
                                </button>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
