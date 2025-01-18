<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticuloController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\LineaPedidoController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ArticuloController::class)->group(function (){
    Route::get('/articulos', 'index');
    Route::post('/articulo', 'store');
    Route::get('/articulo/{id}', 'show');
    Route::put('/articulo/{id}', 'update');
    Route::delete('/articulo/{id}', 'destroy');
    Route::get('/categorias','categories');
    Route::get('/ccategorias','countCategories');
    Route::get('/masvalorados','topRated');
    Route::get('/search/{busq}','search');
    Route::get('/cprecios','priceCounters');
    Route::get('/cimpresoras','printerCounters');
    Route::get('/cmateriales','matCounters');
    Route::get('/caccesorios','accCounters');
    Route::get('/filter/{p}/{i}/{a}/{m}','filtrado');
    Route::get('/sugeridos','sugeridos');
    Route::get('/lista/{listaIDs}','listaArticulos');
    Route::get('/articulospedido/{id_pedido}','articulosPorPedido');
    Route::get('/preciospedido/{id_pedido}','preciosPedido');
});

Route::controller(LoginController::class)->group(function (){
    Route::post('/login', 'authenticate');
    Route::post('/logout', 'logout');
});

Route::controller(RegisterController::class)->group(function(){
    Route::post('/register', 'create');
    Route::post('/registeradmin', 'createAdmin');
});

Route::controller(UserController::class)->group(function(){
    Route::get('/usuarios', 'index');
    Route::get('/usuario/{id}', 'show');
    Route::put('/usuario/{id}', 'update');
    Route::delete('/usuario/{id}', 'destroy');
});

Route::controller(PedidoController::class)->group(function(){
    Route::get('/pedidos', 'index');
    Route::get('/pedidoscliente/{id}', 'indexByCliente');
    Route::post('/pedido', 'store');
    Route::get('/pedido/{id}', 'show');
    Route::get('/pedidoporcliente/{id}', 'showByCliente');
    Route::put('/pedido/{id}', 'update');
    Route::get('/totales', 'totales');
    Route::delete('/pedido/{id}', 'destroy');
    Route::get('/email/{id}', 'sendEmail');
});

Route::controller(LineaPedidoController::class)->group(function(){
    Route::get('/lineaspedido/{id_pedido}', 'index');
    Route::post('/lineapedido', 'store');
    Route::get('/lineapedido/{id}', 'show');
    Route::put('/lineapedido/{id}', 'update');
});