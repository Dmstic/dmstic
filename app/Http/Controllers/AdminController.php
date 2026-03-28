<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller {
    public function create() { return view("admin.create-provider"); }
    public function store(Request $req) {
        $req->validate(["name"=>"required|max:100","type"=>"required"]);
        $iconMap  = ["electricity"=>"elec","gas"=>"gas","water"=>"water","internet"=>"net","bank"=>"bank","other"=>"doc"];
        $colorMap = ["electricity"=>"#63b3ed","gas"=>"#f6ad55","water"=>"#4fd1c5","internet"=>"#68d391","bank"=>"#b794f4","other"=>"#a0aec0"];
        DB::table("energy_providers")->insert(["name"=>$req->name,"type"=>$req->type,"client_number"=>$req->client_number??"","point_number"=>$req->point_number??"","address"=>$req->address??"","scraper_class"=>$req->scraper_class??"","icon"=>$iconMap[$req->type]??"doc","color"=>$colorMap[$req->type]??"#a0aec0"]);
        return redirect("/")->with("success","Dostawca dodany.");
    }
    public function editProvider($id) {
        $provider = DB::table("energy_providers")->where("id",$id)->first();
        if (!$provider) abort(404);
        return view("admin.edit-provider", compact("provider"));
    }
    public function updateProvider($id, Request $req) {
        DB::table("energy_providers")->where("id",$id)->update(["name"=>$req->name,"client_number"=>$req->client_number,"point_number"=>$req->point_number,"address"=>$req->address,"scraper_class"=>$req->scraper_class,"icon"=>$req->icon,"color"=>$req->color]);
        return redirect("/provider/{$id}")->with("success","Zapisano.");
    }
    public function upload(Request $req) {
        $req->validate(["provider_id"=>"required|integer","document"=>"required|file|mimes:pdf,jpg,png|max:10240"]);
        $path = $req->file("document")->store("documents/{$req->provider_id}","public");
        return back()->with("success","Zapisano: $path");
    }
}
