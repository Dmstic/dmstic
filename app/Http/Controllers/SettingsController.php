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
        $keys = ["app_name","color_scheme","ai_api_key","private_repo_token","accent_color","font_size","sidebar_width","border_radius"];
        foreach ($keys as $key) {
            if ($req->has($key)) DB::table("settings")->updateOrInsert(["k"=>$key],["v"=>$req->input($key),"updated_at"=>now()]);
        }
        return redirect("/settings")->with("success","Ustawienia zapisane.");
    }
}
