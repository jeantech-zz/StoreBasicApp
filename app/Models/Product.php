<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'products';

    protected $fillable = ['name','description','price','image'];
    //protected $fillable = ['name','description','price','image','user_id','customer_name','customer_email','customer_mobile','product_id','status'];
    
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'product_id', 'id');
    }
    
}
