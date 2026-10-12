@if(session('success'))
  <div class="admin-feedback admin-feedback--success" role="status">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="admin-feedback admin-feedback--error" role="alert">{{ session('error') }}</div>
@endif

@if($errors->any())
  <div class="admin-feedback admin-feedback--error" role="alert">
    <p>Vérifiez les informations saisies :</p>
    <ul>
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
