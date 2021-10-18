<!-- Modal -->
<div wire:ignore.self class="modal fade" id="OrderPaymentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="OrderPaymentModalLabel">Payment Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span wire:click.prevent="cancel()" aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
					<input type="hidden" wire:model="selected_id">
            <div class="form-group">
                <label for="user_name"></label>
                <input wire:model="user_name" type="text" class="form-control" id="user_name" placeholder="User Name">@error('user_id') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <input type="hidden" wire:model="user_id">
            <div class="form-group">
                <label for="customer_name"></label>
                <input wire:model="customer_name" type="text" class="form-control" id="customer_name" placeholder="Customer Name">@error('customer_name') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="customer_email"></label>
                <input wire:model="customer_email" type="text" class="form-control" id="customer_email" placeholder="Customer Email">@error('customer_email') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="customer_mobile"></label>
                <input wire:model="customer_mobile" type="text" class="form-control" id="customer_mobile" placeholder="Customer Mobile">@error('customer_mobile') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="product_name"></label>
                <input wire:model="product_name" type="text" class="form-control" id="product_name" placeholder="Product Id">@error('product_id') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <input type="hidden" wire:model="product_id">
            <div class="form-group">
                <label for="status"></label>
                <input wire:model="status" type="text" class="form-control" id="status" placeholder="Status">@error('status') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="price"></label>
                <input wire:model="price" type="text" class="form-control" id="price" placeholder="Status">@error('price') <span class="error text-danger">{{ $message }}</span> @enderror
            </div>            
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click.prevent="cancel()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
                @if ($buttonUrlPay) 
                    <button type="button" onclick="window.location.href = '{{ $url_payment }}';" class="btn btn-primary" data-dismiss="modal">Pay</button>                
                @endif
            </div>
       </div>
    </div>
</div>