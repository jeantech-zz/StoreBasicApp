<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* $product= new Product();
        $product->name="Termo";
        $product->description="Termo metalico moderno";
        $product->price=20000;
        $product->image="termo.jpg";
        $product->save();*/

        Product::create([
            "name"=>"Termo",
            "description"=>"Termo metalico moderno",
            "price"=>20000,
            "image"=>"termo.jpg",
        ]);
    }
}
