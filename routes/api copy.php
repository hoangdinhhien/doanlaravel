<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('categories',[ApiCategoryController::class, 'index']);
// Route::post('categories',[ApiCategoryController::class, 'store']);
// Route::get('categories/{category}',[ApiCategoryController::class, 'show']);
// Route::put('categories/{category}',[ApiCategoryController::class, 'update']);
// Route::delete('categories/{category}',[ApiCategoryController::class, 'destroy']);

Route::post('/tokens/create', function (Request $request) {
    $response = array('response' => '', 'success'=>false);
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
    $response['response'] = $validator->messages();
    } else {
    //process the request
    }
    $user = User::create([
        'name' => 'Admin Manager',
        'email' => 'admin@gmail.com',
        'password' => bcrypt(123456)
    ]);
    $token = $user->createToken(Str::slug($user->name));

    return ['token' => $token];
});

Route::get('profile', function() {
    return ['Data' => ''];
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
