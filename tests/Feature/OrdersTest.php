<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Product;
use Tests\Feature\RouteServiceProvider;



class OrdersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testPayrmentOrder(){
        $login= config('app.loginPay');//"6dd490faf9cb87a9862245da41170ff2";
        $secretKey=config('app.secretKeyPay');//"024h1IlD";
        $returnUrl=config('app.returnUrl');
        $ipAddress=config('app.ipAddress');
        $now = date("YmdHms");
        $expiration=date("c",strtotime($now."+ 1 days"));
        $tranKey=$secretKey;

        $nonce = random_bytes(16);
        $seed = date('c');
        $digest = base64_encode(hash('sha256', $nonce . $seed . $tranKey, true));
        $dataConexion = [
            'login' => $login,
            'tranKey' => $digest,
            'nonce' => base64_encode($nonce),
            'seed' => $seed,
        ];
    
        $response = $this->call('POST','https://dev.placetopay.com/redirection/api/session',
                        [
                            "auth" => $dataConexion,
                            "payment" => [
                                            "reference" =>  "58",
                                            "description" => "Pago básico Test",
                                            "amount"=> [
                                                "currency"=> "COP",
                                                "total"=> "1000"
                                                        ],
                                            ],
                            "expiration"=>  $expiration, 
                            "returnUrl"=>   $returnUrl,
                            "ipAddress"=>   $ipAddress,
                            "userAgent"=>   "PlacetoPay Sandbox"
                        ]
        );

    $this->assertEquals("200", $response->status());
    }

    public function testCreateOrder(){
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->post('/store', [
            'user_id' => $user->id,
			'customer_name' => $user->name,
			'customer_email' => $user->email,
			'customer_mobile' => "77777",
			'product_id' =>  $product->id,
            'price' => $product->price,
			'status' => "CREATED"
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
