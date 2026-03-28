<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller {
    private function icon($code) {
        return match($code) { "elec"=>"⚡","gas"=>"🔥","water"=>"💧","net"=>"🌐","bank"=>"🏦",default=>"📄" };
    }
    public function index(Request $req) {
        $year = $req->input('year');
        $dateFrom = $req->input('from');
        $dateTo = $req->input('to');
        if ($year && !$dateFrom) { $dateFrom = "{$year}-01-01"; $dateTo = "{$year}-12-31"; }  // year quick-select
        $availYears = DB::table("bills")->selectRaw("DISTINCT YEAR(issue_date) as yr")->where("doc_type","FV")->orderBy("yr","desc")->pluck("yr");
        $baseQ = fn($q) => $q->where("bills.doc_type","FV")->where("bills.is_correction",0)
            ->when($dateFrom, fn($q) => $q->where("bills.issue_date",">=",$dateFrom))
            ->when($dateTo,   fn($q) => $q->where("bills.issue_date","<=",$dateTo));
        $perProvider = $baseQ(DB::table("bills")->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("energy_providers.id, energy_providers.name, energy_providers.type, energy_providers.icon, energy_providers.color, SUM(bills.amount_gross) as total, SUM(bills.consume_energy) as kwh, COUNT(*) as cnt")
            ->groupBy("energy_providers.id","energy_providers.name","energy_providers.type","energy_providers.icon","energy_providers.color"))
            ->get()->map(fn($p) => tap($p, fn($p) => $p->icon_html = $this->icon($p->icon)));
        $totalCost = $perProvider->sum("total");
        $totalKwh  = $perProvider->sum("kwh");
        $monthly = $baseQ(DB::table("bills")->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("YEAR(bills.issue_date) as yr, MONTH(bills.issue_date) as mo, energy_providers.name as pname, energy_providers.color as pcolor, SUM(bills.amount_gross) as total")
            ->groupByRaw("yr, mo, energy_providers.id, energy_providers.name, energy_providers.color")
            ->orderByRaw("yr, mo"))->get();
        $labels = $monthly->map(fn($r) => sprintf("%04d-%02d",$r->yr,$r->mo))->unique()->values();
        $datasets = $monthly->groupBy("pname")->map(fn($rows,$name) => [
            "label"=>$name, "color"=>$rows->first()->pcolor,
            "data"=>$labels->map(fn($l) => $rows->first(fn($r)=>sprintf("%04d-%02d",$r->yr,$r->mo)===$l)?->total??0)->values()
        ])->values();
        $lastBills = $baseQ(DB::table("bills")->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("energy_providers.name as pname, energy_providers.icon as picon, energy_providers.color as pcolor, energy_providers.id as pid, bills.doc_number, bills.amount_gross, bills.issue_date, bills.status")
            ->orderBy("bills.issue_date","desc")->limit(10))->get();
        $iconFn = fn($c) => $this->icon($c);
        // Payment progress — current calendar month, all providers
        $curY = (int)date('Y'); $curM = (int)date('n');
        $monthFV = DB::table("bills")->where("doc_type","FV")->where("is_correction",0)
            ->whereYear("issue_date",$curY)->whereMonth("issue_date",$curM)->get();
        $payProgress = [
            'total'      => $monthFV->sum('amount_gross'),
            'paid'       => $monthFV->whereIn('status',['Opłacona','Rozliczono'])->sum('amount_gross'),
            'count'      => $monthFV->count(),
            'paid_count' => $monthFV->whereIn('status',['Opłacona','Rozliczono'])->count(),
            'label'      => sprintf('%s %d', ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'][$curM-1], $curY),
        ];
        return view("dashboard", compact("perProvider","totalCost","totalKwh","labels","datasets","lastBills","iconFn","availYears","year","dateFrom","dateTo","payProgress"));
    }
}
