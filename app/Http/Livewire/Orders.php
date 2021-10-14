<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\User;

class Orders extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $user_id, $customer_name, $customer_email, $customer_mobile, $product_id, $status, $price;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.orders.view', [
            'orders' => Order::latest()
						->orWhere('user_id', 'LIKE', $keyWord)
						->orWhere('customer_name', 'LIKE', $keyWord)
						->orWhere('customer_email', 'LIKE', $keyWord)
						->orWhere('customer_mobile', 'LIKE', $keyWord)
						->orWhere('product_id', 'LIKE', $keyWord)
                        ->orWhere('price', 'LIKE', $keyWord)
						->orWhere('status', 'LIKE', $keyWord)
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

    
}
