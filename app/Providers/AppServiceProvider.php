<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
        View::composer("*", function($view) {
            try {
                $providers = DB::table("energy_providers")->orderBy("id")->get();
                $settings = DB::table("settings")->pluck("v","k")->toArray();
            } catch (\Exception $e) { $providers = collect(); $settings = []; }
            $view->with("sidebarProviders", $providers)->with("appSettings", $settings);
        });
    }
}
