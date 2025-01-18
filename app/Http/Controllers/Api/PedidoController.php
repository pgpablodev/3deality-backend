<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\LineaPedido;
use App\Models\Articulo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Mail;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(){
        $pedidos = Pedido::all();
        return $pedidos;
    }

    public function totales(){
        $pedidos = Pedido::all();
        $totales = [];
        foreach($pedidos as $pedido){
            $lineasPedido = LineaPedido::where('id_pedido',$pedido->id)->get();
            $array = [];
            foreach($lineasPedido as $lineaPedido){
                $articulo = Articulo::where('id',$lineaPedido->id_articulo)->firstOrFail();
                array_push($array,$articulo->precio*$lineaPedido->cantidad*1.21);
            }
            array_push($totales,array_sum($array)*1.05);
        }
        return $totales;
    }

    public function total($id){
        $pedido = Pedido::find($id);
        $lineasPedido = LineaPedido::where('id_pedido',$pedido->id)->get();
        $total = 0;
        foreach($lineasPedido as $lineaPedido){
            $articulo = Articulo::where('id',$lineaPedido->id_articulo)->firstOrFail();
            $total += $articulo->precio*$lineaPedido->cantidad*1.21;
        }
        return number_format($total*1.05,2);
    }

    public function indexByCliente($id){
        return DB::table('pedidos')->where("pedidos.id_cliente","=",$id)->get("*");
    }

    public function show($id){
        $pedidos = Pedido::find($id);
        return $pedidos;
    }

    public function showByCliente($id){
        return DB::table('pedidos')->where("pedidos.id_cliente","=",$id)->latest('id')->first();
    }

    public function update(Request $request, $id){
        $pedido = Pedido::findOrFail($id);
        $pedido->id_cliente = $request->id_cliente;

        $pedido->save();
        return $pedido;
    }

    public function store(Request $request){
        $pedido = new Pedido();
        $pedido->id_cliente = $request->id_cliente;

        $pedido->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pedido = Pedido::destroy($id);
        $lineasPedido = LineaPedido::where('id_pedido',$id)->get();
        foreach($lineasPedido as $lineaPedido){
            $lineaPedido->delete();
        }
        return $pedido;
    }
    
    public function sendEmail($idPedido){

        $pedido = Pedido::find($idPedido);
        $cliente = User::find($pedido->id_cliente);

        $body = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <style>
                body{
                    background-color: #EDF1FF;
                    font-family: Arial, Helvetica, sans-serif;
                }
        
                .encabezamiento{
                    display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    margin-right: 3%;
                }
                .cuerpo{
                    margin-left: 3%;
                    margin-right: 3%;
                }
                .cuadro-detalles{
                    background-color: #C8D4FF;
                    display: flex;
                    flex-direction: row;
                    justify-content: space-around;
                }
                .pie{
                    margin-left: 3%;
                }
                .btn{
                    text-decoration: none;
                    display: inline-block;
                    font-weight: 400;
                    color: white;
                    text-align: center;
                    vertical-align: middle;
                    user-select: none;
                    background-color: #6F6F6F;
                    border: 1px solid transparent;
                    padding: 0.375rem 0.75rem;
                    font-size: 1rem;
                    line-height: 1.5;
                    border-radius: 0;
                    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                }
            </style>
        </head>
        <body>
            <main>
                <div class="encabezamiento">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div>
                        <h1>Confirmación del pedido</h1>
                        <p>Pedido '.$this->formatoFecha($pedido->created_at).' '.$this->formatoHora($pedido->created_at).'</p>
                    </div>
                </div>
                <div class="cuerpo">
                    <h3>Hola '.$cliente->nombre.': </h3>
                    <p>
                        Gracias por tu pedido. La fecha estimada de entrega se indica a continuación. 
                        Visita Mi cuenta en <a href="">3Deality</a> para consultar el estado de tu 
                        pedido y descargar la factura.
                    </p>
                    <div class="cuadro-detalles">
                        <div>
                            <div>
                                <h4>Entrega:</h4>
                                <p>'.$this->fechaEntrega($pedido->created_at).'</p>
                            </div>
                            <div>
                                <h4>Tu opción de envío:</h4>
                                <p>Envío estándar</p>
                            </div>
                            <a href="http://localhost:3001/#/pdfpedido/:'.$idPedido.'" class="btn">
                                Ver los detalles del pedido
                            </a>
                        </div>
                        <div>
                            <div>
                                <h4>Tu pedido se enviará a:</h4>
                                <p>'.$cliente->nombre.' '.$cliente->apellidos.'</p>
                                <p>'.$cliente->provincia.', '.$cliente->ciudad.', '.$cliente->pais.'</p>
                                <p>'.$cliente->cp.', '.$cliente->direccion.'</p>
                            </div>
                            <div>
                                <h4>Importe total del pedido</h4>
                                <p><b>EUR</b> '.$this->total($pedido->id).'</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pie">
                    <p>Esperamos volver a verte pronto,</p>
                    <h4>3Deality</h4>
                </div>
            </main>
        </body>
        </html>';

        Mail::send([], [], function($message) use($body, $pedido, $cliente) {
            $message->from('povarg.pablo@gmail.com');
            $message->to($cliente->email);
            $message->subject('Tu pedido de 3Deality del '.$this->formatoFecha($pedido->created_at).'');
            $message->setBody($body, 'text/html');
        });
    }

    public function formatoFecha($fecha){
        return substr($fecha,8,2)."-".substr($fecha,5,2)."-".substr($fecha,0,4);
    }

    public function formatoHora($fecha){
        return substr($fecha,10,6);
    }

    public function fechaEntrega($fecha){
        $strFecha = substr($fecha,0,4)."-".substr($fecha,5,2)."-".substr($fecha,8,2);
        $fecha = Carbon::parse($strFecha);
        $fechaEntrega = $fecha->addDay(5);
        $strEntrega = $fechaEntrega->toDateString();
        return substr($fechaEntrega,0,4)."-".substr($fechaEntrega,5,2)."-".substr($fechaEntrega,8,2);
    }
}
