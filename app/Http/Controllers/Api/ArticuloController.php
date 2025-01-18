<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\Pedido;
use App\Models\LineaPedido;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articulos = Articulo::all();
        return $articulos;
    }

    /**
     * Display a listing of the categories.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function categories(){
        $categories = DB::table('articulos')->distinct()->get("categoria");
        $foto1 = DB::table('articulos')->where("categoria","Impresoras 3D")->first()->foto;
        $foto2 = DB::table('articulos')->where("categoria","Materiales")->first()->foto;
        $foto3 = DB::table('articulos')->where("categoria","Accesorios")->first()->foto;
        $fotos = [$foto1,$foto2,$foto3];
        for($i=0;$i<count($categories);$i++){
            $categories[$i]->foto = $fotos[$i];
        }
        return $categories;
    }

    /**
     * Display a count of the categories.
     * 
     * @return array<int>
     */
    public function countCategories(){
        $countImp = DB::table('articulos')->where("categoria","Impresoras 3D")->count("*");
        $countMat = DB::table('articulos')->where("categoria","Materiales")->count("*");
        $countAcc = DB::table('articulos')->where("categoria","Accesorios")->count("*");
        return [$countImp,$countMat,$countAcc];
    }

    /**
     * Display top rated products
     * 
     * @return \Illuminate\Support\Collection
     */
    public function topRated(){
        $articulos = DB::table('articulos')->orderBy('valoracion','desc')->get("*");
        return $articulos;
    }

    /**
     * Search products
     * 
     * @return \Illuminate\Support\Collection
     */
    public function search($busq){
        $search = DB::table('articulos')->where("articulos.nombre","like",'%'.$busq.'%')->get("*");
        return $search;
    }

    /**
     * Return quantity of products in each price range
     * 
     * @return array<int>
     */
    public function priceCounters(){
        $contadores = [            
            DB::table('articulos')->where("articulos.precio",">=","0")->where("articulos.precio","<=","300")->count("*"),
            DB::table('articulos')->where("articulos.precio",">=","300")->where("articulos.precio","<=","600")->count("*"),
            DB::table('articulos')->where("articulos.precio",">=","600")->where("articulos.precio","<=","900")->count("*"),
            DB::table('articulos')->where("articulos.precio",">=","900")->where("articulos.precio","<=","1200")->count("*"),
            DB::table('articulos')->where("articulos.precio",">=","1200")->where("articulos.precio","<=","1500")->count("*"),
            DB::table('articulos')->where("articulos.precio",">=","0")->count("*"),
        ];
        return $contadores;
    }

    /**
     * Return quantity of products for each kind of printer
     * 
     * @return array<int>
     */
    public function printerCounters(){
        $contadores = [            
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","FDM")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Resina")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Arcilla")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Chocolate")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Bioimpresora")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Impresoras 3D")->count("*"),
        ];
        return $contadores;
    }

    /**
     * Return quantity of products for each kind of accesory
     * 
     * @return array<int>
     */
    public function accCounters(){
        $contadores = [            
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Adhesión")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Almacenadores")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Limpieza")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Cubiertas")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Herramientas")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Componentes")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Accesorios")->count("*"),
        ];
        return $contadores;
    }

    /**
     * Return quantity of products for each kind of material
     * 
     * @return array<int>
     */
    public function matCounters(){
        $contadores = [            
            DB::table('articulos')->where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Filamentos")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Resinas")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Postprocesado")->count("*"),
            DB::table('articulos')->where("articulos.categoria","=","Materiales")->count("*"),
        ];
        return $contadores;
    }    

    /**
     * Return a selection of items
     * 
     * @return \Illuminate\Support\Collection
     */
    public function filtrado($precio,$impre,$acce,$mat){
        $queryPrecio = "";
        $queryImpre = "";
        $queryAcce = "";
        $queryMat = "";

        if($precio==0){
            $queryPrecio = 'where("articulos.precio",">=","0")->';
        }else if($precio==1){
            $queryPrecio = 'where("articulos.precio",">=","0")->where("articulos.precio","<=","300")->';
        }else if($precio==2){
            $queryPrecio = 'where("articulos.precio",">=","300")->where("articulos.precio","<=","600")->';
        }else if($precio==3){
            $queryPrecio = 'where("articulos.precio",">=","600")->where("articulos.precio","<=","900")->';
        }else if($precio==4){
            $queryPrecio = 'where("articulos.precio",">=","900")->where("articulos.precio","<=","1200")->';
        }else if($precio==5){
            $queryPrecio = 'where("articulos.precio",">=","1200")->where("articulos.precio","<=","1500")->';
        }

        if($impre==0){
            $queryImpre = '';
        }else if($impre==1){
            $queryImpre = 'where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","FDM")->';
        }else if($impre==2){
            $queryImpre = 'where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Resina")->';
        }else if($impre==3){
            $queryImpre = 'where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Arcilla")->';
        }else if($impre==4){
            $queryImpre = 'where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Chocolate")->';
        }else if($impre==5){
            $queryImpre = 'where("articulos.categoria","=","Impresoras 3D")->where("articulos.tipo","=","Bioimpresora")->';
        }

        if($acce==0){
            $queryAcce = '';
        }else if($acce==1){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Adhesión")->';
        }else if($acce==2){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Almacenadores")->';
        }else if($acce==3){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Limpieza")->';
        }else if($acce==4){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Cubiertas")->';
        }else if($acce==5){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Herramientas")->';
        }else if($acce==6){
            $queryAcce = 'where("articulos.categoria","=","Accesorios")->where("articulos.tipo","=","Componentes")->';
        }

        if($mat==0){
            $queryMat = '';
        }else if($mat==1){
            $queryMat = 'where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Filamentos")->';
        }else if($mat==2){
            $queryMat = 'where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Resinas")->';
        }else if($mat==3){
            $queryMat = 'where("articulos.categoria","=","Materiales")->where("articulos.tipo","=","Postprocesado")->';
        }

        $filtrado = eval("return DB::table('articulos')->".$queryPrecio.$queryImpre.$queryAcce.$queryMat."get('*');");

        return $filtrado;
    }
    
    public function sugeridos(){
        $sugeridos = DB::table('articulos')->inRandomOrder()->get();
        return $sugeridos;
    }

    public function listaArticulos($listaIDs){
        $lista = [];
        foreach(json_decode($listaIDs) as $id){
            $lista[] = $this->show($id);
        }
        return $lista;
    }

    public function articulosPorPedido($id_pedido){
        $lineasPedido = LineaPedido::where('id_pedido',$id_pedido)->get();
        $array = [];
        foreach($lineasPedido as $lineaPedido){
            $articulo = Articulo::where('id',$lineaPedido->id_articulo)->firstOrFail();
            array_push($array,$articulo);
        }
        return $array;
    }

    public function preciosPedido($id_pedido){
        $lineasPedido = LineaPedido::where('id_pedido',$id_pedido)->get();
        $array = [];
        foreach($lineasPedido as $lineaPedido){
            $articulo = Articulo::where('id',$lineaPedido->id_articulo)->firstOrFail();
            array_push($array,$articulo->precio*$lineaPedido->cantidad*1.21);
        }
        return ["subtotal" => array_sum($array), "envio" => array_sum($array)*0.05, "total" => array_sum($array)*1.05];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $articulo = new Articulo();
        $articulo->nombre = $request->nombre;
        $articulo->precio = $request->precio;
        $articulo->valoracion = $request->valoracion;
        $articulo->descripcion = $request->descripcion;
        $articulo->categoria = $request->categoria;
        $articulo->tipo = $request->tipo;
        
        if($request->hasFile('foto')){
            $request->validate([
                'image' => 'mimes:jpeg,bmp,png'
            ]);

            $path = Storage::disk('public')->put('fotos', $request->foto);
            
            $articulo->foto = Storage::url($path);            
        }else{
            $articulo->foto = "";
        }

        $articulo->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articulo = Articulo::find($id);
        return $articulo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($request->id);
        $articulo->nombre = $request->nombre;
        $articulo->precio = $request->precio;
        $articulo->valoracion = $request->valoracion;
        $articulo->descripcion = $request->descripcion;
        $articulo->categoria = $request->categoria;
        $articulo->tipo = $request->tipo;
        
        if($request->hasFile('foto')){
            $request->validate([
                'image' => 'mimes:jpeg,bmp,png'
            ]);

            $path = Storage::disk('public')->put('fotos', $request->foto);
            
            $articulo->foto = Storage::url($path);            
        }else{
            $articulo->foto = "";
        }

        $articulo->save();
        
        return $articulo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $articulo = Articulo::destroy($id);
        return $articulo;
    }
}
