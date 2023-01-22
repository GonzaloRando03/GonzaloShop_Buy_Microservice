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
        $newCompra = new Compras;
        $newCompra->idUsuario = $request->idUsuario;
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

        $articulos = DB::table('articulos')->where('compra', '=', $newCompra->id)->get();
        $compra = array(
            "id"=>$newCompra->id,
            "idUsuario"=>$newCompra->idUsuario,
            "precioTotal"=>$newCompra->precioTotal,
            "fechaPedido"=>$newCompra->fechaPedido,
            "fechaEntrega"=>$newCompra->fechaEntrega,
            "articulos"=>$articulos
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
