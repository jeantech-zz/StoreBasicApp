<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $user_id, $customer_name, $customer_email, $customer_mobile, $product_id, $status, $price;
    public $user_name, $product_name, $url_payment, $buttonUrlPay, $disabled;
    public $updateMode = false;

    public function render()
    {
       
        $userId=auth()->id();

		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.orders.view', [
            'orders' => Order::select('orders.*', 'users.name As user_name','products.name As product_name')
                        ->join('products', 'products.id', '=', 'orders.product_id')   
                        ->join('users', 'users.id', '=', 'orders.user_id')   
                        ->where ('orders.user_id',  $userId)            
						->where('orders.customer_name', 'LIKE', $keyWord)
						->where('orders.customer_email', 'LIKE', $keyWord)
						->where('orders.customer_mobile', 'LIKE', $keyWord)
						->where('orders.product_id', 'LIKE', $keyWord)
                        ->where('orders.price', 'LIKE', $keyWord)
						->where('orders.status', 'LIKE', $keyWord)
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
		$this->user_id = null;
		$this->customer_name = null;
		$this->customer_email = null;
		$this->customer_mobile = null;
		$this->product_id = null;
        $this->price = null;
		$this->status = null;
    }

    public function store()
    {
        $this->validate([
		'customer_name' => 'required',
		'customer_email' => 'required',
		'customer_mobile' => 'required',
		'status' => 'required',
        'price' => 'required',
        ]);

        Order::create([ 
			'user_id' => $this-> user_id,
			'customer_name' => $this-> customer_name,
			'customer_email' => $this-> customer_email,
			'customer_mobile' => $this-> customer_mobile,
			'product_id' => $this-> product_id,
            'price' => $this-> product_id,
			'status' => $this-> status
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Order Successfully created.');
    }

    public function edit($id)
    {
        $record = Order::findOrFail($id);

        $this->selected_id = $id; 
		$this->user_id = $record-> user_id;
		$this->customer_name = $record-> customer_name;
		$this->customer_email = $record-> customer_email;
		$this->customer_mobile = $record-> customer_mobile;
		$this->product_id = $record-> product_id;
        $this->price = $record-> price;
		$this->status = $record-> status;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'customer_name' => 'required',
		'customer_email' => 'required',
		'customer_mobile' => 'required',
        'price' => 'required',
		'status' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Order::find($this->selected_id);
            $record->update([ 
			'user_id' => $this-> user_id,
			'customer_name' => $this-> customer_name,
			'customer_email' => $this-> customer_email,
			'customer_mobile' => $this-> customer_mobile,
			'product_id' => $this-> product_id,
            'price' => $this-> price,
			'status' => $this-> status
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Order Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Order::where('id', $id);
            $record->delete();
        }
    }


    public function payrmentOrder($id)
    {
        $buttonUrlPay=true;

        $record = Order::select('orders.*', 'users.name As user_name','products.name As product_name' ,'requests.url_payment As url_payment')
        ->join('requests', 'requests.order_id', '=', 'orders.id') 
        ->join('products', 'products.id', '=', 'orders.product_id')   
        ->join('users', 'users.id', '=', 'orders.user_id')         
        ->where('orders.id',$id)
        ->first();

        $statusOrder=$record->status;

        $serviceOrder=$this->getRequestInformationPayment($id);
        $statusServiceOrder= $serviceOrder['status']['status'];

        if( $statusOrder<>$statusServiceOrder){
            $orderUpdate= Order::find($id);
            $orderUpdate->update([ 
			'status' => $statusServiceOrder
            ]);
        }

        if($statusServiceOrder=="APPROVED"){
            $buttonUrlPay=false;
        }

        $this->selected_id = $id; 
		$this->user_id = $record-> user_id;
        $this->user_name = $record-> user_name;
		$this->customer_name = $record-> customer_name;
		$this->customer_email = $record-> customer_email;
		$this->customer_mobile = $record-> customer_mobile;
		$this->product_id = $record-> product_id;
        $this->product_name = $record-> product_name;
        $this->price = $record-> price;
		$this->status =  $statusServiceOrder;
        $this->url_payment = $record-> url_payment;
        $this->buttonUrlPay = $buttonUrlPay;
        $this->disabled=true;
        
		
        $this->updateMode = true;
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

    public function getRequestInformationPayment($idOrder){

        $order = Order::select('orders.*', 'requests.*')
        ->join('requests', 'requests.order_id', '=', 'orders.id') 
        ->where('orders.id',$idOrder)
        ->first();

        $requestId=$order->requestId;
        $reference=$order->reference;
        $total=$order->price;
        $expiration=$order->expiration;
        
        $login= config('app.loginPay');//"6dd490faf9cb87a9862245da41170ff2";
        $secretKey=config('app.secretKeyPay');//"024h1IlD";
        $returnUrl=config('app.returnUrl');
        $ipAddress=config('app.ipAddress');

        $dataConexion= $this->generate($login,$secretKey);

       $url='https://dev.placetopay.com/redirection/api/session/'.$requestId;
        $response = Http::post($url, [
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

    
    
}

