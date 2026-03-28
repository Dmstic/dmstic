<!DOCTYPE html>
<html lang="pl" data-theme="{{ $appSettings['color_scheme'] ?? 'dark' }}">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'Dmstic')</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root,[data-theme="dark"]{--bg:#0f1117;--sf:#1a1f2e;--sf2:#16213e;--bd:#2d3748;--tx:#e2e8f0;--mu:#718096;--ac:#63b3ed;--sb:#13151e}
[data-theme="light"]{--bg:#f0f4f8;--sf:#fff;--sf2:#edf2f7;--bd:#e2e8f0;--tx:#1a202c;--mu:#718096;--ac:#3182ce;--sb:#1a202c}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--tx);display:flex;flex-direction:column;min-height:100vh}
.wrap{display:flex;flex:1}
.sb{width:220px;background:var(--sb);border-right:1px solid var(--bd);display:flex;flex-direction:column;position:sticky;top:0;height:100vh;flex-shrink:0}
.sb-logo{padding:18px 16px 14px;border-bottom:1px solid var(--bd)}
.sb-logo a{font-size:1.2rem;font-weight:700;color:var(--ac);text-decoration:none}
.sb-tag{font-size:.6rem;color:var(--mu);margin-top:2px}
.sb-nav{flex:1;padding:6px 0;overflow-y:auto}
.sb-nav a{display:flex;align-items:center;gap:8px;padding:8px 14px;color:var(--mu);text-decoration:none;font-size:.85rem;border-left:3px solid transparent;transition:all .15s}
.sb-nav a:hover{color:var(--tx);background:rgba(255,255,255,.04)}
.sb-nav a.act{color:var(--tx);background:rgba(99,179,237,.1);border-left-color:var(--ac)}
.sb-sec{padding:8px 14px 3px;font-size:.6rem;text-transform:uppercase;letter-spacing:.08em;color:var(--mu);margin-top:6px}
.sb-add{padding:7px 14px;font-size:.78rem;color:var(--ac);opacity:.7;text-decoration:none;display:block;transition:opacity .15s}
.sb-add:hover{opacity:1}
.sb-foot{padding:10px 0;border-top:1px solid var(--bd)}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.mh{padding:14px 26px;border-bottom:1px solid var(--bd);background:var(--sf);display:flex;align-items:center;justify-content:space-between}
.mh h1{font-size:1.1rem;font-weight:600}
.mc{padding:22px 26px;flex:1}
footer{text-align:center;padding:10px;color:var(--mu);font-size:.72rem;border-top:1px solid var(--bd)}
.card{background:var(--sf);border:1px solid var(--bd);border-radius:10px;padding:18px;margin-bottom:18px}
.ct{font-size:.8rem;font-weight:600;color:var(--mu);margin-bottom:14px}
.sg{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px}
.sc{background:var(--sf);border:1px solid var(--bd);border-radius:10px;padding:14px}
.sc .lb{font-size:.65rem;text-transform:uppercase;letter-spacing:.05em;color:var(--mu);margin-bottom:5px}
.sc .vl{font-size:1.4rem;font-weight:700;color:var(--ac)}
.sc .sb2{font-size:.72rem;color:var(--mu);margin-top:2px}
.g2{display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:18px}
table{width:100%;border-collapse:collapse;font-size:.83rem}
th{padding:7px 11px;text-align:left;font-size:.65rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mu);border-bottom:1px solid var(--bd)}
td{padding:7px 11px;border-bottom:1px solid rgba(45,55,72,.5)}
tr:hover td{background:rgba(255,255,255,.02)}
.tr{text-align:right}
.b{padding:2px 6px;border-radius:4px;font-size:.68rem;font-weight:600;display:inline-block}
.bfv{background:rgba(99,179,237,.15);color:#63b3ed}
.bfk{background:rgba(252,129,129,.15);color:#fc8181}
.bno{background:rgba(246,173,85,.15);color:#f6ad55}
.bnb{background:rgba(183,148,244,.15);color:#b794f4}
.bok{background:rgba(104,211,145,.15);color:#68d391}
.bwait{background:rgba(252,129,129,.15);color:#fc8181}
.bset{background:rgba(160,174,192,.15);color:#a0aec0}
.neg{color:#fc8181}.pos{color:#68d391}
.fb{background:var(--sf);border:1px solid var(--bd);border-radius:10px;padding:12px 14px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.fb label{font-size:.65rem;text-transform:uppercase;letter-spacing:.04em;color:var(--mu);display:block;margin-bottom:2px}
.fb input,.fb select{background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:5px 9px;font-size:.8rem}
.btn{padding:5px 12px;border-radius:6px;border:none;cursor:pointer;font-size:.8rem;font-weight:500;transition:opacity .15s;text-decoration:none;display:inline-block}
.bp{background:var(--ac);color:#fff}.bg{background:transparent;border:1px solid var(--bd);color:var(--mu)}.btn:hover{opacity:.8}
.tbr{display:flex;gap:2px;background:var(--sf2);border-radius:8px;padding:3px;margin-bottom:14px}
.tbb{flex:1;padding:6px 10px;background:transparent;border:none;color:var(--mu);border-radius:6px;cursor:pointer;font-size:.78rem;font-weight:500;transition:all .15s}
.tbb.act{background:var(--sf);color:var(--tx);box-shadow:0 1px 3px rgba(0,0,0,.3)}
.tp{display:none}.tp.act{display:block}
.tip{position:relative;cursor:help}
.tip:hover::after{content:attr(data-tip);position:absolute;bottom:100%;left:50%;transform:translateX(-50%);background:#000;color:#fff;padding:3px 7px;border-radius:4px;font-size:.68rem;white-space:nowrap;z-index:10;margin-bottom:3px}
.al{padding:9px 12px;border-radius:7px;margin-bottom:14px;font-size:.83rem}
.al-ok{background:rgba(104,211,145,.12);border:1px solid #68d391;color:#68d391}
form .fg{margin-bottom:12px}
form .fg label{font-size:.75rem;color:var(--mu);margin-bottom:3px;display:block}
form .fg input,form .fg select,form .fg textarea{background:var(--sf2);border:1px solid var(--bd);color:var(--tx);border-radius:6px;padding:7px 11px;font-size:.85rem;width:100%}
@media(max-width:700px){.sb{display:none}.g2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
<aside class="sb">
  <div class="sb-logo">
    <a href="/">{{ $appSettings['app_name'] ?? 'Dmstic' }}</a>
    <div class="sb-tag">Analiza kosztów domowych</div>
  </div>
  <nav class="sb-nav">
    <a href="/" class="{{ request()->is('/') ? 'act' : '' }}">&#x1F4CA; Przegląd</a>
    <div class="sb-sec">Dostawcy</div>
    @foreach($sidebarProviders as $p)
    @php $icons=['elec'=>'&#x26A1;','gas'=>'&#x1F525;','water'=>'&#x1F4A7;','net'=>'&#x1F310;','bank'=>'&#x1F3E6;','doc'=>'&#x1F4C4;']; @endphp
    <a href="/provider/{{ $p->id }}" class="{{ request()->is('provider/'.$p->id) || request()->is('provider/'.$p->id.'/*') ? 'act' : '' }}" style="{{ request()->is('provider/'.$p->id) || request()->is('provider/'.$p->id.'/*') ? 'border-left-color:'.$p->color : '' }}">
      {!! $icons[$p->icon] ?? '📄' !!} {{ $p->name }}
    </a>
    @endforeach
    <a href="/admin/provider/create" class="sb-add">&#xFF0B; Dodaj dostawcę</a>
    <div class="sb-sec">System</div>
    <a href="/settings" class="{{ request()->is('settings') ? 'act' : '' }}">&#x2699;&#xFE0F; Ustawienia</a>
    <a href="/docs" class="{{ request()->is('docs') ? 'act' : '' }}">&#x1F4DA; Dokumentacja</a>
  </nav>
</aside>
<div class="main">
  <div class="mh"><h1>@yield('title','Przegląd')</h1></div>
  <div class="mc">
    @if(session('success'))<div class="al al-ok">{{ session('success') }}</div>@endif
    @yield('content')
  </div>
  <footer>Dmstic &copy; 2026</footer>
</div>
</div>
<script>
document.querySelectorAll(".tbb").forEach(b=>b.addEventListener("click",()=>{
  const g=b.closest("[data-tabs]");
  g.querySelectorAll(".tbb").forEach(x=>x.classList.remove("act"));
  g.querySelectorAll(".tp").forEach(x=>x.classList.remove("act"));
  b.classList.add("act");
  document.getElementById(b.dataset.tab).classList.add("act");
}));
const saved=localStorage.getItem("theme");
if(saved) document.documentElement.dataset.theme=saved;
</script>
@stack('scripts')
</body></html>
