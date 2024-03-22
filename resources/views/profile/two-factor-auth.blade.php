@extends('profile.layout')

@section('main')
    <h2>Active Two-Factor Auth:</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.manage-two-factor-auth') }}" method="post" id="twoFactorAuthForm">
        @csrf
        <div class="form-group">
            <label for="type">Type</label>
            <select class="form-select" name="type" id="type">
                @foreach(config('twoFactorAuth.types') as $key => $value)
                    <option value="{{ $key }}" {{ old('type') == $key || auth()->user()->isTwoFactorAuth($key) ? 'selected' : ''}}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <br>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') ?? auth()->user()->phone_number }}" placeholder="09*********" class="form-control">
        </div>
        <br>
        <div class="form-group">
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
