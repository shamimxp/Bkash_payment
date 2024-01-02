@extends($template . 'layouts.master')
@push('css')
    <style>
        .payment-method {
            gap: 10px;
        }

           .payment-method .img-btn>img {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 8px;
            border-radius: 5px;
            background: #EEEEEF;
        }

        .payment-method .img-btn>input:checked+img {
            border-color: #7DA1F2;
            background: transparent;
            position: relative;
        }

        label.img-btn i {
            position: absolute;
            font-size: 10px;
            background: #6AAD00;
            padding: 5px;
            color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            line-height: 8px;
            display: none;
            right: 10px;
            bottom: 9px;
        }

        .payment-method .img-btn>input:checked+img label.img-btn i {
            display: block
        }

        label.img-btn {
            position: relative;
        }

        .payment-method .img-btn>input:checked+img+label.img-btn i {
            display: block;
}

.agree-conditions a {
            border-bottom: 1px solid #000;
}
    </style>
@endpush
@section('content')
@php
     $user = auth()->user();
@endphp
    <!-- checkout start -->
    <div class="checkout-area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <input type="hidden" id="minimumRedeemPoint" value="{{$setting->min_point_redeem ?? '0'}}">
                    <input type="hidden" id="pointRedeem" value="{{$setting->point_redeem ?? '0'}}">
                    <input type="hidden" id="pointRedeemPrice" value="{{$setting->point_redeem_price ?? '0'}}">
                    <input type="hidden" id="minimumShippingAmount" value="{{$setting->min_shipping_amount ?? '0'}}">
                    <input type="hidden" id="discountOfPoint" value="0">
                </div>
                <div class="col-md-4">
                    @if ($user !== null)
                    <form id="redemption">
                        <h4>Redeem Point</h4>
                        <div class="d-flex mb-3">
                            <input type="text" id="redeempted_amount" name="redeemAmount" class="form-control rounded-0" value="0">
                            <input type="submit" class="btn btn-secondary border-0 rounded-0" value="Apply">
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            <form action="{{ route('checkout.payment') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-8">
                        <div class="checkout-user-info">
                            <h4>@lang('Billing details')</h4>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="name">@lang('Name')<span class="text-danger fs-6"><strong
                                                    class="text-danger">*</strong></span></label>
                                        <input type="text" id="customer_name" name="name" value="{{ old('name') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mobile">@lang('Mobile Number')<span class="text-danger fs-6"><strong
                                                    class="text-danger">*</strong></span></label>
                                        <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="email">@lang('Email') <span class="text-danger fs-6"><strong
                                                    class="text-danger">*</strong></span></label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <!--<div class="col-md-6 col-12">-->
                                <!--    <div class="form-group">-->
                                <!--        <label for="district">@lang('Division') <span class="text-danger fs-6"><strong-->
                                <!--                    class="text-danger">*</strong></span></label>-->
                                <!--        <select name="district_id" id="district" class="form-select address-select select2"-->
                                <!--            required>-->
                                <!--            <option value="" selected disabled>Select One</option>-->
                                <!--            <option value="Dhaka">Dhaka</option>-->
                                <!--            <option value="Chittagong">Chittagong</option>-->
                                <!--            <option value="Barishal">Barishal</option>-->
                                <!--            <option value="Rajshahi">Rajshahi</option>-->
                                <!--            <option value="Rangpur">Rangpur</option>-->
                                <!--            <option value="Sylhet">Sylhet</option>-->
                                <!--            <option value="Mymensingh">Mymensingh</option>-->
                                <!--            <option value="Khulna">Khulna</option>-->
                                <!--        </select>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="district">@lang('Division') <span class="text-danger fs-6"><strong class="text-danger">*</strong></span></label>
                                        <select name="district_id" id="district" class="form-select address-select select2" onchange="updateShippingOptions()" required>
                                            <option value="" selected disabled>Select One</option>
                                            <option value="Dhaka">Dhaka</option>
                                            <option value="Chittagong">Chittagong</option>
                                            <option value="Barishal">Barishal</option>
                                            <option value="Rajshahi">Rajshahi</option>
                                            <option value="Rangpur">Rangpur</option>
                                            <option value="Sylhet">Sylhet</option>
                                            <option value="Mymensingh">Mymensingh</option>
                                            <option value="Khulna">Khulna</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="address">@lang('Address')<span class="text-danger fs-6"><strong
                                                    class="text-danger">*</strong></span></label>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <!--<div class="col-md-6 col-12">-->
                                <!--    <div class="form-group">-->
                                <!--        <label for="s_charge">@lang('Shipping Place')<span class="text-danger fs-6"><strong-->
                                <!--                    class="text-danger">*</strong></span></label>-->
                                <!--        <select name="shipping_place" id="s_charge"-->
                                <!--            class="form-select address-select select2" required>-->
                                <!--            <option value="" selected hidden="">Select One</option>-->
                                <!--            <option data-type="insideDhaka" value="{{ $setting->inside_dhaka }}">Inside-->
                                <!--                Dhaka({{ __($setting->currency_symbol) }}{{ $setting->inside_dhaka }})-->
                                <!--            </option>-->
                                <!--            <option data-type="outsideDhaka" value="{{ $setting->outside_dhaka }}">Outside-->
                                <!--                Dhaka({{ __($setting->currency_symbol) }}{{ $setting->outside_dhaka }})-->
                                <!--            </option>-->
                                <!--            <option data-type="subAreaDhaka" value="{{ $setting->subarea_dhaka }}">Sub Area-->
                                <!--                Dhaka({{ __($setting->currency_symbol) }}{{ $setting->subarea_dhaka }})-->
                                <!--            </option>-->
                                <!--        </select>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="s_charge">@lang('Shipping Place')<span class="text-danger fs-6"><strong class="text-danger">*</strong></span></label>
                                        <select name="shipping_place" id="s_charge" class="form-select address-select select2" required>
                                            <option value="" selected hidden="">Select One</option>
                                            <option data-type="insideDhaka" value="{{ $setting->inside_dhaka }}">Inside Dhaka({{ __($setting->currency_symbol) }}{{ $setting->inside_dhaka }})</option>
                                            <option data-type="outsideDhaka" value="{{ $setting->outside_dhaka }}">Outside Dhaka({{ __($setting->currency_symbol) }}{{ $setting->outside_dhaka }})</option>
                                            <option data-type="subAreaDhaka" value="{{ $setting->subarea_dhaka }}">Sub Area Dhaka({{ __($setting->currency_symbol) }}{{ $setting->subarea_dhaka }})</option>
                                        </select>
                                    </div>
                                </div>


                            </div>
                            <div class="form-group">
                                <label for="note">@lang('Order notes')<span class="text-danger fs-6"></span></label>
                                <textarea name="note" id="note" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="checkout-summery">
                            @php
                                $user_id = auth()->user()->id ?? null;
                                $user = auth()->user();

                                if ($user_id != null) {
                                    $data = App\Models\Cart::where('customer_id', $user_id)
                                        ->with(['product', 'product.stocks', 'product.categories', 'productAttributes'])
                                        ->get();
                                } else {
                                    $s_id = session()->get('session_id');
                                    $data = App\Models\Cart::where('session_id', $s_id)
                                        ->with(['product', 'product.stocks', 'product.categories', 'productAttributes'])
                                        ->get();
                                }
                                $sub_total = 0;
                                $total_point = 0;
                                $coupon = session('coupon')['amount'] ?? 0;
                            @endphp


                            <h4>@lang('Order Summary')</h4>
                            <div class="accordion" id="accordionPanelsStayOpenExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <div class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#checkout-cart" aria-expanded="true"
                                            aria-controls="checkout-cart">
                                            @lang('Cart Items')
                                        </div>
                                    </h2>
                                    <div id="checkout-cart" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            @foreach ($data as $item)
                                                <div class="single-cart-item checkout-item">
                                                    <div class="single-cart-product d-flex checkout-item">
                                                        <div class="sigle-cart-product-photo checkout-item">
                                                            <a href="#">
                                                                <img src="{{ asset($templateAssets . 'images/products/' . $item->product->image) }}"
                                                                    alt="">
                                                            </a>
                                                        </div>
                                                        <div class="sigle-cart-product-content checkout-item">
                                                            <div class="cart-product-name">
                                                                <a href="#">{{ __($item->product->name) }} -
                                                                    {{ __($item->productAttributes->content ?? '') }}</a>
                                                                @if ($item->product->discount_price == null || $item->product->discount_price == 0)
                                                                    <span
                                                                        class="d-block">{{ __($setting->currency_symbol) }}{{ __(number_format($item->product->regular_price * $item->quantity, 2)) }}</span>
                                                                @else
                                                                    <span
                                                                        class="d-block">{{ __($setting->currency_symbol) }}{{ __(number_format($item->product->discount_price * $item->quantity, 2)) }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="cart-product-quantity d-flex  mt-1">
                                                                <div class="checkout-item-point">
                                                                    <span>HOLAGO CLUB POINTS:
                                                                        <br>{{ $item->product->points * $item->quantity }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($item->product->discount_price == null || $item->product->discount_price == 0)
                                                    @php $sub_total += $item->product->regular_price * $item['quantity'] @endphp
                                                @else
                                                    @php $sub_total += $item->product->discount_price * $item['quantity'] @endphp
                                                @endif
                                                @php $total_point += $item->product->points * $item['quantity'] @endphp
                                                @php $afterDisount = $coupon > 0 ? $sub_total - $coupon : $sub_total @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-payment-area">
                                <input type="hidden" id="subtotal_hidden" name="subtotal" value="{{ $sub_total }}">
                                <input type="hidden" id="shipping_hidden" name="shipping_charge" value="">
                                <input type="hidden" id="redeem_point_hidden" name="redeem_point" value="0">
                                <input type="hidden" id="grandTotal_hidden" name="grand_total" value="">
                                <p>
                                    <strong>@lang('Subtotal') :</strong> <span>{{ __($setting->currency_symbol) }} <span
                                            id="check_total_amount">{{ __($sub_total) }}</span></span>
                                </p>
                                <p>
                                    <strong>@lang('Shipping') :</strong> <span>{{ __($setting->currency_symbol) }} <span
                                            id="shipping_amount">{{ __(00) }}</span></span>
                                </p>
                                <p>
                                    <strong>@lang('Discount') :</strong>
                                    <span>{{ __($setting->currency_symbol) }}{{ __($coupon ?? 00) }}</span>
                                </p>
                                <p>
                                    <strong>@lang('Shipping Discount') :</strong> <span>{{ __($setting->currency_symbol) }} <span
                                            id="shipping_discount">{{ __(00) }}</span></span>
                                </p>
                                <p>
                                    <strong>@lang('Point Discount') :</strong> <span>{{ __($setting->currency_symbol) }} <span
                                            id="point_discount">{{ __(00) }}</span></span>
                                </p>
                                <p>
                                    <strong>@lang('Total Points') :</strong> <span>{{ __($total_point) }}</span>
                                </p>
                                <p>
                                    <strong>@lang('Grand Total') :</strong> <span>{{ __($setting->currency_symbol) }} <span
                                            id="grandTotal_amount">{{ __($afterDisount) }}</span></span>
                                </p>
                            </div>
                            <div class="payment-method my-3 d-flex align-items-center">
                                <div class="form-group" id="codPayment">
                                    <label class="img-btn">
                                        <input type="radio" name="payment_option" value="cod">
                                        <img src="{{ asset($templateAssets . 'img/cash-on-delivery.jpg') }}"
                                            alt="COD">
                                            <label class="img-btn" for="checkbox">
                                                <i class="fa fa-check"></i>
                                            </label>
                                    </label>
                                    <span>@lang('COD')</span>
                                </div>
                                <div class="form-group">
                                    <label class="img-btn">
                                        <input type="radio" name="payment_option" value="sslcommerz">
                                        <img src="{{ asset($templateAssets . 'img/sslcz-verified.png') }}"
                                            alt="Credit Card">
                                               <label class="img-btn" for="checkbox">
                                                <i class="fa fa-check"></i>
                                            </label>
                                    </label>
                                    <span>@lang('Credit Card')</span>
                                </div>
                                <div class="form-group">
                                    <label class="img-btn">
                                        <input type="radio" name="payment_option" value="bkash">
                                        <img src="{{ asset($templateAssets . 'img/sslcz-verified.png') }}"
                                            alt="Credit Card" title="Bkash">
                                               <label class="img-btn" for="checkbox">
                                                <i class="fa fa-check"></i>
                                            </label>
                                    </label>
                                    <span>@lang('Bkash')</span>
                                </div>
                            </div>
                            <p>@lang('Your personal data will be used to process your order, support your experience throughout
                                                                                                                                                                                                                                    this website, and for other purposes described in our privacy policy').</p>
                            <div class="agree-conditions">
                                <div class="form-check">
                                    <input class="form-check-input" name="check" type="checkbox" value="yes"
                                        id="agree-condition" checked required>
                                    <label class="form-check-label" for="agree-condition">
                                        @lang(' I have read and agree to the website') <a href="{{route('tou')}}" target="_blank"
                                            title="terms and conditions">@lang('terms and conditions')</a>, <a
                                            href="{{ route('privacy.policy') }}"
                                            title="shipping policy">@lang('shipping policy')</a> @lang('and')
                                        <a href="{{ route('refund') }}"
                                            title="refund & return policy">@lang('refund & return policy')</a>.
                                        <span class="text-danger fs-6"><strong>*</strong></span>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn">@lang('Place order')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        // // value
        // let shippingPlace  = document.getElementById("s_charge")
        // console.log("shippingPlace",shippingPlace)
        // let shippingAmount  = document.getElementById("shipping_amount")
        // console.log("shipping Amount",shippingAmount)
        // let checkTotalAmount  = document.getElementById("check_total_amount")
        // console.log("check total amount",checkTotalAmount)
        // let grandTotal = document.getElementById("grandTotal_amount")
        // console.log("grand total",grandTotal)
        // // hidden value
        // let shippingHidden = document.getElementById("shipping_hidden")
        // let grandTotalHidden = document.getElementById("grandTotal_hidden")

        // shippingPlace.addEventListener("change", function (){
        //     shippingAmount.innerText = shippingPlace.value;
        //     console.log("shipping place value",shippingPlace.value)

        //     let checkAmountNum = Number(checkTotalAmount.innerText)
        //     console.log("check amount num",checkAmountNum)
        //     let sumGrandTotal = checkAmountNum + Number(shippingPlace.value);
        //     console.log('sum of grand total',sumGrandTotal)
        //     grandTotal.innerText = sumGrandTotal
        //     shippingHidden.value = shippingPlace.value
        //     grandTotalHidden.value = sumGrandTotal
        // })
    </script>
    <script>
        $(document).on('change', "#s_charge", function() {
            var selectedOption = $(this).find('option:selected');
            var attribute = selectedOption.attr('data-type')
            if (attribute == 'outsideDhaka' || attribute == 'subAreaDhaka') {
                $('#codPayment').hide().find('input[type="radio"]').prop('checked', false);
            } else {
                $('#codPayment').show()
            }
            calculate()
        })

        $(document).on('submit', '#redemption', function(e) {
            e.preventDefault();
            var amount =parseFloat($("#redeempted_amount").val()) || 0;
            var url = '{{ route('point.redeem') }}';
            var minimumRedeemAmount = parseFloat($("#minimumRedeemPoint").val())|| 0;
            var redeemAmount = parseFloat($("#pointRedeem").val()) || 0;
            var redeemAmountPrice = parseFloat($("#pointRedeemPrice").val()) || 0;

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    amount,minimumRedeemAmount,redeemAmount,redeemAmountPrice
                },
                success: function(response) {
                    if(response.status == '1'){
                        toastr.success(response?.message);
                        var pointdiscountAmount = response?.pointDiscount;
                        $("#discountOfPoint").val(pointdiscountAmount);
                        console.log("redem point",amount)
                        $("#redeem_point_hidden").val(amount);
                        calculate()
                    }else{
                        toastr.error(response?.message);
                        console.log("failed")
                    }
                    console.log(response);

                },
                error: function(error) {
                    // Handle the error
                    console.error('Error:', error);
                }
            });
        });



        function calculate() {
            var shippingDiscountAmount = parseFloat($("#minimumShippingAmount").val()) || 0;
            var redemptedAmount = parseFloat($("#discountOfPoint").val()) || 0;
            var subtotal = parseFloat($("#subtotal_hidden").val()) || 0;
            var shippingPlace = $("#s_charge").find('option:selected').attr('data-type');
            var shippingCharge = parseFloat($("#s_charge").val()) || 0;
            var afterAppliedShipping = subtotal + shippingCharge - redemptedAmount;
            $("#point_discount").text("-"+ redemptedAmount);
            $('#shipping_amount').text(shippingCharge);
            $('#shipping_hidden').val(shippingCharge);
            $("#grandTotal_amount").text(afterAppliedShipping);
            $("#grandTotal_hidden").val(afterAppliedShipping);
            var shippingDiscount = 0;

            if (shippingDiscountAmount <= afterAppliedShipping) {
                var grandTotal = parseFloat($("#grandTotal_amount").text()) || 0;
                afterShippingDiscount = grandTotal - shippingCharge;
                $("#shipping_discount").text("-" + shippingCharge);
                $('#shipping_hidden').val('0');
                $("#grandTotal_amount").text(afterShippingDiscount);
                $("#grandTotal_hidden").val(afterShippingDiscount);
                console.log(grandTotal)

            }
            // var grandTotal = parseFloat($("#grandTotal_amount").text()) || 0;

            var total = 0;

            console.log("subtotal vaiya", subtotal)
            console.log("shipping place", shippingPlace)
            console.log("shipping charge", shippingCharge)
            console.log("Grand Total", grandTotal)
        }
    </script>



    <script>
//     $(document).ready(function() {
//     // Initial update
//     var selectedDivision = $('#district').val();
//     var shippingPlaceField = $('#s_charge');
//     var cod = $('#codPayment');

//     if (selectedDivision !== 'Dhaka') {
//         shippingPlaceField.val('{{ $setting->outside_dhaka }}').show();
//         cod.hide().find('input[type="radio"]').prop('checked', false);
//     } else {
//         shippingPlaceField.val('{{ $setting->inside_dhaka }}').show();
//         cod.show();
//     }

//     // Event handler for district change
//     $('#district').on('change', function() {
//         var selectedDivision = $(this).val();

//         if (selectedDivision !== 'Dhaka') {
//             shippingPlaceField.val('{{ $setting->outside_dhaka }}').show();
//             cod.hide();
//         } else {
//             shippingPlaceField.val('{{ $setting->inside_dhaka }}').show();
//             cod.show();
//         }
//         calculate();
//     });
// });

  function updateShippingOptions() {
            var divisionDropdown = document.getElementById('district');
            var shippingPlaceDropdown = document.getElementById('s_charge');
            var cod = $('#codPayment');
            // Get the selected value from the Division dropdown
            var selectedDivision = divisionDropdown.value;

            // Get all options in the Shipping Place dropdown
            var options = shippingPlaceDropdown.options;

            // Hide all options first
            for (var i = 0; i < options.length; i++) {
                options[i].style.display = 'none';
            }

            // Show the appropriate options based on the selected Division
            if (selectedDivision === 'Dhaka') {
                document.querySelector('option[data-type="insideDhaka"]').style.display = 'block';
                document.querySelector('option[data-type="outsideDhaka"]').style.display = 'block';
                document.querySelector('option[data-type="subAreaDhaka"]').style.display = 'block';
                cod.show();
            } else {
                document.querySelector('option[data-type="outsideDhaka"]').style.display = 'block';
                cod.hide();
                // You can add additional conditions for other options here if needed
            }

            // Set the first visible option as selected
            for (var i = 0; i < options.length; i++) {
                if (options[i].style.display !== 'none') {
                    shippingPlaceDropdown.value = options[i].value;
                    break;
                }
            }
            calculate();
       }


</script>
@endpush
