<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class ProviderController extends Controller {
    public function show($id, Request $req) {
        $provider = DB::table("energy_providers")->where("id",$id)->first();
        if (!$provider) abort(404);
        $iconMap = ["elec"=>"⚡","gas"=>"🔥","water"=>"💧","net"=>"🌐","bank"=>"🏦"];
        $provider->icon_html = $iconMap[$provider->icon] ?? "📄";
        $dateFrom = $req->get("from");
        $dateTo   = $req->get("to");
        $yearFilter   = $req->get("year");
        $typeFilter   = $req->get("type","all");
        $statusFilter = $req->get("status","all");
        if ($yearFilter && !$dateFrom && !$dateTo) { $dateFrom = "$yearFilter-01-01"; $dateTo = "$yearFilter-12-31"; }
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
        $availYears = DB::table("bills")->where("provider_id",$id)->selectRaw("YEAR(issue_date) as yr")->groupBy("yr")->orderBy("yr")->pluck("yr");
        $forecast = $this->computeForecast($monthly);
        $documents = $this->listDocuments($id);
        return view("provider.show", compact("provider","bills","monthly","costPerKwh","yoy","years","stats","lastBill","dateFrom","dateTo","yearFilter","typeFilter","statusFilter","docTypes","statuses","availYears","forecast","documents"));
    }
    private function computeForecast($monthly) {
        $data = $monthly->filter(fn($r)=>$r->kwh>0)->values();
        if ($data->count() < 3) return [];
        $n = $data->count(); $sx=0; $sy=0; $sxy=0; $sx2=0;
        foreach ($data as $i=>$r) { $sx+=$i; $sy+=$r->total; $sxy+=$i*$r->total; $sx2+=$i*$i; }
        $denom = $n*$sx2 - $sx*$sx;
        if ($denom == 0) return [];
        $m = ($n*$sxy - $sx*$sy) / $denom;
        $b = ($sy - $m*$sx) / $n;
        $lastRow = $data->last();
        $yr = (int)$lastRow->yr; $mo = (int)$lastRow->mo;
        $result = [];
        for ($i=1; $i<=6; $i++) {
            $mo++; if ($mo>12) { $mo=1; $yr++; }
            $predicted = max(0, round($m*($n-1+$i) + $b, 2));
            $result[] = ["label"=>sprintf("%04d-%02d",$yr,$mo),"val"=>$predicted];
        }
        return $result;
    }
    private function listDocuments($providerId) {
        $dir = storage_path("app/public/documents/$providerId");
        if (!is_dir($dir)) return [];
        $files = scandir($dir);
        return collect($files)->filter(fn($f)=>$f!="."&&$f!="..")->map(fn($f)=>["name"=>$f,"url"=>"/storage/documents/$providerId/$f","ext"=>strtolower(pathinfo($f,PATHINFO_EXTENSION))])->values()->toArray();
    }
    public function updateQuick($id, Request $req) {
        $data = $req->only(["name","icon","color"]);
        if (!empty($data)) DB::table("energy_providers")->where("id",$id)->update($data);
        return response()->json(["ok"=>true,"data"=>DB::table("energy_providers")->where("id",$id)->first()]);
    }
}
