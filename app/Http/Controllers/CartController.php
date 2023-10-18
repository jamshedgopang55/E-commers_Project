<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\country;
use App\Models\product;
use App\Models\shipping;
use App\Models\order_item;
use Illuminate\Http\Request;
use App\Models\discountCoupon;
use Illuminate\Support\Carbon;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $req)
    {
        $product = product::with('product_images')->find($req->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found'
            ]);
        }
        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($req->id, $product->tittle, 1, $product->price, ['productImage' => ($product->product_images) ? $product->product_images->first() : '']);
                session()->flash('success', "<strong>" . $product->tittle . "</strong>" . ' added in Cart.');
                return response()->json([
                    'status' => true,
                    'message' => $product->tittle . ' added in Cart'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Product Alrady Exist In Cart'
                ]);
            }
        } else {
            Cart::add($req->id, $product->tittle, 1, $product->price, ['productImage' => ($product->product_images) ? $product->product_images->first() : '']);
            session()->flash('success', "<strong>" . $product->tittle . "</strong>" . ' added in Cart.');

            return response()->json([
                'status' => true,
                'message' => $product->tittle . " added in Cart"
            ]);
        }
    }

    public function cart()
    {
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $req)
    {
        $item_info = Cart::get($req->rowId);

        $product = product::with('product_images')->find($item_info->id);

        if ($product->track_qty == 'Yes') {
            if ($req->qty <= $product->qty) {
                Cart::update($req->rowId, $req->qty);
                $message = "Cart Updated Successfully";
                $status = true;
                session()->flash('success', $message);

            } else {
                $message = "Requested qty(" . $req->qty . ") not avaliable in stock.";
                $status = false;
                session()->flash('error', $message);

            }
        } else {
            Cart::update($req->rowId, $req->qty);
            $message = "Cart Updated Successfully";
            $status = true;
            session()->flash('success', $message);
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
    public function deleteCart(Request $req)
    {
        $cartItem = Cart::get($req->rowId);
        if ($cartItem == null) {
            $errorMessage = 'Item Not Found In Cart';
            session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }
        Cart::remove($req->rowId);
        $Message = 'Item Remove Form Cart Successfully';
        session()->flash('success', $Message);
        return response()->json([
            'status' => true,
            'message' => $Message
        ]);
    }
    public function checkout()
    {
        $discount = 0;
        //IF Cart Is Empty Redirect TO Cart Page
        if (Cart::count() == 0) {
            session()->flash('success', 'Your Cart Empty');

            return redirect()->route('front.cart');
        }
        //If User is not Login Redirect TO Login Page

        if (Auth::check() == false) {
            session(['url.intended' => url()->current()]);

            return redirect()->route('account.login');
        }

        session()->forget('url.intended');
        session()->forget('code');
        $totalQty = 0;
        $totalShipping = 0;
        $shippingAmount = 0;
        $grandTotal = 0;

        $countries = country::orderBy('name', 'asc')->get();

        $subTotal = Cart::subtotal(2, '.', '');
        ///Apply Discount Here
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        $shippingCounrty = CustomerAddress::where('user_id', Auth::user()->id)->first();

        if ($shippingCounrty != null) {
            $shipping = shipping::where('country_id', $shippingCounrty->country_id)->first();

            if ($shipping == null) {
                $restAmount = shipping::where('country_id', 'rest_of_world')->first();
                $shippingAmount = $restAmount->amount;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                $totalShipping = $shippingAmount * $totalQty;
                $grandTotal = $totalShipping + $subTotal;

            } else {
                $shippingAmount = $shipping->amount;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                $totalShipping = $shippingAmount * $totalQty;
                $grandTotal = ($totalShipping - $discount) + $subTotal;


            }
        } else {

            $grandTotal = ($totalShipping - $discount);
            $$totalShipping = 0;
        }
        if($grandTotal== 0){
            $grandTotal = Cart::subtotal(2,'.','');
        }
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        $data['totalShipping'] = $totalShipping;
        $data['customerAddress'] = $customerAddress;
        $data['countries'] = $countries;
        $data['discount'] = $discount;
        $data['grandTotal'] = $grandTotal;
        return view('front.ckeckout', $data);
    }
    public function processCheckout(Request $req)
    {
        // return session()->get('code');
        $validator = Validator::make($req->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } else {
            /// Save User Address
            $user = Auth::user();
            CustomerAddress::updateOrcreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'first_name' => $req->first_name,
                    'last_name' => $req->last_name,
                    'email' => $req->email,
                    'mobile' => $req->mobile,
                    'country_id' => $req->country,
                    'address' => $req->address,
                    'apartment' => $req->appartment,
                    'city' => $req->city,
                    'state' => $req->state,
                    'zip' => $req->zip,
                ]
            );
            $discount = 0;
            $promoCode = null;
            $couponId = '';
            ///Apply Discount Here
            if (session()->has('code')) {
                $code = session()->get('code');
                if ($code->type == 'percent') {
                    $discount = ($code->discount_amount / 100) * Cart::subtotal(2, '.', '');
                } else {
                    $discount = $code->discount_amount;
                }
                $promoCode = $code->code;

                $couponId = $code->id;
            }

            /// Store data in Order Table
            if ($req->payment_method == 'cod') {
                $shipping = 0;

                $subTotal = Cart::subtotal(2, '.', '');

                $totalQty = 0;
                $subTotal = Cart::subtotal(2, '.', '');
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                $shippingInfo = shipping::where('country_id', $req->country)->first();

                if ($shippingInfo != null) {
                    $shipping = $totalQty * $shippingInfo->amount;
                    $grandTotal = $subTotal + $shipping;
                } else {
                    $shippingInfo = shipping::where('country_id', 'rest_of_world')->first();
                    $shipping = $totalQty * $shippingInfo->amount;
                    $grandTotal = $subTotal + $shipping;
                }




                $order = new order;
                $order->user_id = $user->id;
                $order->subtotal = $subTotal;
                $order->shipping = $shipping;
                $order->grand_total = $grandTotal-$discount;
                $order->discount = $discount;
                $order->coupon_code_id = $couponId;
                $order->coupon_code = $promoCode;
                $order->first_name = $req->first_name;
                $order->last_name = $req->last_name;
                $order->email = $req->email;
                $order->mobile = $req->mobile;
                $order->country_id = $req->country;
                $order->address = $req->address;
                $order->apartment = $req->appartment;
                $order->city = $req->city;
                $order->state = $req->state;
                $order->zip = $req->zip;
                $order->notes = $req->order_notes;
                $order->save();

                /// Store data in Order Item Table

                $orderItem = new order_item;
                foreach (Cart::content() as $item) {
                    $orderItem->product_id = $item->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price * $item->qty;
                    $orderItem->save();
                }
                session()->flash('success', 'You have successfully placed your Order.');
                Cart::destroy();
                return response()->json([
                    'status' => true,
                    'orderId' => $order->id,
                    'message' => 'Order Save Successfully'
                ]);
            }

        }

    }

    public function getOrderSummery(Request $req)
    {
        $subTotal = Cart::subtotal(2, '.', '');
        ///Apply Discount Here
        $discount = 0;
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        if ($req->country_id > 0) {
            $shippingInfo = shipping::where('country_id', $req->country_id)->first();

            if ($shippingInfo != null) {
                $totalQty = 0;

                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'shippingCharge' => number_format($shippingCharge, 2),
                    'discount' => number_format($discount),
                    'grandTotal' => number_format($grandTotal, 2)
                ]);
            } else {
                $totalQty = 0;
                foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
                }

                $shippingInfo = shipping::where('country_id', 'rest_of_world')->first();


                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'shippingCharge' => number_format($shippingCharge, 2),
                    'discount' => number_format($discount,2),
                    'grandTotal' => number_format($grandTotal, 2)
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'shippingCharge' => number_format(0, 2),
                'discount' => number_format($discount,2),
                'grandTotal' => number_format(($subTotal - $discount), 2)
            ]);
        }
    }
    public function thankYou($id)
    {
        return view('front.thankyou', [
            'id' => $id
        ]);
    }

    public function applyDiscount(Request $req)
    {

        $code = discountCoupon::where('code', $req->code)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Discount Coupon'
            ]);
        } else {
            $now = Carbon::now();
            if ($code->start_at != '') {
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->start_at);
                if ($now->lt($startDate)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Discount Coupon'
                    ]);
                }
            }
            if ($code->expires_at != '') {
                $expiresDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);
                if ($now->gt($expiresDate)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Discount Coupon'
                    ]);
                }
            }
            //Coupons Used
            if($code->max_uses > 0){
                $couponUsed = order::where('coupon_code_id',$code->id)->count();
                if($couponUsed >= $code->max_uses){
                    return response()->json([
                        'status' => false,
                        'message' => 'Sorry, the coupon limit has been reached. You can no longer use this coupon.'
                    ]);
                }
            }

            ///Max Uses User Check

            if($code->max_uses_user > 0){
                $couponUsedByUser = order::where(['coupon_code_id'=>$code->id,'user_id'=>Auth::user()->id])->count();
                if($couponUsedByUser >= $code->max_uses_user){
                    return response()->json([
                        'status' => false,
                        'message' => 'youve already used this coupon.'
                    ]);
                }
            }
             $code->min_amount;
            ///Mix Amount Check
           $subTotal = Cart::subtotal(2,'.','');
            if($code->min_amount > 0){
                if($subTotal < $code->min_amount){
                    return response()->json([
                        'status' => false,
                        'message' => 'Your min Amount must Be $'.$code->min_amount.'.'
                    ]);
                }
            }

            session()->put('code', $code);
            return $this->getOrderSummery($req);

        }
    }
}
