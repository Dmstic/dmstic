@extends('layout')
@section('title','Dokumentacja')
@section('content')
<div style="max-width:820px">
<div class="card">
  <div class="ct">O aplikacji Dmstic</div>
  <p style="font-size:.85rem;color:var(--mu);line-height:1.6">Aplikacja do analizy kosztów domowych. Agreguje dane z rachunków dostawców mediów i prezentuje wykresy, tabele i analizy trendów.</p>
</div>
<div class="card">
  <div class="ct">Architektura techniczna</div>
  <table><tbody>
    <tr><td><strong>Framework</strong></td><td>PHP 8.3 + Laravel 13.2</td></tr>
    <tr><td><strong>Baza danych</strong></td><td>MySQL — VM7000 (10.51.1.247)</td></tr>
    <tr><td><strong>Hosting</strong></td><td>VM7000 — Virtualmin, Apache, PHP CGI mode</td></tr>
    <tr><td><strong>SSL</strong></td><td>*.netol.com (ZeroSSL) — terminacja na ns2 (HAProxy)</td></tr>
    <tr><td><strong>URL</strong></td><td><a href="https://dmstic.netol.com" style="color:var(--ac)">https://dmstic.netol.com</a></td></tr>
  </tbody></table>
</div>
<div class="card">
  <div class="ct">Typy dokumentow (pole doc_type)</div>
  <table><thead><tr><th>Kod</th><th>Pełna nazwa</th><th>Opis</th></tr></thead><tbody>
    <tr><td><span class="b bfv">FV</span></td><td>Faktura VAT</td><td>Faktura sprzedaży z zużyciem i kwotą do zapłaty</td></tr>
    <tr><td><span class="b bfk">FK</span></td><td>Faktura Korygująca</td><td>Korekta faktury — kwoty mogą być ujemne</td></tr>
    <tr><td><span class="b bno">NO</span></td><td>Nota Odsetkowa</td><td>Odsetki za opóźnienie w płatności</td></tr>
    <tr><td><span class="b bnb">NB</span></td><td>Nota Bankowa</td><td>Rozliczenie bankowe lub kaucja</td></tr>
  </tbody></table>
</div>
<div class="card">
  <div class="ct">Źródła danych</div>
  <table><thead><tr><th>Dostawca</th><th>Typ</th><th>Metoda</th><th>Endpoint</th></tr></thead><tbody>
    <tr><td>&#x26A1; TAURON</td><td>Energia elektryczna</td><td>Internal REST API</td><td style="font-size:.72rem;font-family:monospace">moj.tauron.pl/api/sitecore/EbokArchiveDocuments/GetDocuments</td></tr>
    <tr><td>&#x1F525; ORLEN (PGNiG)</td><td>Gaz ziemny</td><td>DOM scraping</td><td style="font-size:.72rem;font-family:monospace">ebok.myorlen.pl/faktury</td></tr>
    <tr><td>&#x1F4A7; Woda</td><td>Woda</td><td>TBD</td><td>—</td></tr>
    <tr><td>&#x1F310; Internet</td><td>Multimedia</td><td>TBD</td><td>—</td></tr>
    <tr><td>&#x1F3E6; Bank</td><td>Wyciagi bankowe</td><td>TBD — import CSV</td><td>—</td></tr>
  </tbody></table>
</div>
<div class="card">
  <div class="ct">Przechowywanie dokumentow</div>
  <code style="display:block;background:var(--sf2);padding:8px;border-radius:6px;font-size:.75rem;margin-bottom:8px">/data/home/dmstic/app/storage/app/public/documents/{provider_id}/</code>
  <p style="font-size:.78rem;color:var(--mu)">URL: <code>https://dmstic.netol.com/storage/documents/{id}/plik.pdf</code></p>
</div>
<div class="card">
  <div class="ct">Roadmap</div>
  <ul style="font-size:.85rem;color:var(--mu);line-height:2;padding-left:18px">
    <li>&#x2705; TAURON — 40 dokumentow (2021–2026), 38 618 kWh, 35 269 PLN</li>
    <li>&#x2705; ORLEN/PGNiG — 29 dokumentow (2024–2026), 30 804 kWh, 11 673 PLN</li>
    <li>&#x2705; Dashboard z wykresami Chart.js</li>
    <li>&#x2705; Multi-dostawca — sidebar, podstrony, filtry</li>
    <li>&#x2705; Analityka: koszt/kWh, rok do roku, porownanie okresu</li>
    <li>&#x1F532; Pobieranie PDF z TAURON i ORLEN</li>
    <li>&#x1F532; AI parsing dokumentow (Claude API)</li>
    <li>&#x1F532; GitHub — repozytoria publiczne i prywatne</li>
    <li>&#x1F532; Woda, internet, wyciagi bankowe</li>
  </ul>
</div>
</div>
@endsection
