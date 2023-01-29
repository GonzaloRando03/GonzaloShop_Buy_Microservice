<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ComprasTest extends TestCase{

    public function test_crate_compra(){
        $articulosPedido = array();
        $articulo1 = array(
            "precio" => 10,
            "nombre" => "articulo de prueba",
            "cantidad" => 1
        );

        array_push($articulosPedido, $articulo1);

        $response = $this->post("/api/compras", [
            "idUsuario" => 1,
            "precioTotal" => 10,
            "fechaPedido" => "2023-01-01",
            "fechaEntrega" => "2023-01-20",
            "articulos" => $articulosPedido
        ],['HTTP_Authorization' => "tokenCorrecto"]);

        $response->assertStatus(200);
    }

    public function test_crate_compra_token_error(){
        $articulosPedido = array();
        $articulo1 = array(
            "precio" => 10,
            "nombre" => "articulo de prueba",
            "cantidad" => 1
        );

        array_push($articulosPedido, $articulo1);

        $response = $this->post("/api/compras", [
            "idUsuario" => 1,
            "precioTotal" => 10,
            "fechaPedido" => "2023-01-01",
            "fechaEntrega" => "2023-01-20",
            "articulos" => $articulosPedido
        ]);

        $response->assertStatus(401);
    }

    public function test_crate_compra_user_error(){
        $articulosPedido = array();
        $articulo1 = array(
            "precio" => 10,
            "nombre" => "articulo de prueba",
            "cantidad" => 1
        );

        array_push($articulosPedido, $articulo1);

        $response = $this->post("/api/compras", [
            "idUsuario" => 1523,
            "precioTotal" => 10,
            "fechaPedido" => "2023-01-01",
            "fechaEntrega" => "2023-01-20",
            "articulos" => $articulosPedido
        ],['HTTP_Authorization' => "tokenCorrecto"]);

        $response->assertStatus(401);
    }

    public function test_get_compras(){
        $response = $this->get(
            "/api/compras/1",
            ['HTTP_Authorization' => "tokenCorrecto"]
        );

        $response->assertStatus(200);
    }

    public function test_get_compras_token_error(){
        $response = $this->get(
            "/api/compras/1"
        );

        $response->assertStatus(401);
    }

    public function test_get_compras_user_error(){
        $response = $this->get(
            "/api/compras/1234",
            ['HTTP_Authorization' => "tokenCorrecto"]
        );

        $response->assertStatus(401);
    }
}
