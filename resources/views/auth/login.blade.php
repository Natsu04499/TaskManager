
@if(session('status'))
    {{ session('status') }}
@endif

<form method="POST" action="/login">
    @csrf
    Email: <input type="text" name="email"><br>
    Mot de Passe: <input type="password" name="password"><br>
    <button type="submit">Se connecter</button>
    <a href="/forgot-password">Mot de passe oublié ?</a>
</form>

<hr>

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
@endif

@include('components.footer')