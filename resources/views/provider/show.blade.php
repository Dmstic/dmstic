@extends('layout')
@section('title', $provider->icon_html . ' ' . $provider->name)
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <div style="font-size:.8rem;color:var(--mu)">
    {{ ucfirst($provider->type) }} &middot; Nr klienta: {{ $provider->client_number }}
    @if($provider->address) &middot; {{ $provider->address }} @endif
  </div>
  <a href="/provider/{{ $provider->id }}/edit" class="btn bg">&#x270F; Edytuj</a>
</div>
<div class="sg">
  <div class="sc"><div class="lb">Łączny koszt</div><div class="vl" style="color:{{ $provider->color }}">{{ number_format($stats['total_cost'],0,',',' ') }} PLN</div><div class="sb2">{{ $stats['count'] }} faktur FV</div></div>
  <div class="sc"><div class="lb">Łączne zużycie</div><div class="vl">{{ number_format($stats['total_kwh'],0,',',' ') }} kWh</div></div>
  <div class="sc"><div class="lb">Śr. / faktura</div><div class="vl">{{ number_format($stats['avg_monthly'],0,',',' ') }} PLN</div></div>
  <div class="sc"><div class="lb">Ostatnia faktura</div><div class="vl">{{ $lastBill ? number_format($lastBill->amount_gross,2,',',' ') : '—' }}</div><div class="sb2">{{ $lastBill ? \Carbon\Carbon::parse($lastBill->issue_date)->format('d.m.Y') : '' }}</div></div>
</div>

<div class="g2">
  <div class="card">
    <div class="ct">Zużycie i koszty miesięczne</div>
    <canvas id="cMonthly" style="max-height:230px"></canvas>
  </div>
  <div class="card">
    <div class="ct">Wgraj dokument</div>
    <form method="POST" action="/admin/upload" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="provider_id" value="{{ $provider->id }}">
      <div class="fg"><label>Plik (PDF / JPG / PNG, max 10MB)</label><input type="file" name="document" accept=".pdf,.jpg,.png"></div>
      <button type="submit" class="btn bp">&#x1F4CE; Wgraj</button>
    </form>
    <div style="margin-top:14px;font-size:.73rem;color:var(--mu)">
      Dokumenty w: <code style="background:var(--sf2);padding:2px 6px;border-radius:4px;font-size:.72rem">storage/documents/{{ $provider->id }}/</code>
    </div>
  </div>
</div>

@if(count($documents))
<div class="card">
  <div class="ct">&#x1F4C2; Dokumenty ({{ count($documents) }})</div>
  <div class="doc-grid">
    @foreach($documents as $doc)
    <div class="doc-card" onclick="viewDoc('{{ $doc['url'] }}','{{ $doc['ext'] }}','{{ addslashes($doc['name']) }}')">
      <div class="doc-icon">
        @if($doc['ext']=='pdf')📄@elseif(in_array($doc['ext'],['jpg','jpeg','png','webp']))🖼️@else📎@endif
      </div>
      <div class="doc-name">{{ $doc['name'] }}</div>
    </div>
    @endforeach
  </div>
  <div id="docViewer" style="display:none;margin-top:14px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
      <span id="docViewerName" style="font-size:.82rem;color:var(--mu)"></span>
      <div style="display:flex;gap:8px">
        <a id="docViewerLink" href="#" target="_blank" class="btn bg" style="font-size:.75rem">&#x1F517; Otwórz</a>
        <button onclick="closeDoc()" class="btn bg" style="font-size:.75rem">&#x2715; Zamknij</button>
      </div>
    </div>
    <div id="docViewerContent"></div>
  </div>
</div>
@endif

<div class="card" data-tabs>
  <div class="ct">Analizy</div>
  <div class="tbr">
    <button class="tbb act" data-tab="t-cpk">Koszt / kWh</button>
    <button class="tbb" data-tab="t-yoy">Rok do roku</button>
    <button class="tbb" data-tab="t-per">Porównanie okresu</button>
    <button class="tbb" data-tab="t-fcst">&#x1F52E; Prognoza</button>
  </div>
  <div id="t-cpk" class="tp act">
    <canvas id="cCpk" style="max-height:180px;margin-bottom:12px"></canvas>
    <table><thead><tr><th>Miesiąc</th><th class="tr">PLN / kWh</th></tr></thead><tbody>
      @foreach($costPerKwh as $c)<tr><td>{{ $c['label'] }}</td><td class="tr">{{ number_format($c['val'],4,',',' ') }}</td></tr>@endforeach
    </tbody></table>
  </div>
  <div id="t-yoy" class="tp">
    <div style="overflow-x:auto"><table>
      <thead>
        <tr><th>Miesiąc</th>@foreach($years as $y)<th class="tr" colspan="2">{{ $y }}</th>@endforeach</tr>
        <tr><th></th>@foreach($years as $y)<th class="tr" style="font-size:.6rem;color:var(--mu)">kWh</th><th class="tr" style="font-size:.6rem;color:var(--mu)">PLN</th>@endforeach</tr>
      </thead>
      <tbody>
        @foreach($yoy as $row)
        <tr>
          <td>{{ $row['name'] }}</td>
          @foreach($years as $y)
          <td class="tr">{{ $row["y{$y}_kwh"] !== null ? number_format($row["y{$y}_kwh"],0,',',' ') : '—' }}</td>
          <td class="tr">{{ $row["y{$y}_pln"] !== null ? number_format($row["y{$y}_pln"],2,',',' ') : '—' }}</td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table></div>
  </div>
  <div id="t-per" class="tp">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;align-items:flex-end">
      <div><label style="font-size:.7rem;color:var(--mu);display:block;margin-bottom:2px">Okres A: od</label><input type="date" name="p1f" value="{{ request('p1f') }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:5px 9px;font-size:.8rem"></div>
      <div><label style="font-size:.7rem;color:var(--mu);display:block;margin-bottom:2px">do</label><input type="date" name="p1t" value="{{ request('p1t') }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:5px 9px;font-size:.8rem"></div>
      <span style="color:var(--mu);align-self:flex-end;padding-bottom:5px">vs</span>
      <div><label style="font-size:.7rem;color:var(--mu);display:block;margin-bottom:2px">Okres B: od</label><input type="date" name="p2f" value="{{ request('p2f') }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:5px 9px;font-size:.8rem"></div>
      <div><label style="font-size:.7rem;color:var(--mu);display:block;margin-bottom:2px">do</label><input type="date" name="p2t" value="{{ request('p2t') }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:5px 9px;font-size:.8rem"></div>
      <button type="submit" class="btn bp">Porównaj</button>
    </form>
    @if(request('p1f') && request('p1t') && request('p2f') && request('p2t'))
    @php
    $qb = fn($f,$t) => \DB::table('bills')->where('provider_id',$provider->id)->where('doc_type','FV')->where('is_correction',0)->whereBetween('issue_date',[$f,$t]);
    $pA = ['pln'=>$qb(request('p1f'),request('p1t'))->sum('amount_gross'),'kwh'=>$qb(request('p1f'),request('p1t'))->sum('consume_energy'),'cnt'=>$qb(request('p1f'),request('p1t'))->count()];
    $pB = ['pln'=>$qb(request('p2f'),request('p2t'))->sum('amount_gross'),'kwh'=>$qb(request('p2f'),request('p2t'))->sum('consume_energy'),'cnt'=>$qb(request('p2f'),request('p2t'))->count()];
    @endphp
    <table><thead><tr><th>Wskaźnik</th><th class="tr">Okres A<br><small style="font-weight:normal;color:var(--mu)">{{ request('p1f') }} – {{ request('p1t') }}</small></th><th class="tr">Okres B<br><small style="font-weight:normal;color:var(--mu)">{{ request('p2f') }} – {{ request('p2t') }}</small></th><th class="tr">Różnica</th></tr></thead><tbody>
      @php $d=$pA['pln']-$pB['pln']; @endphp
      <tr><td>Koszt PLN</td><td class="tr">{{ number_format($pA['pln'],2,',',' ') }}</td><td class="tr">{{ number_format($pB['pln'],2,',',' ') }}</td><td class="tr {{ $d<0?'pos':($d>0?'neg':'') }}">{{ ($d>0?'+':'') . number_format($d,2,',',' ') }}</td></tr>
      @php $d=$pA['kwh']-$pB['kwh']; @endphp
      <tr><td>Zużycie kWh</td><td class="tr">{{ number_format($pA['kwh'],0,',',' ') }}</td><td class="tr">{{ number_format($pB['kwh'],0,',',' ') }}</td><td class="tr {{ $d<0?'pos':($d>0?'neg':'') }}">{{ ($d>0?'+':'') . number_format($d,0,',',' ') }}</td></tr>
      <tr><td>Faktury</td><td class="tr">{{ $pA['cnt'] }}</td><td class="tr">{{ $pB['cnt'] }}</td><td class="tr">{{ ($pA['cnt']-$pB['cnt']>0?'+':'') . ($pA['cnt']-$pB['cnt']) }}</td></tr>
      @if($pA['kwh']>0 && $pB['kwh']>0)
      <tr><td>Koszt / kWh</td><td class="tr">{{ number_format($pA['pln']/$pA['kwh'],4,',',' ') }} PLN</td><td class="tr">{{ number_format($pB['pln']/$pB['kwh'],4,',',' ') }} PLN</td><td></td></tr>
      @endif
    </tbody></table>
    @endif
  </div>
  <div id="t-fcst" class="tp">
    @if(count($forecast))
    <p style="font-size:.78rem;color:var(--mu);margin-bottom:12px">Prognoza na 6 miesięcy oparta na regresji liniowej danych historycznych (faktury FV).</p>
    <canvas id="cFcst" style="max-height:200px;margin-bottom:14px"></canvas>
    <table><thead><tr><th>Miesiąc</th><th class="tr">Prognozowany koszt PLN</th><th>Typ</th></tr></thead><tbody>
      @foreach($forecast as $f)
      <tr><td>{{ $f['label'] }}</td><td class="tr">{{ number_format($f['val'],2,',',' ') }}</td><td><span class="b" style="background:rgba(183,148,244,.15);color:#b794f4">Prognoza</span></td></tr>
      @endforeach
    </tbody></table>
    <p style="font-size:.7rem;color:var(--mu);margin-top:12px">&#x26A0; Prognoza ma charakter orientacyjny i opiera się wyłącznie na trendzie historycznym. Nie uwzględnia sezonowości ani zmian cen.</p>
    @else
    <p style="color:var(--mu);font-size:.83rem">Za mało danych do wygenerowania prognozy (minimum 3 miesiące faktur FV).</p>
    @endif
  </div>
</div>

<div class="card">
  <div class="ct">Filtruj dokumenty</div>
  <div style="margin-bottom:10px">
    <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mu);margin-bottom:4px">Szybki wybór roku</div>
    <div class="yr-btns">
      <a href="/provider/{{ $provider->id }}" class="yr-btn {{ !$yearFilter && !$dateFrom?'act':'' }}">Wszystkie</a>
      @foreach($availYears as $yr)
      <a href="/provider/{{ $provider->id }}?year={{ $yr }}&type={{ $typeFilter }}&status={{ $statusFilter }}" class="yr-btn {{ $yearFilter==$yr?'act':'' }}">{{ $yr }}</a>
      @endforeach
    </div>
  </div>
  <form method="GET" class="fb">
    <div><label>Data od</label><input type="date" name="from" value="{{ $dateFrom }}"></div>
    <div><label>Data do</label><input type="date" name="to" value="{{ $dateTo }}"></div>
    <div>
      <label class="tip" data-tip="FV=Faktura VAT · FK=Korekta · NO=Nota Odsetkowa · NB=Nota Bankowa">Typ &#x2139;</label>
      <select name="type">
        <option value="all" {{ $typeFilter=='all'?'selected':'' }}>Wszystkie typy</option>
        @foreach($docTypes as $t)
        <option value="{{ $t }}" {{ $typeFilter==$t?'selected':'' }}>
          {{ $t }} — @if($t=='FV')Faktura VAT@elseif($t=='FK')Korekta@elseif($t=='NO')Nota Odsetkowa@elseif($t=='NB')Nota Bankowa@else{{ $t }}@endif
        </option>
        @endforeach
      </select>
    </div>
    <div>
      <label>Status</label>
      <select name="status">
        <option value="all" {{ $statusFilter=='all'?'selected':'' }}>Wszystkie statusy</option>
        @foreach($statuses as $s)<option value="{{ $s }}" {{ $statusFilter==$s?'selected':'' }}>{{ $s }}</option>@endforeach
      </select>
    </div>
    <div style="display:flex;gap:6px">
      <button type="submit" class="btn bp">Filtruj</button>
      <a href="/provider/{{ $provider->id }}" class="btn bg">Reset</a>
    </div>
  </form>
  <div style="font-size:.76rem;color:var(--mu);margin-bottom:10px">Wyniki: <strong>{{ count($bills) }}</strong> dokumentów
    @if($yearFilter) &middot; rok: <strong>{{ $yearFilter }}</strong>@endif
    @if($dateFrom || $dateTo) &middot; zakres: {{ $dateFrom }} – {{ $dateTo }}@endif
  </div>
  <table>
    <thead><tr>
      <th>Nr dokumentu</th>
      <th class="tip" data-tip="FV=Faktura VAT · FK=Korekta · NO=Nota Odsetkowa · NB=Nota Bankowa">Typ &#x2139;</th>
      <th>Data</th><th>Termin</th>
      <th class="tr">Brutto</th><th class="tr">Netto</th><th class="tr">kWh</th><th>Status</th>
    </tr></thead>
    <tbody>
      @foreach($bills as $b)
      <tr>
        <td style="font-family:monospace;font-size:.74rem;color:var(--mu)">{{ $b->doc_number }}</td>
        <td>
          @if($b->doc_type=='FV')<span class="b bfv">FV</span>
          @elseif($b->doc_type=='FK')<span class="b bfk">FK</span>
          @elseif($b->doc_type=='NO')<span class="b bno">NO</span>
          @else<span class="b bnb">{{ $b->doc_type }}</span>@endif
        </td>
        <td style="font-size:.82rem">{{\Carbon\Carbon::parse($b->issue_date)->format('d.m.Y')}}</td>
        <td style="font-size:.82rem">{{\Carbon\Carbon::parse($b->payment_date)->format('d.m.Y')}}</td>
        <td class="tr {{ $b->amount_gross<0?'neg':'' }}">{{ number_format($b->amount_gross,2,',',' ') }}</td>
        <td class="tr {{ $b->amount_net<0?'neg':'' }}">{{ number_format($b->amount_net,2,',',' ') }}</td>
        <td class="tr">{{ $b->consume_energy!=0 ? number_format($b->consume_energy,0,',',' ') : '—' }}</td>
        <td>
          @if($b->status=='Opłacona')<span class="b bok">Opłacona</span>
          @elseif($b->status=='Rozliczono')<span class="b bset">Rozliczono</span>
          @else<span class="b bwait">{{ $b->status }}</span>@endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
@push('scripts')
<script>
Chart.defaults.color='#718096';Chart.defaults.borderColor='#2d3748';
const m={!! json_encode($monthly) !!},c='{{ $provider->color }}';
new Chart(document.getElementById('cMonthly'),{type:'bar',data:{labels:m.map(r=>`${r.yr}-${String(r.mo).padStart(2,'0')}`),datasets:[{label:'kWh',data:m.map(r=>r.kwh),backgroundColor:c+'55',borderColor:c,borderWidth:1,borderRadius:3,yAxisID:'y'},{type:'line',label:'PLN',data:m.map(r=>r.total),borderColor:'#f6ad55',backgroundColor:'transparent',tension:.3,pointRadius:3,yAxisID:'y2'}]},options:{responsive:true,interaction:{mode:'index',intersect:false},scales:{x:{grid:{color:'rgba(255,255,255,.04)'}},y:{grid:{color:'rgba(255,255,255,.04)'},title:{display:true,text:'kWh'}},y2:{position:'right',grid:{display:false},title:{display:true,text:'PLN'}}}}});
const cpk={!! json_encode($costPerKwh->values()) !!};
if(cpk.length) new Chart(document.getElementById('cCpk'),{type:'line',data:{labels:cpk.map(r=>r.label),datasets:[{label:'PLN/kWh',data:cpk.map(r=>r.val),borderColor:'#f6ad55',backgroundColor:'rgba(246,173,85,.1)',fill:true,tension:.3,pointRadius:3}]},options:{responsive:true,scales:{x:{grid:{color:'rgba(255,255,255,.04)'}},y:{grid:{color:'rgba(255,255,255,.04)'},title:{display:true,text:'PLN/kWh'}}}}});
const fcst={!! json_encode($forecast) !!},hist={!! json_encode($monthly) !!};
if(fcst.length && document.getElementById('cFcst')){
  const hLabels=hist.map(r=>`${r.yr}-${String(r.mo).padStart(2,'0')}`);
  const hVals=hist.map(r=>r.total);
  const fLabels=fcst.map(r=>r.label);
  const fVals=fcst.map(r=>r.val);
  new Chart(document.getElementById('cFcst'),{type:'line',data:{labels:[...hLabels,...fLabels],datasets:[{label:'Historia PLN',data:[...hVals,...Array(fLabels.length).fill(null)],borderColor:c,backgroundColor:c+'22',fill:true,tension:.3,pointRadius:2},{label:'Prognoza PLN',data:[...Array(hLabels.length-1).fill(null),hVals[hVals.length-1],...fVals],borderColor:'#b794f4',backgroundColor:'rgba(183,148,244,.12)',fill:false,borderDash:[6,3],tension:.3,pointRadius:3}]},options:{responsive:true,scales:{x:{grid:{color:'rgba(255,255,255,.04)'}},y:{grid:{color:'rgba(255,255,255,.04)'},title:{display:true,text:'PLN'}}}}});
}
function viewDoc(url,ext,name){
  const v=document.getElementById('docViewer');
  const c=document.getElementById('docViewerContent');
  document.getElementById('docViewerName').textContent=name;
  document.getElementById('docViewerLink').href=url;
  if(ext==='pdf') c.innerHTML=`<iframe src="${url}" class="pdf-embed"></iframe>`;
  else if(['jpg','jpeg','png','webp','gif'].includes(ext)) c.innerHTML=`<img src="${url}" style="max-width:100%;border-radius:8px;border:1px solid var(--bd)">`;
  else c.innerHTML=`<p style="color:var(--mu);font-size:.82rem">Ten typ pliku (${ext}) nie obsługuje podglądu inline. <a href="${url}" target="_blank" style="color:var(--ac)">Otwórz</a></p>`;
  v.style.display='block';
  v.scrollIntoView({behavior:'smooth',block:'start'});
}
function closeDoc(){document.getElementById('docViewer').style.display='none';}
const h=location.hash.replace('#','');
if(h){const btn=document.querySelector(`.tbb[data-tab="${h}"]`);if(btn)btn.click();}
document.querySelectorAll('.tbb').forEach(b=>b.addEventListener('click',()=>location.hash=b.dataset.tab));
</script>
@endpush
