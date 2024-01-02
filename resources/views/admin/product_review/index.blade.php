@extends('admin.layouts.master')
@section('content')
@section('title','Product_review')
<div class='card mt-3'>
	<div class='card-body'>
		<div class='table-responsive'>
			<table class='table align-middle mb-0'>
				<thead class='table-light'>
					<tr>
						<th scope='col'>@lang('ID')</th>
						<th scope='col'>@lang('Customer')</th>
						<th scope='col'>@lang('Product')</th>
						<th scope='col'>@lang('review')</th>
						<th scope='col'>@lang('rating')</th>
						<th scope='col'>@lang('Status')</th>
						<th scope='col'>@lang('Action')</th>
					</tr>
				</thead>
							
				<tbody>
					@forelse($product_reviews as $key => $product_review)
						<tr>
							<td scope='col'>{{ ++$key }}</td>
								<td scope='col'>{{__($product_review->customer->first_name)}}</td>
							<td scope='col'>{{__($product_review->product->name)}}</td>
						<td scope='col'>{{__($product_review->review)}}</td>
						<td scope='col'>{{__($product_review->rating)}}</td>
						<td scope='col'>
								@if($product_review->status == 1)
									<span class='badge badge-pill bg-gradient-success'>@lang('Enabled')</span>
								 @else
									<span class='badge badge-pill bg-gradient-danger'>@lang('Disable')</span>
								@endif
							</td>
							<td>
								<div class="align-items-center gap-3 fs-6">
									<button class="btn bg-gradient-info btn-sm icon-btn ml-1 editBtn"
										data-bs-toggle="modal"
										data-bs-target="#editModal"
										data-id="{{ __($product_review->id)}}"
										data-action="{{ route('admin.product_review.update',$product_review->id) }}"
										data-customer="{{ __($product_review->customer_id)}}"
										data-product="{{ __($product_review->product_id)}}"
										data-review="{{ __($product_review->review)}}"
										data-rating="{{ __($product_review->rating)}}">
										<i class="la la-pencil"></i>
									</button>
									@if($product_review->status == 1)
									<button class="btn bg-gradient-danger btn-sm icon-btn ml-1 disableBtn"
										data-bs-toggle="modal"
										data-bs-target="#disableModal"
										data-id="{{ __($product_review->id)}}"
										>
										<i class="la la-eye-slash"></i>
									</button>
									@else
									<button class="btn bg-gradient-success btn-sm icon-btn ml-1 enableBtn"
										data-bs-toggle="modal"
										data-bs-target="#enableModal"
										data-id="{{ __($product_review->id)}}"
										>
										<i class="la la-eye"></i>
									</button>
									@endif
								</div>
							</td>
						</tr>
						@empty
							<tr>
								<td class='text-muted text-center' colspan='100%'>@lang('Data Not Found')</td>
							</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>
@if($product_reviews->hasPages())
	<div class='card-footer'>
		{{ $product_reviews->links() }}
	</div>
@endif


{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog modal-lg">"
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="createModalLabel"> @lang('Add New')</h4>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class='col-md-12'>
							<div class='form-row form-group mt-3'>
								<label for="customer01" class='font-weight-bold form-label'>@lang('Customer')<span class='text-danger'>*</span></label>
								<div class='col-md-12'>
									<select class="form-control " name="customer" id="customer01" required>
										<option selected value="">@lang('Select One')</option>
											@foreach($customers as $customer)
												<option value="{{ $customer->id }}">{{__($customer->first_name) }}</option>
											@endforeach
									</select>
								</div>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="product01" class='font-weight-bold form-label'>@lang('Product')<span class='text-danger'>*</span></label>
								<div class='col-md-12'>
									<select class="form-control " name="product" id="product01" required>
										<option selected value="">@lang('Select One')</option>
											@foreach($products as $product)
												<option value="{{ $product->id }}">{{__($product->name) }}</option>
											@endforeach
									</select>
								</div>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="review01" class='font-weight-bold form-label'>@lang('Review')</label><span class='text-danger'>*</span>
								<input type="text" class="form-control" name="review" id="review01" required>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="rating01" class='font-weight-bold form-label'>@lang('Rating')</label><span class='text-danger'>*</span>
								<input type="text" class="form-control" name="rating" id="rating01" required>
							</div>
						</div>
					</div>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-dark' data-bs-dismiss='modal'>@lang('Close')</button>
					<button type='submit' class='btn btn-primary' id='btn-save' value='add'>@lang('Save')</button>
				</div>
			</form>
		</div>
	</div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog modal-lg">"
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editModalLabel"> @lang('Add New')</h4>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class='col-md-12'>
							<div class='form-row form-group mt-3'>
								<label for="customer01" class='font-weight-bold form-label'>@lang('Customer')<span class='text-danger'>*</span></label>
								<div class='col-md-12'>
									<select class="form-control " name="customer" id="customer01" required>
										<option selected value="">@lang('Select One')</option>
											@foreach($customers as $customer)
												<option value="{{ $customer->id }}">{{__($customer->first_name) }}</option>
											@endforeach
									</select>
								</div>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="product01" class='font-weight-bold form-label'>@lang('Product')<span class='text-danger'>*</span></label>
								<div class='col-md-12'>
									<select class="form-control " name="product" id="product01" required>
										<option selected value="">@lang('Select One')</option>
											@foreach($products as $product)
												<option value="{{ $product->id }}">{{__($product->name) }}</option>
											@endforeach
									</select>
								</div>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="review01" class='font-weight-bold form-label'>@lang('Review')</label><span class='text-danger'>*</span>
								<input type="text" class="form-control" name="review" id="review01" required>
							</div>
							<div class='form-row form-group mt-3'>
								<label for="rating01" class='font-weight-bold form-label'>@lang('Rating')</label><span class='text-danger'>*</span>
								<input type="text" class="form-control" name="rating" id="rating01" required>
							</div>
						</div>
					</div>
				</div>
				<div class='modal-footer'>
					<button type='button' class='btn btn-dark' data-bs-dismiss='modal'>@lang('Close')</button>
					<button type='submit' class='btn btn-primary' id='btn-save' value='add'>@lang('Save')</button>
				</div>
			</form>
		</div>
	</div>
</div>

{{-- Enable --}}
<div id="enableModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">@lang('Product_review Enable Confirmation')</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('admin.product_review.enable') }}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal-body">
					<p>@lang('Are you sure to Enable this product_review?')</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('No')</button>
					<button type="submit" class="btn btn-primary">@lang('Yes')</button>
				</div>
			</form>
		</div>
	</div>
</div>

{{-- Disable --}}
<div id="disableModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">@lang('Product_review Disable Confirmation')</h5>
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('admin.product_review.disable') }}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal-body">
					<p>@lang('Are you sure to disable this product_review?')</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-dark" data-bs-dismiss="modal">@lang('No')</button>
					<button type="submit" class="btn btn-primary">@lang('Yes')</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('top-area')
	<button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#createModal'><i class='lab la-telegram-plane'></i> @lang('Add Product_review')</button>
@endpush
@push('js')
	<script>
		(function($){
			"use strict";
			
			$('.editBtn').on('click',function(){
				var editModal = $('#editModal');			
				editModal.find('select[name=customer]').val($(this).data('customer'));
				editModal.find('select[name=product]').val($(this).data('product'));
				editModal.find('input[name=review]').val($(this).data('review'));
			
				editModal.find('input[name=rating]').val($(this).data('rating'));
				editModal.find('form').attr('action',$(this).data('action'));
			});
			$('.enableBtn').on('click', function () {
				var modal = $('#enableModal');
				modal.find('input[name=id]').val($(this).data('id'));
			});
			$('.disableBtn').on('click', function () {
				var modal = $('#disableModal');
				modal.find('input[name=id]').val($(this).data('id'));
			});
		})(jQuery);
	</script>
@endpush