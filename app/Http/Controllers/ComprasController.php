<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComprasRequest;
use App\Http\Requests\UpdateComprasRequest;
use Illuminate\Http\Request;
use App\Models\Compras;
use App\Models\Articulo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    //función para el get
    public function index(){
        return [];
    }

    //función para post
    public function store(Request $request){
        if(!$request->header('Authorization')){
            return abort(401, 'Debe proveer un Token ');
        }

        $tokenID = $request->idUsuario;
        $sql = "SELECT au.id, am.cantidad, am.descuento FROM api_usuario au
            LEFT JOIN api_monedero am 
            ON au.id = am.usuario_id 
            WHERE au.id = $tokenID;";

        $user = DB::select($sql);

        if(count($user) !== 1){
            return abort(401, 'El usuario no existe');
        }

        $applyDiscount = ($user[0]->descuento !== 0);
        $precio = $applyDiscount
            ?($request->precioTotal * $user[0]->descuento)/100
            :$request->precioTotal;
        $dineroTotal = $user[0]->cantidad - $precio;

        if($dineroTotal < 0){
            return abort(500, 'Dinero insuficiente para realizar la compra');
        }

        $newCompra = new Compras;
        $newCompra->idUsuario = $tokenID;
        $newCompra->precioTotal = $request->precioTotal;
        $newCompra->fechaPedido = $request->fechaPedido;
        $newCompra->fechaEntrega = $request->fechaEntrega;
        $newCompra->save();

        foreach ($request->articulos as $articulo) {
            $newArticulo = new Articulo;
            $newArticulo->precio = $articulo['precio'];
            $newArticulo->nombre = $articulo['nombre'];
            $newArticulo->cantidad = $articulo['cantidad'];
            $newArticulo->compra = $newCompra->id;
            $newArticulo->save();
        }

        $payment = "UPDATE api_monedero SET cantidad=$dineroTotal, descuento=0 WHERE usuario_id = $tokenID;";
        DB::update($payment);

        $articulos = DB::table('articulos')->where('compra', '=', $newCompra->id)->get();
        $compra = array(
            "id"=>$newCompra->id,
            "idUsuario"=>$newCompra->idUsuario,
            "precioTotal"=>$newCompra->precioTotal,
            "fechaPedido"=>$newCompra->fechaPedido,
            "fechaEntrega"=>$newCompra->fechaEntrega,
            "articulos"=>$articulos,
            "descuento"=> $applyDiscount
        );
        return $compra;
    }


    //función para get con parámetros
    public function show($id, Request $request){
        if(!$request->header('Authorization')){
            return abort(401, 'Debe proveer un Token ');
        }

        $sql = "SELECT id, name, username FROM api_usuario 
            WHERE id = $id;";

        $user = DB::select($sql);

        if(count($user) !== 1){
            return abort(401, 'El usuario no existe');
        }

        $compras = DB::table('compras')->where('idUsuario', '=', $id)->get();
        $response = array();

        foreach ($compras as $cmp) {
            $articulos = DB::table('articulos')->where('compra', '=', $cmp->id)->get();
            $compra = array(
                "id"=>$cmp->id,
                "idUsuario"=>$cmp->idUsuario,
                "precioTotal"=>$cmp->precioTotal,
                "fechaPedido"=>$cmp->fechaPedido,
                "fechaEntrega"=>$cmp->fechaEntrega,
                "articulos"=>$articulos
            );
            array_push($response, $compra);
        }

        return $response;
    }

    

    public function edit(Compras $compras)
    {
        //
    }

   
    public function update(UpdateComprasRequest $request, Compras $compras)
    {
        //
    }

    
    public function destroy(Compras $compras)
    {
        //
    }
}
