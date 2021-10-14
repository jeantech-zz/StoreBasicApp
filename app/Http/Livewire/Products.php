<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class Products extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    //public $selected_id, $keyWord, $name, $description, $price, $image;
    public $selected_id, $keyWord, $name, $description, $price, $image, $user_id, $user_name, $customer_name, $customer_email, $customer_mobile, $product_id,$product_name, $status;
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

    public function storeOrder()
    {
        $this->validate([
		'customer_name' => 'required',
		'customer_email' => 'required',
		'customer_mobile' => 'required',
        'price' => 'required',
		'status' => 'required',
        ]);

        Order::create([ 
			'user_id' => $this-> user_id,
			'customer_name' => $this-> customer_name,
			'customer_email' => $this-> customer_email,
			'customer_mobile' => $this-> customer_mobile,
			'product_id' => $this-> product_id,
            'price' => $this-> price,
			'status' => $this-> status
        ]);
        
        $this->resetInputOrder();
		$this->emit('closeModal');
		session()->flash('message', 'Order Successfully created.');
    }

}
