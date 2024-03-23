@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Enter Your Token
                    </div>
                    <div class="card-body">
                        <form action="{{ route('auth.token.post-token') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="token">Token</label>
                                <input type="text" name="token" class="form-control @error('token') is-invalid @enderror" placeholder="Enter Code">
                                {{-- this part show a simple message about token required, for more detail add bootstrap class--}}
                                @error('token')
                                <sp class="invalid-feedback">{{ $message }}</sp>
                                @enderror
                            </div>
                            <br>
                            <div class="form-group">
                                <button class="btn btn-primary">
                                    Verify Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
