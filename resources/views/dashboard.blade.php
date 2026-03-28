@extends('layout')
@section('title','Przegląd')
@section('content')
<div class="card mb-3" style="padding:10px 14px">
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <span style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mu);white-space:nowrap">Okres:</span>
    <div class="yr-btns" style="margin:0">
      <a href="/" class="yr-btn {{ !$year && !$dateFrom?'act':'' }}">Wszystkie</a>
      @foreach($availYears as $yr)
      <a href="/?year={{ $yr }}" class="yr-btn {{ $year==$yr?'act':'' }}">{{ $yr }}</a>
      @endforeach
    </div>
    <form method="GET" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin:0">
      <input type="date" name="from" value="{{ $dateFrom }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:4px 8px;font-size:.78rem">
      <span style="color:var(--mu);font-size:.78rem">—</span>
      <input type="date" name="to" value="{{ $dateTo }}" style="background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:4px 8px;font-size:.78rem">
      <button type="submit" class="btn bp" style="padding:4px 10px;font-size:.78rem">OK</button>
      @if($dateFrom||$dateTo||$year)<a href="/" class="btn bg" style="padding:4px 10px;font-size:.78rem">&#x2715;</a>@endif
    </form>
  </div>
</div>
<div class="sg">
  <div class="sc"><div class="lb">Łączne wydatki</div><div class="vl">{{ number_format($totalCost,0,',',' ') }} PLN</div><div class="sb2">wszystkie dostawcy</div></div>
  <div class="sc"><div class="lb">Łączne zużycie</div><div class="vl">{{ number_format($totalKwh,0,',',' ') }} kWh</div><div class="sb2">energia + gaz</div></div>
  <div class="sc"><div class="lb">Dostawcy</div><div class="vl">{{ count($perProvider) }}</div><div class="sb2">aktywne źródła</div></div>
  <div class="sc"><div class="lb">Dokumenty</div><div class="vl">{{ $perProvider->sum('cnt') }}</div><div class="sb2">łącznie faktur FV</div></div>
</div>
<div class="g2">
  <div class="card">
    <div class="ct">Koszty miesięczne wg dostawców</div>
    <canvas id="cOverview" style="max-height:250px"></canvas>
  </div>
  <div class="card">
    <div class="ct">Podział kosztów</div>
    @foreach($perProvider as $p)
    @php $icons=['elec'=>'⚡','gas'=>'🔥','water'=>'💧','net'=>'🌐','bank'=>'🏦','doc'=>'📄']; @endphp
    <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--bd)">
      <div style="display:flex;align-items:center;gap:7px">
        <span style="width:9px;height:9px;border-radius:50%;background:{{$p->color}};display:inline-block"></span>
        <a href="/provider/{{$p->id}}" style="color:var(--tx);text-decoration:none;font-size:.85rem">{{ $icons[$p->icon]??'📄' }} {{$p->name}}</a>
      </div>
      <div style="text-align:right">
        <div style="font-weight:600;color:{{$p->color}};font-size:.9rem">{{ number_format($p->total,0,',',' ') }} PLN</div>
        <div style="font-size:.72rem;color:var(--mu)">{{ number_format($p->kwh,0,',',' ') }} kWh</div>
      </div>
    </div>
    @endforeach
    <canvas id="cDoughnut" style="max-height:160px;margin-top:14px"></canvas>
  </div>
</div>
<div class="card">
  <div class="ct">Ostatnie faktury</div>
  <table>
    <thead><tr><th>Dostawca</th><th>Nr dokumentu</th><th>Data</th><th class="tr">Kwota</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($lastBills as $b)
      @php $icons=['elec'=>'⚡','gas'=>'🔥','water'=>'💧','net'=>'🌐','bank'=>'🏦']; @endphp
      <tr>
        <td><a href="/provider/{{$b->pid}}" style="color:{{$b->pcolor}};text-decoration:none;font-size:.83rem">{{ $icons[$b->picon]??'📄' }} {{$b->pname}}</a></td>
        <td style="font-family:monospace;font-size:.75rem;color:var(--mu)">{{$b->doc_number}}</td>
        <td style="font-size:.82rem">{{\Carbon\Carbon::parse($b->issue_date)->format('d.m.Y')}}</td>
        <td class="tr" style="font-weight:500">{{ number_format($b->amount_gross,2,',',' ') }}</td>
        <td><span class="b {{ $b->status=='Opłacona'?'bok':'bwait' }}">{{$b->status}}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
@push('scripts')
<script>
Chart.defaults.color='#718096';Chart.defaults.borderColor='#2d3748';
const labels={!! json_encode($labels) !!};
const ds={!! json_encode($datasets) !!};
new Chart(document.getElementById('cOverview'),{type:'bar',data:{labels,datasets:ds.map(d=>({label:d.label,data:d.data,backgroundColor:d.color+'99',borderColor:d.color,borderWidth:1,borderRadius:3}))},options:{responsive:true,interaction:{mode:'index',intersect:false},scales:{x:{stacked:true,grid:{color:'rgba(255,255,255,.04)'}},y:{stacked:true,grid:{color:'rgba(255,255,255,.04)'},title:{display:true,text:'PLN'}}}}});
const pp={!! json_encode($perProvider->map(fn($p)=>['name'=>$p->name,'total'=>$p->total,'color'=>$p->color])->values()) !!};
new Chart(document.getElementById('cDoughnut'),{type:'doughnut',data:{labels:pp.map(p=>p.name),datasets:[{data:pp.map(p=>p.total),backgroundColor:pp.map(p=>p.color+'cc'),borderColor:'var(--sf)',borderWidth:2}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:10},padding:8}}}}});
</script>
@endpush
