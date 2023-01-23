<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComprasRequest;
use App\Http\Requests\UpdateComprasRequest;
use Illuminate\Http\Request;
use App\Models\Compras;
use App\Models\Articulo;
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
        $sql = "SELECT au.id, am.cantidad, am.descuento from api_usuario au
            left JOIN api_monedero am 
            on au.id = am.usuario_id 
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Compras  $compras
     * @return \Illuminate\Http\Response
     */
    public function show(Compras $compras)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Compras  $compras
     * @return \Illuminate\Http\Response
     */
    public function edit(Compras $compras)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateComprasRequest  $request
     * @param  \App\Models\Compras  $compras
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateComprasRequest $request, Compras $compras)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Compras  $compras
     * @return \Illuminate\Http\Response
     */
    public function destroy(Compras $compras)
    {
        //
    }
}
