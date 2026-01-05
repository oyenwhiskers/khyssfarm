<x-guest-layout>
    <div class="auth-header">
        <div class="logo"><i class="fas fa-envelope-open-text"></i></div>
        <h1>Forgot Password</h1>
        <p>Enter your email and we will send you a reset link.</p>
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Email Password Reset Link
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-muted">Back to login</a>
            </div>
        </form>
    </div>
</x-guest-layout>
