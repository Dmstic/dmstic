<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SettingsController extends Controller {
    public function index() {
        $settings = DB::table("settings")->pluck("v","k")->toArray();
        return view("settings", compact("settings"));
    }
    public function update(Request $req) {
        foreach (["app_name","color_scheme","ai_api_key"] as $k) {
            if ($req->has($k)) DB::table("settings")->updateOrInsert(["k"=>$k],["v"=>$req->get($k),"updated_at"=>now()]);
        }
        return back()->with("success","Ustawienia zapisane.");
    }
}
