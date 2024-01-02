<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ProductStocks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class OrderConteoller extends Controller
{
    public function saveOrder(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();
        // dd($user);
        $user_id = auth()->user()->id ?? null;
        if ($user_id != null) {
            $data = Cart::where('customer_id', $user_id)->with(['product', 'product.stocks', 'product.categories', 'productAttributes'])
                ->get();
        } else {
            $s_id       = session()->get('session_id');
            $data = Cart::where('session_id', $s_id)
                ->with(['product', 'product.stocks', 'product.categories', 'productAttributes'])
                ->get();
        }

        $prefix = 'HLG';
        $uniqueId = DB::table('orders')->count() + 1;

        // Ensure the uniqueId is limited to 4 digits
        $uniqueId = str_pad($uniqueId, 7, '0', STR_PAD_LEFT);

        $invoiceNumber = "{$prefix}-{$uniqueId}";

        // dd($data);
        Session::forget('coupon');
        $order = new Order();
        $order->customer_id = auth()->user()->id ?? null;
        $order->order_number = $invoiceNumber;
        $order->shipping_address = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'district_id' => $request->district_id,
            'address' => $request->address,
            'shipping_place' => $request->shipping_place,
            'note' => $request->note
        ];
        $order->shipping_charge = $request->shipping_charge;
        $order->total_amount = $request->grand_total;
        $order->order_type = $request->payment_option;
        $order->payment_status = 'cod';
        $order->save();
        $order_id = $order->id;
        $contents = $data;
        foreach ($contents as  $v_content) {
            $order_details = new OrderDetails();
            $order_details->order_id = $order_id;
            $order_details->product_id = $v_content->product->id;
            $order_details->quantity = $v_content->quantity;
            $order_details->buying_price = $v_content->product->buying_price;
            $order_details->regular_price = $v_content->product->regular_price;
            $order_details->discount_price = $v_content->product->discount_price;
            $order_details->attribute = $v_content->productAttributes->content ?? '';
            $order_details->wholesale_price = $v_content->product->wholesale_price;
            $order_details->wholesale_minimum_quantity = $v_content->product->wholesale_min_qty;
            $order_details->details = null;
            $order_details->total_price = $v_content->product->regular_price * $v_content->quantity;
            $order_details->save();

            $attribute = json_encode($v_content->attributes);
            $productStock = ProductStocks::where('product_id', $v_content->product_id)->whereJsonContains('attributes', $attribute)->first();
            $requestqty = $v_content->quantity;
            $currentQuantity = $productStock->quantity;
            $currentstock = $currentQuantity - $requestqty;
            // dd($currentstock);
            $productStock->update([
                'quantity' => $currentstock
            ]);
        }

        // dd($data);

        if ($user !== null) {
            $current_user = User::where('id',$user->id)->first();
            // dd($current_user);
            $remainingPoint = $user->points - $request->redeem_point;
            $current_user->update([
                'points' => $remainingPoint
            ]);
        }
        foreach ($data as $cart) {
            $cart->delete();
        }


        $alert = ['success', 'Payment & Order place Successfully!'];
        return  redirect()->route('order.success')->withAlert($alert);
    }

    public function successOrder()
    {
        return view('templates.classicmart.user.order_success');
    }
    public function failedOrder()
    {
        return view('templates.classicmart.user.failed_order');
    }
    public function cancelOrder()
    {
        return view('templates.classicmart.user.cancel_order');
    }

    public function payment(Request $request)
    {
        $request->validate([
            'payment_option' => 'required'
        ]);

        if ($request->payment_option == 'cod') {
            $checkout = new OrderConteoller;
            return $checkout->saveOrder($request);
        }
        //session
        Session::put('checkout_request', $request->all());
        Session::put('name', $request->name);
        Session::put('mobile', $request->mobile);
        Session::put('email', $request->email);
        Session::put('district_id', $request->district_id);
        Session::put('address', $request->address);
        Session::put('shipping_place', $request->shipping_place);
        Session::put('note', $request->note);
        Session::put('shipping_charge', $request->shipping_charge);
        Session::put('grand_total', $request->grand_total);
        Session::put('payment_option', $request->payment_option);
        Session::put('subtotal', $request->subtotal);

        $payment_method = $request->payment_option;
        $order_number = trxNumber();
        Session::put('invoice_no', $order_number);
        if ($request->payment_option == 'cod') {
            return redirect()->route('order.store');
        } else {
            Session::put('payment_method', $request->payment_option);
            Session::put('payment_type', 'cart_payment');
            $total_amount = $request->grand_total;
            Session::put('payment_amount', $total_amount);
            if ($request->payment_option == 'nagad') {
                return "nagad";
            } else if ($request->payment_option == 'bkash') {
                $bkash = new BkashPaymentController;
                return $bkash->createPayment($request);
            } elseif ($request->payment_option == 'sslcommerz') {
                $ssl = new SslCommerzPaymentController;
                return $ssl->index($request);
            }
        }

        return view('templates.classicmart.checkout.payment', compact('payment_method', 'total_amount', 'order_number'));
    } // end method


    public function redeem(Request $request)
    {
        $redeemValue = (float)$request->amount;
        $minimumReedAmount = (float)$request->minimumRedeemAmount;
        $redeemAmount = (float)$request->redeemAmount;
        $redeemPriceValue = (float)$request->redeemAmountPrice;
        $user = auth()->user();
        if ($redeemValue < $minimumReedAmount) {
            return response()->json([
                'status' => 0,
                'message' => 'Please Fillup Minimum Amount of Redeem!'
            ]);
        } else {
            if ($user->points >= $redeemValue) {
                $totalDivideOfRedeem = $redeemValue / $redeemAmount;
                $totalDiscountOfRedeem = $totalDivideOfRedeem * $redeemPriceValue;
                // dd($totalDiscountOfRedeem);
                return response()->json([
                    'status' => 1,
                    'message' => "Applied Redeem Point!",
                    'pointDiscount' => $totalDiscountOfRedeem
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Not Enough Point To Redeem!'
                ]);
            }
        }
    }
}
