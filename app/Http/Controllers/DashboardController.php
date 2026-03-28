<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller {
    private function icon($code) {
        return match($code) { "elec"=>"⚡","gas"=>"🔥","water"=>"💧","net"=>"🌐","bank"=>"🏦",default=>"📄" };
    }
    public function index() {
        $perProvider = DB::table("bills")
            ->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("energy_providers.id, energy_providers.name, energy_providers.type, energy_providers.icon, energy_providers.color, SUM(bills.amount_gross) as total, SUM(bills.consume_energy) as kwh, COUNT(*) as cnt")
            ->where("bills.doc_type","FV")->where("bills.is_correction",0)
            ->groupBy("energy_providers.id","energy_providers.name","energy_providers.type","energy_providers.icon","energy_providers.color")
            ->get()->map(fn($p) => tap($p, fn($p) => $p->icon_html = $this->icon($p->icon)));
        $totalCost = $perProvider->sum("total");
        $totalKwh = $perProvider->sum("kwh");
        $monthly = DB::table("bills")
            ->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("YEAR(bills.issue_date) as yr, MONTH(bills.issue_date) as mo, energy_providers.name as pname, energy_providers.color as pcolor, SUM(bills.amount_gross) as total")
            ->where("bills.doc_type","FV")->where("bills.is_correction",0)
            ->groupByRaw("yr, mo, energy_providers.id, energy_providers.name, energy_providers.color")
            ->orderByRaw("yr, mo")->get();
        $labels = $monthly->map(fn($r) => sprintf("%04d-%02d",$r->yr,$r->mo))->unique()->values();
        $datasets = $monthly->groupBy("pname")->map(fn($rows,$name) => [
            "label"=>$name, "color"=>$rows->first()->pcolor,
            "data"=>$labels->map(fn($l) => $rows->first(fn($r)=>sprintf("%04d-%02d",$r->yr,$r->mo)===$l)?->total??0)->values()
        ])->values();
        $lastBills = DB::table("bills")
            ->join("energy_providers","bills.provider_id","=","energy_providers.id")
            ->selectRaw("energy_providers.name as pname, energy_providers.icon as picon, energy_providers.color as pcolor, energy_providers.id as pid, bills.doc_number, bills.amount_gross, bills.issue_date, bills.status")
            ->where("bills.doc_type","FV")->where("bills.is_correction",0)
            ->orderBy("bills.issue_date","desc")->limit(10)->get();
        $iconFn = fn($c) => $this->icon($c);
        return view("dashboard", compact("perProvider","totalCost","totalKwh","labels","datasets","lastBills","iconFn"));
    }
}
