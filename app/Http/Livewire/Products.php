<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Request;
use Illuminate\Support\Facades\Http;


class Products extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    //public $selected_id, $keyWord, $name, $description, $price, $image;
    public $selected_id, $keyWord, $name, $description, $price, $image, $user_id, $user_name, $customer_name, $customer_email, $customer_mobile, $product_id,$product_name, $status;
    public $order_id, $url_app, $url_payment, $respons;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.products.view', [
            'products' => Product::latest()
						->orWhere('name', 'LIKE', $keyWord)
						->orWhere('description', 'LIKE', $keyWord)
						->orWhere('price', 'LIKE', $keyWord)
						->orWhere('image', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->name = null;
		$this->description = null;
		$this->price = null;
		$this->image = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
		'description' => 'required',
		'price' => 'required',
		'image' => 'required',
        ]);

        Product::create([ 
			'name' => $this-> name,
			'description' => $this-> description,
			'price' => $this-> price,
			'image' => $this-> image
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Product Successfully created.');
    }

    public function edit($id)
    {
        $record = Product::findOrFail($id);

        $this->selected_id = $id; 
		$this->name = $record-> name;
		$this->description = $record-> description;
		$this->price = $record-> price;
		$this->image = $record-> image;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
		'description' => 'required',
		'price' => 'required',
		'image' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Product::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name,
			'description' => $this-> description,
			'price' => $this-> price,
			'image' => $this-> image
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Product Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Product::where('id', $id);
            $record->delete();
        }
    }

    public function createOrder($id)
    {
        $userId=auth()->id();
        $datoUser=User::where('id',$userId)->first();
        $datoProduct=Product::where('id',$id)->first();

        $this->selected_id = $id; 
		$this->user_id = $datoUser->id;
        $this->user_name = $datoUser->name;
		$this->customer_name = $datoUser->name;
		$this->customer_email = $datoUser->email;
		$this->product_id = $datoProduct->id;
        $this->product_name = $datoProduct->name;
        $this->price = $datoProduct->price;
		$this->status ='CREATED';

    }

    private function resetInputOrder()
    {		
		$this->user_id = null;
		$this->customer_name = null;
		$this->customer_email = null;
		$this->customer_mobile = null;
        $this->product_id = null;
        $this->price = null;
        $this->status = null;
    }

    public static function generate(string $login, string $tranKey): array
    {
        $nonce = random_bytes(16);
        $seed = date('c');
        $digest = base64_encode(hash('sha256', $nonce . $seed . $tranKey, true));
        return [
            'login' => $login,
            'tranKey' => $digest,
            'nonce' => base64_encode($nonce),
            'seed' => $seed,
        ];
    }

    public function AuthPayment($expiration, $reference, $total ){
        
        $login= config('app.loginPay');//"6dd490faf9cb87a9862245da41170ff2";
        $secretKey=config('app.secretKeyPay');//"024h1IlD";
        $returnUrl=config('app.returnUrl');
        $ipAddress=config('app.ipAddress');

        $dataConexion= $this->generate($login,$secretKey);

        $response = Http::post('https://dev.placetopay.com/redirection/api/session', [
            "auth" => $dataConexion,
            "payment" => [
                            "reference" =>  $reference,
                            "description" => "Pago básico",
                            "amount"=> [
                                "currency"=> "COP",
                                "total"=> $total
                            ],
                            ],
            "expiration"=>  $expiration, 
            "returnUrl"=>   $returnUrl,
            "ipAddress"=>   $ipAddress,
            "userAgent"=>   "PlacetoPay Sandbox"
        ]);

        return $response->json();
    }

    public function createRequestsPayment($orderId, $expiration, $reference, $total ){

        $returnUrl=config('app.returnUrl');
        $response= $this->AuthPayment($expiration, $reference, $total);
        
        $message=$response['status']['message'];

     
        if($response['status']['status']=="OK"){
            $processUrl=$response['processUrl'];
            $requestId=$response['requestId'];

            Request::create([ 
                'order_id' => intval($orderId),
                'url_app'	=> $returnUrl,
                'url_payment'	=> $processUrl,
                'response' => $message,
                'requestId' => $requestId,
                'expiration' => $expiration,
                'reference' => $reference,
            ]);
        }else{
            Request::create([ 
                'order_id' => intval($orderId),
                'url_app'	=> $returnUrl,
                'url_payment'	=> "",
                'response' =>  $message,
                'requestId' => "",
                'expiration' => "",
                'reference' => "",
            ]);
        }
    }

    public function storeOrder()
    {
        $this->validate([
		'customer_name' => 'required',
		'customer_email' => 'required',
		'customer_mobile' => 'required',
        'price' => 'required',
		'status' => 'required',
        ]);

       
       $Order= Order::create([ 
			'user_id' => $this-> user_id,
			'customer_name' => $this-> customer_name,
			'customer_email' => $this-> customer_email,
			'customer_mobile' => $this-> customer_mobile,
			'product_id' => $this-> product_id,
            'price' => $this-> price,
			'status' => $this-> status
        ]);

        $orderId= $Order->id;
        $now = date("YmdHms");
        $expiration=date("c",strtotime($now."+ 1 days"));
        $reference=$orderId.'-'.$now ;
        $total=$this-> price;

        $this->createRequestsPayment($orderId, $expiration, $reference, $total );
        $this->resetInputOrder();
		$this->emit('closeModal');
		session()->flash('message', 'Order Successfully created.');
    }

    
}
