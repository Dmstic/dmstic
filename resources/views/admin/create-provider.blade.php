@extends('layout')
@section('title','Dodaj dostawce')
@section('content')
<div style="max-width:480px"><div class="card">
  <div class="ct">Nowy dostawca mediow</div>
  <form method="POST" action="/admin/provider/create">
    @csrf
    <div class="fg"><label>Nazwa *</label><input type="text" name="name" required placeholder="np. Aquanet, Multimedia"></div>
    <div class="fg"><label>Typ media *</label>
      <select name="type" required>
        <option value="electricity">Energia elektryczna</option>
        <option value="gas">Gaz</option>
        <option value="water">Woda</option>
        <option value="internet">Internet / Multimedia</option>
        <option value="bank">Bank / Wyciagi</option>
        <option value="other">Inne</option>
      </select>
    </div>
    <div class="fg"><label>Nr klienta</label><input type="text" name="client_number"></div>
    <div class="fg"><label>Nr punktu poboru</label><input type="text" name="point_number"></div>
    <div class="fg"><label>Adres obiektu</label><input type="text" name="address" placeholder="ul. Przykladowa 1, 00-000 Miasto"></div>
    <div class="fg"><label>URL logowania (scraper)</label><input type="url" name="scraper_url" placeholder="https://..."></div>
    <div style="display:flex;gap:8px"><button type="submit" class="btn bp">Dodaj dostawce</button><a href="/" class="btn bg">Anuluj</a></div>
  </form>
</div></div>
@endsection
