@extends('layout')
@section('title','Edytuj dostawcę')
@section('content')
<div style="max-width:520px">
  @if(session('success'))<div class="alert alert-success mb-3" style="padding:10px 14px;background:var(--sg);color:#fff;border-radius:var(--br)">{{ session('success') }}</div>@endif
  <div class="card mb-3">
    <div class="ct">Edytuj: {{ $provider->name }}</div>
    <form method="POST" action="/provider/{{ $provider->id }}/edit">
      @csrf
      <div class="fg"><label>Nazwa</label><input type="text" name="name" value="{{ $provider->name }}" required></div>
      <div class="fg"><label>Typ</label>
        <select name="type">
          @foreach(['electricity'=>'Energia elektryczna','gas'=>'Gaz','water'=>'Woda','internet'=>'Internet','multimedia'=>'Multimedia','other'=>'Inne'] as $val=>$label)
            <option value="{{ $val }}" {{ $provider->type==$val?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="fg"><label>Nr klienta</label><input type="text" name="client_number" value="{{ $provider->client_number }}"></div>
      <div class="fg"><label>Nr punktu poboru</label><input type="text" name="point_number" value="{{ $provider->point_number }}"></div>
      <div class="fg"><label>Adres</label><input type="text" name="address" value="{{ $provider->address }}"></div>
      <div class="fg"><label>Klasa scrapera</label><input type="text" name="scraper_class" value="{{ $provider->scraper_class }}" placeholder="App\Scrapers\TauronScraper"></div>
      <div class="fg"><label>URL scrapera / API</label><input type="text" name="api_endpoint" value="{{ $provider->api_endpoint }}" placeholder="https://..."></div>
      <div class="fg"><label>Ikona</label>
        <select name="icon">
          @foreach(['elec'=>'⚡ elec','gas'=>'🔥 gas','water'=>'💧 water','net'=>'🌐 net','bank'=>'🏦 bank','doc'=>'📄 doc'] as $val=>$label)
            <option value="{{ $val }}" {{ $provider->icon==$val?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="fg"><label>Kolor akcentu</label>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="color" id="colorPick" value="{{ $provider->color }}" style="height:36px;width:60px;padding:2px" oninput="document.getElementById('colorHex').value=this.value">
          <input type="text" name="color" id="colorHex" value="{{ $provider->color }}" style="width:100px" oninput="document.getElementById('colorPick').value=this.value">
        </div>
      </div>
      <div style="display:flex;gap:8px;margin-top:12px">
        <button type="submit" class="btn bp">💾 Zapisz</button>
        <a href="/provider/{{ $provider->id }}" class="btn bg">Anuluj</a>
      </div>
    </form>
  </div>

  <div class="card mb-3" style="border:1px solid #e53e3e33">
    <div class="ct" style="color:#e53e3e">⚠️ Strefa niebezpieczna</div>
    <p style="font-size:.85rem;color:var(--mu);margin-bottom:12px">Dostawca ma <strong>{{ $billCount }}</strong> dokumentów.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <form method="POST" action="/provider/{{ $provider->id }}/bills" onsubmit="return confirm('Usunąć wszystkie {{ $billCount }} dokumenty dostawcy {{ $provider->name }}? Tej operacji nie można cofnąć.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn" style="background:#e53e3e;color:#fff">🗑 Usuń wszystkie dokumenty ({{ $billCount }})</button>
      </form>
      <form method="POST" action="/provider/{{ $provider->id }}" onsubmit="return confirm('Usunąć dostawcę {{ $provider->name }} WRAZ ze wszystkimi danymi? Tej operacji nie można cofnąć.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn" style="background:#742a2a;color:#fff">💥 Usuń dostawcę i wszystkie dane</button>
      </form>
    </div>
  </div>
</div>
@endsection
