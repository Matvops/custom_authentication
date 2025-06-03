<x-layouts.main_layout title="Profile">
    <x-slot:content>
         <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-6">
                        <p class="display-6">DEFINIR NOVA SENHA</p>

                        <form action="{{ route('password.update') }}" method="post">

                            @csrf
                            <div class="mb-3">
                                <label for="current_password" class="form-label">A sua senha atual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">Defina a nova senha</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                @error('new_password')
                                    <div class="text-danger">{{ $message }}</div>                                
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirmar a nova senha</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                @error('new_password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mt-4">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-secondary px-5">ALTERAR SENHA</button>
                                </div>
                            </div>

                        </form>

                        @if(session('server_error'))
                            <div class="alert alert-danger text-center mt-3">
                                {{ session('server_error') }}
                            </div>
                        @endif

                       @if(session('success'))
                            <div class="alert alert-success text-center mt-3">
                                {{ session('success') }}
                            </div>
                        @endif


                        <hr>

                        <div class="card border-1 border-danger p-5 text-center my-2">
                            Se pretender remover a sua conta de usuário de forma permanente, escreva o texto: "ELIMINAR"
                            e clique no botão abaixo.
                        

                            <form action="{{ route('profile.delete')}}" method="POST">
                                @method("DELETE")
                                @csrf

                                <div class="my-3">
                                    <input type="text" name="delete_confirmation" class="form-control text-center display-6">
                                    @error('delete_confirmation')
                                        <div class="alert alert-danger text-danger my-3">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-danger">ELIMINAR CONTA</button>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-layouts.main_layout>