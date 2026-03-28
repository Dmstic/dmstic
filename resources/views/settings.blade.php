@extends('layout')
@section('title','Ustawienia')
@section('content')
<div style="max-width:580px">
<form method="POST" action="/settings">
  @csrf
  <div class="card">
    <div class="ct">Ustawienia globalne</div>
    <div class="fg"><label>Nazwa aplikacji</label><input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'Dmstic' }}"></div>
    <div class="fg">
      <label>Schemat kolorów</label>
      <select name="color_scheme" id="themesel">
        <option value="dark" {{ ($settings['color_scheme']??'dark')==='dark'?'selected':'' }}>Ciemny</option>
        <option value="light" {{ ($settings['color_scheme']??'dark')==='light'?'selected':'' }}>Jasny</option>
      </select>
    </div>
  </div>
  <div class="card">
    <div class="ct">Integracja AI</div>
    <p style="font-size:.78rem;color:var(--mu);margin-bottom:10px">Klucz API Claude (Anthropic) do automatycznego parsowania wgranych dokumentów PDF.</p>
    <div class="fg"><label>Claude API Key</label><input type="password" name="ai_api_key" value="{{ $settings['ai_api_key'] ?? '' }}" placeholder="sk-ant-..."></div>
  </div>
  <div class="card">
    <div class="ct">Przechowywanie dokumentów</div>
    <p style="font-size:.78rem;color:var(--mu)">Wgrane pliki zapisywane na serwerze:</p>
    <code style="display:block;background:var(--sf2);padding:7px 10px;border-radius:6px;margin:8px 0;font-size:.74rem">/data/home/dmstic/app/storage/app/public/documents/{provider_id}/</code>
    <p style="font-size:.75rem;color:var(--mu)">URL: <code>https://dmstic.netol.com/storage/documents/{id}/plik.pdf</code></p>
  </div>
  <button type="submit" class="btn bp">Zapisz ustawienia</button>
</form>
</div>
<script>
document.getElementById('themesel').addEventListener('change',function(){
  document.documentElement.dataset.theme=this.value;
  localStorage.setItem('theme',this.value);
});
</script>
@endsection
