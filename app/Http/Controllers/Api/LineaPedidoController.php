<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LineaPedido;

class LineaPedidoController extends Controller
{
    public function index($id_pedido){
        $lineaspedido = LineaPedido::where('id_pedido', $id_pedido)->get();
        return $lineaspedido;
    }

    public function show($id){
        $lineaspedido = LineaPedido::find($id);
        return $lineaspedido;
    }

    public function update(Request $request, $id){
        $lineaspedido = LineaPedido::findOrFail($id);
        $lineaspedido->id_pedido = $request->id_pedido;
        $lineaspedido->id_articulo = $request->id_articulo;
        $lineaspedido->cantidad = $request->cantidad;

        $lineaspedido->save();
        return $lineaspedido;
    }

    public function store(Request $request){
        $lineaspedido = new LineaPedido();
        $lineaspedido->id_pedido = $request->id_pedido;
        $lineaspedido->id_articulo = $request->id_articulo;
        $lineaspedido->cantidad = $request->cantidad;

        $lineaspedido->save();
    }
}
