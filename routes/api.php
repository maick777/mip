<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//GET PROVINCIAS
/*
Route::get('/provincias/{id}', function ($id) {
    $response = DB::select('SELECT id, provincia FROM provincias WHERE id_departamento = ? ', [$id]);
    return response()->json($response, 200);
});
*/

Route::middleware(['auth:api', 'verify.session'])->group(function () {
});


Route::get('/provincias/{id}', function ($id) {
    $response = DB::select('SELECT id, provincia FROM provincias WHERE id_departamento = ? ', [$id]);
    return response()->json($response, 200);
});
//GET DISTRITOS
Route::get('/distritos/{id}', function ($id) {
    $response = DB::select('SELECT id, distrito FROM distritos WHERE id_provincia = ? ', [$id]);
    return response()->json($response, 200);
});
