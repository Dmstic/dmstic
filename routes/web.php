<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingsController;
Route::get("/", [DashboardController::class, "index"]);
Route::get("/provider/{id}", [ProviderController::class, "show"])->name("provider.show");
Route::post("/provider/{id}/quick-update", [ProviderController::class, "updateQuick"])->name("provider.quick-update");
Route::get("/provider/{id}/edit", [AdminController::class, "editProvider"])->name("provider.edit");
Route::post("/provider/{id}/edit", [AdminController::class, "updateProvider"]);
Route::delete("/provider/{id}", [AdminController::class, "deleteProvider"])->name("provider.delete");
Route::delete("/provider/{id}/bills", [AdminController::class, "deleteProviderBills"])->name("provider.delete-bills");
Route::get("/admin/provider/create", [AdminController::class, "create"])->name("provider.create");
Route::post("/admin/provider/create", [AdminController::class, "store"]);
Route::post("/admin/upload", [AdminController::class, "upload"])->name("admin.upload");
Route::get("/settings", [SettingsController::class, "index"])->name("settings");
Route::post("/settings", [SettingsController::class, "update"]);
Route::get("/docs", fn() => view("docs"))->name("docs");
