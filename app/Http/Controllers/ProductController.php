<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\MathTrait;

class ProductController extends Controller
{
    use MathTrait;

    /*
    |--------------------------------------------------------------------------
    | TEST ADD FUNCTION (FROM TRAIT)
    |--------------------------------------------------------------------------
    */
    public function testAdd()
    {
        $a = 10;
        $b = 5;

        $hasil = $this->add($a, $b);

        return response()->json([
            'status' => true,
            'message' => 'Hasil penjumlahan berhasil dihitung',
            'data' => [
                'angka_pertama' => $a,
                'angka_kedua' => $b,
                'hasil' => $hasil
            ]
        ]);
    }
}