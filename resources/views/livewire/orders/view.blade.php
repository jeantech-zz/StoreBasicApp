@section('title', __('Orders'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="fab fa-laravel text-info"></i>
							Order Listing </h4>
						</div>
						
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Orders">
						</div>
						
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.orders.create')
						@include('livewire.orders.update')
						@include('livewire.orders.paymentOrder')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>User Id</th>
								<th>Customer Name</th>
								<th>Customer Email</th>
								<th>Customer Mobile</th>
								<th>Product Name</th>
								<th>Status</th>
								<th>ACTIONS</th>								
							</tr>
						</thead>
						<tbody>
							@foreach($orders as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->user_name }}</td>
								<td>{{ $row->customer_name }}</td>
								<td>{{ $row->customer_email }}</td>
								<td>{{ $row->customer_mobile }}</td>
								<td>{{ $row->product_name }}</td>
								<td>{{ $row->status }}</td>
								<td width="90">
								@if (Auth::check())
									@if (Auth::user()->isAdmin()) 
										<div class="btn-group">
											<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Actions
											</button>
											<div class="dropdown-menu dropdown-menu-right">
											<a data-toggle="modal" data-target="#updateModal" class="dropdown-item" wire:click="edit({{$row->id}})"><i class="fa fa-edit"></i> Edit </a>							 
											<a class="dropdown-item" onclick="confirm('Confirm Delete Product id {{$row->id}}? \nDeleted Order cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id}})"><i class="fa fa-trash"></i> Delete </a>   
											</div>
										</div>
									@else
										<div class="btn-group">
											<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Actions
											</button>
											<div class="dropdown-menu dropdown-menu-right">
											<a data-toggle="modal" data-target="#OrderPaymentModal" class="dropdown-item" wire:click="payrmentOrder({{$row->id}})"><i class="fa fa-edit"></i> Payment Order  </a>							 																		 																																
										</div>
										</div>
									@endif
								@endif
								</td>	
							@endforeach
						</tbody>
					</table>						
					{{ $orders->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>