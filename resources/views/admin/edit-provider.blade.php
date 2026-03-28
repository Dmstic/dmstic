@extends('layout')
@section('title','Edytuj dostawce')
@section('content')
<div style="max-width:480px"><div class="card">
  <div class="ct">Edytuj: {{ $provider->name }}</div>
  <form method="POST" action="/provider/{{ $provider->id }}/edit">
    @csrf
    <div class="fg"><label>Nazwa</label><input type="text" name="name" value="{{ $provider->name }}" required></div>
    <div class="fg"><label>Kod ikony (elec/gas/water/net/bank/doc)</label><input type="text" name="icon" value="{{ $provider->icon }}"></div>
    <div class="fg"><label>Kolor akcentu</label><div style="display:flex;gap:8px;align-items:center"><input type="color" name="color" value="{{ $provider->color }}" style="height:36px;width:60px;padding:2px"><span style="font-size:.8rem;color:var(--mu)">{{ $provider->color }}</span></div></div>
    <div class="fg"><label>Nr klienta</label><input type="text" name="client_number" value="{{ $provider->client_number }}"></div>
    <div class="fg"><label>Nr punktu poboru</label><input type="text" name="point_number" value="{{ $provider->point_number }}"></div>
    <div class="fg"><label>Adres</label><input type="text" name="address" value="{{ $provider->address }}"></div>
    <div class="fg"><label>Klasa scrapera</label><input type="text" name="scraper_class" value="{{ $provider->scraper_class }}"></div>
    <div style="display:flex;gap:8px"><button type="submit" class="btn bp">Zapisz</button><a href="/provider/{{ $provider->id }}" class="btn bg">Anuluj</a></div>
  </form>
</div></div>
@endsection
