@extends('layout')
@section('title','Ustawienia')
@section('content')
<div style="max-width:640px">
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
    <div class="ct">Wygląd i układ</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="fg">
        <label>Kolor akcentu</label>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="color" name="accent_color" value="{{ $settings['accent_color'] ?? '#63b3ed' }}" id="accentPicker" style="width:44px;height:36px;padding:2px;cursor:pointer;border-radius:4px">
          <input type="text" id="accentHex" value="{{ $settings['accent_color'] ?? '#63b3ed' }}" style="width:90px;font-size:.78rem" placeholder="#63b3ed">
        </div>
      </div>
      <div class="fg">
        <label>Rozmiar czcionki (px)</label>
        <input type="number" name="font_size" value="{{ $settings['font_size'] ?? '14' }}" min="11" max="20" style="width:80px">
      </div>
      <div class="fg">
        <label>Szerokość sidebaru (px)</label>
        <input type="number" name="sidebar_width" value="{{ $settings['sidebar_width'] ?? '220' }}" min="160" max="340" style="width:80px">
      </div>
      <div class="fg">
        <label>Zaokrąglenie kart (px)</label>
        <input type="number" name="border_radius" value="{{ $settings['border_radius'] ?? '10' }}" min="0" max="24" style="width:80px">
      </div>
    </div>
    <div style="margin-top:8px">
      <label style="font-size:.72rem;color:var(--mu);display:block;margin-bottom:6px">Podgląd koloru akcentu:</label>
      <div id="accentPreview" style="display:flex;gap:8px;align-items:center">
        <div id="pDot" style="width:24px;height:24px;border-radius:50%;background:{{ $settings['accent_color'] ?? '#63b3ed' }}"></div>
        <span style="font-size:.8rem;color:var(--mu)">Kolor używany w wykresach, przyciskach i aktywnych linkach</span>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="ct">Integracja AI</div>
    <p style="font-size:.78rem;color:var(--mu);margin-bottom:10px">Klucz API Claude (Anthropic) do automatycznego parsowania wgranych dokumentów PDF.</p>
    <div class="fg"><label>Claude API Key</label><input type="password" name="ai_api_key" value="{{ $settings['ai_api_key'] ?? '' }}" placeholder="sk-ant-..."></div>
  </div>
  <div class="card">
    <div class="ct">Prywatne repozytorium (Credentials)</div>
    <p style="font-size:.78rem;color:var(--mu);margin-bottom:10px">Token GitHub do pobierania pliku <code>credentials.json</code> z prywatnego repo <code>porczynski/dmstic</code>. Dane logowania do dostawców (TAURON, ORLEN, itd.) są przechowywane w tym repo.</p>
    <div class="fg"><label>GitHub Token (repo:read)</label><input type="password" name="private_repo_token" value="{{ $settings['private_repo_token'] ?? '' }}" placeholder="ghp_..."></div>
    <p style="font-size:.73rem;color:var(--mu)">Repo: <code>https://github.com/porczynski/dmstic</code> · Plik: <code>credentials.json</code></p>
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
const picker=document.getElementById('accentPicker');
const hex=document.getElementById('accentHex');
const dot=document.getElementById('pDot');
picker.addEventListener('input',function(){hex.value=this.value;dot.style.background=this.value;document.documentElement.style.setProperty('--ac',this.value);});
hex.addEventListener('input',function(){if(/^#[0-9a-f]{6}$/i.test(this.value)){picker.value=this.value;dot.style.background=this.value;document.documentElement.style.setProperty('--ac',this.value);}});
</script>
@endsection
