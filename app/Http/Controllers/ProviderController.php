<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProviderController extends Controller {
    public function show($id, Request $req) {
        $provider = DB::table("energy_providers")->where("id",$id)->first();
        if (!$provider) abort(404);
        $iconMap = ["elec"=>"⚡","gas"=>"🔥","water"=>"💧","net"=>"🌐","bank"=>"🏦"];
        $provider->icon_html = $iconMap[$provider->icon] ?? "📄";
        $dateFrom = $req->get("from");
        $dateTo   = $req->get("to");
        $typeFilter   = $req->get("type","all");
        $statusFilter = $req->get("status","all");
        $query = DB::table("bills")->where("provider_id",$id);
        if ($dateFrom) $query->where("issue_date",">=",$dateFrom);
        if ($dateTo)   $query->where("issue_date","<=",$dateTo);
        if ($typeFilter   !== "all") $query->where("doc_type",$typeFilter);
        if ($statusFilter !== "all") $query->where("status",$statusFilter);
        $bills = (clone $query)->orderBy("issue_date","desc")->get();
        $monthly = DB::table("bills")
            ->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)
            ->selectRaw("YEAR(issue_date) as yr, MONTH(issue_date) as mo, SUM(amount_gross) as total, SUM(consume_energy) as kwh")
            ->groupByRaw("yr, mo")->orderByRaw("yr, mo")->get();
        $costPerKwh = $monthly->filter(fn($r)=>$r->kwh>0)
            ->map(fn($r)=>["label"=>sprintf("%04d-%02d",$r->yr,$r->mo),"val"=>round($r->total/$r->kwh,4)])->values();
        $yoyRaw = DB::table("bills")
            ->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)
            ->selectRaw("YEAR(issue_date) as yr, MONTH(issue_date) as mo, SUM(amount_gross) as total, SUM(consume_energy) as kwh")
            ->groupByRaw("yr, mo")->get();
        $years = $yoyRaw->pluck("yr")->unique()->sort()->values();
        $mnames = ["Styczeń","Luty","Marzec","Kwiecień","Maj","Czerwiec","Lipiec","Sierpień","Wrzesień","Październik","Listopad","Grudzień"];
        $yoy = collect(range(1,12))->map(function($m) use ($yoyRaw,$years,$mnames) {
            $row = ["month"=>$m,"name"=>$mnames[$m-1]];
            foreach ($years as $y) { $d=$yoyRaw->first(fn($r)=>$r->yr==$y&&$r->mo==$m); $row["y{$y}_kwh"]=$d?->kwh; $row["y{$y}_pln"]=$d?->total; }
            return $row;
        })->filter(fn($r)=>$years->some(fn($y)=>isset($r["y{$y}_kwh"])&&$r["y{$y}_kwh"]!==null));
        $stats = [
            "total_cost"=>DB::table("bills")->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)->sum("amount_gross"),
            "total_kwh" =>DB::table("bills")->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)->sum("consume_energy"),
            "count"     =>DB::table("bills")->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)->count(),
            "avg_monthly"=>DB::table("bills")->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)->avg("amount_gross"),
        ];
        $lastBill = DB::table("bills")->where("provider_id",$id)->where("doc_type","FV")->where("is_correction",0)->orderBy("issue_date","desc")->first();
        $docTypes = DB::table("bills")->where("provider_id",$id)->distinct()->pluck("doc_type")->sort()->values();
        $statuses  = DB::table("bills")->where("provider_id",$id)->distinct()->pluck("status")->sort()->values();
        return view("provider.show", compact("provider","bills","monthly","costPerKwh","yoy","years","stats","lastBill","dateFrom","dateTo","typeFilter","statusFilter","docTypes","statuses"));
    }
}
