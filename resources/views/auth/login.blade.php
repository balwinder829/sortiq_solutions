<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">

<div class="col-md-6">
    <div class="authincation-content shadow-lg p-4 rounded bg-white">
        <h4 class="text-center mb-4">Sign in to your account</h4>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label><strong>Username</strong></label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            </div>

            <div class="form-group mb-3">
                <label><strong>Password</strong></label>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>

            <div class="form-group  mb-3">
                <label><strong>Select Session</strong></label>
                <select name="session_id" class="form-control">
                    <option value="">-- Select Session --</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}">
                            {{ ucwords($session->display_name) }}
                        </option>
                    @endforeach
                </select>
                @error('batch_mode')
                    <small class="text-danger">Please choose session</small>
                @enderror
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-block" style="background-color: #6b51df; color: #fff;">Login</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>
