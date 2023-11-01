<?php

namespace App\Http\Controllers;

use Stripe;
use Exception;
use App\Models\order;
use App\Models\country;
use App\Models\product;
use App\Models\shipping;
use App\Models\order_item;
use Illuminate\Support\Str;
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
            $totalShipping = 0;
        }
        if ($grandTotal == 0) {
            $grandTotal = Cart::subtotal(2, '.', '');
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
            'address' => 'required|min:15',
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
            $coupenId = null;
            ///Apply Discount Here
            if (session()->has('code')) {
                $code = session()->get('code');
                if ($code->type == 'percent') {
                    $discount = ($code->discount_amount / 100) * Cart::subtotal(2, '.', '');
                } else {
                    $discount = $code->discount_amount;
                }
                $promoCode = $code->code;

                $coupenId = $code->id;
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
                $order->grand_total = $grandTotal - $discount;
                $order->discount = $discount;
                $order->coupon_code_id = $coupenId;
                $order->coupon_code = $promoCode;

                $order->payment_status = 'not paid';
                $order->status = 'pending';

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

                foreach (Cart::content() as $item) {
                    $orderItem = new order_item;
                    $orderItem->product_id = $item->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price * $item->qty;
                    $orderItem->save();

                    //// Update Product Stock
                    $productData = product::find($item->id);
                    if ($productData->track_qty == 'Yes') {
                        $currentQty = $productData->qty;
                        $updatedQty = $currentQty - $item->qty;
                        $productData->qty = $updatedQty;
                        $productData->save();
                    }


                }


                ///Sending Email
                // orderEmail($order->id ,'customer');


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
                    'discount' => number_format($discount, 2),
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
                    'discount' => number_format($discount, 2),
                    'grandTotal' => number_format($grandTotal, 2)
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'shippingCharge' => number_format(0, 2),
                'discount' => number_format($discount, 2),
                'grandTotal' => number_format(($subTotal - $discount), 2)
            ]);
        }
    }
    public function thankYou($id)
    {
        if(isset($_GET['session_id'])){
            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            $session_id = $_GET['session_id'];
            ///Update Order Payment Status
            $orderData = order::where('session_id',$session_id)->first();
             $orderData->payment_status = 'paid';
             $orderData->save();
            $id = $orderData->id;
             Cart::destroy();

             // Update Product Stock
            //  if ($productData->track_qty == 'Yes') {
            //      $currentQty = $productData->qty;
            //      $updatedQty = $currentQty - $item->qty;
            //      $productData->qty = $updatedQty;
            //      $productData->save();
            //  }

            try {
                $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
                if (!$session) {
                    abort(404);
                }

            } catch (Exception $e) {
                abort(404);
            };
        }

        return view('front.thankyou', [
            'id' => $id
        ]);
    }

    public function applyDiscount(Request $req)
    {

        $code = discountCoupon::where('code', $req->code)->where('status', 1)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Discount Coupon'
            ]);
        } else {
            $now = Carbon::now()->setTimezone('PKT')->format('d-m-Y');
            $now = Carbon::parse($now);


            if ($code->start_at != '') {
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->start_at)->format('d-m-Y');
                ;
                if ($now->lt($startDate)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Discount Coupon'
                    ]);
                }
            }
            if ($code->expires_at != '') {
                $expiresDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at)->subDays(1)->format('d-m-Y');

                if ($now->gt($expiresDate)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'The coupon has expired'
                    ]);
                }
            }
            //Coupons Used
            if ($code->max_uses > 0) {
                $couponUsed = order::where('coupon_code_id', $code->id)->count();
                if ($couponUsed >= $code->max_uses) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Sorry, the coupon limit has been reached. You can no longer use this coupon.'
                    ]);
                }
            }

            ///Max Uses User Check

            if ($code->max_uses_user > 0) {
                $couponUsedByUser = order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
                if ($couponUsedByUser >= $code->max_uses_user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'youve already used this coupon.'
                    ]);
                }
            }
            $code->min_amount;
            ///Mix Amount Check
            $subTotal = Cart::subtotal(2, '.', '');
            if ($code->min_amount > 0) {
                if ($subTotal < $code->min_amount) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Your min Amount must Be $' . $code->min_amount . '.'
                    ]);
                }
            }

            session()->put('code', $code);
            return $this->getOrderSummery($req);

        }
    }
    public function stripeCall(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:15',
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
        }



        /////Stripe Calll
        $user_email = Auth::user()->email;
        $line_items = [];
        $total_qty = 0;

        $country = Country::where('id' , $req->country)->first();

        $shippingInfo = shipping::where('country_id', $req->country)->first();
        if($shippingInfo == null){
             $shippingInfo = shipping::where('country_id', 'rest_of_world')->first();
        }

        foreach (Cart::content() as $item) {

            $price = $item->price;

            $line_items[] = [
                'price_data' => [
                    'product_data' => [
                        'name' => $item->name
                    ],
                    'unit_amount' => 100 * $price,

                    'currency' => 'USD'
                ],
                'quantity' => $item->qty
            ];

            $total_qty += $item->qty;
            $total_Shipping = $shippingInfo->amount * $total_qty * 100;

          $shipping_options =   [
                'shipping_rate_data' => [
                  'type' => 'fixed_amount',
                  'fixed_amount' => [
                    'amount' => $total_Shipping,
                    'currency' => 'usd',
                  ],
                  'display_name' => 'Shipping Charges',
                  'delivery_estimate' => [
                    'minimum' => [
                      'unit' => 'business_day',
                      'value' => 5,
                    ],
                    'maximum' => [
                      'unit' => 'business_day',
                      'value' => 7,
                    ],
                  ],
                ],
            ];

        };

        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));

        $successUrl = route('front.thankYou', 33, true) . "?session_id={CHECKOUT_SESSION_ID}";
        $response = $stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'customer_email' => $user_email,
            'payment_method_types' => ['link', 'card'],
            'shipping_address_collection' => ['allowed_countries' => [$country->code]],
            'shipping_options' => [$shipping_options],
            'line_items' => $line_items,
            'mode' => 'payment',
            'allow_promotion_codes' => true
        ]);

         ////Create Order But Not Paid.....
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
         $coupenId = null;
         ///Apply Discount Here
         if (session()->has('code')) {
             $code = session()->get('code');
             if ($code->type == 'percent') {
                 $discount = ($code->discount_amount / 100) * Cart::subtotal(2, '.', '');
             } else {
                 $discount = $code->discount_amount;
             }
             $promoCode = $code->code;

             $coupenId = $code->id;
         }

         /// Store data in Order Table

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

         $Session_id = Str::random(60);
        //  return $response->id;

         $order = new order;
         $order->user_id = $user->id;
         $order->session_id = $response->id;
         $order->subtotal = $subTotal;
         $order->shipping = $shipping;
         $order->grand_total = $grandTotal - $discount;
         $order->discount = $discount;
         $order->coupon_code_id = $coupenId;
         $order->coupon_code = $promoCode;

         $order->payment_status = 'pending';
         $order->status = 'pending';

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

         foreach (Cart::content() as $item) {
             $orderItem = new order_item;
             $orderItem->product_id = $item->id;
             $orderItem->order_id = $order->id;
             $orderItem->name = $item->name;
             $orderItem->qty = $item->qty;
             $orderItem->price = $item->price;
             $orderItem->total = $item->price * $item->qty;
             $orderItem->save();

             ///Product Stock Update
            //  $productData = product::find($item->id);
            //  if ($productData->track_qty == 'Yes') {
            //      $currentQty = $productData->qty;
            //      $updatedQty = $currentQty - $item->qty;
            //      $productData->qty = $updatedQty;
            //      $productData->save();
            //  }
         }

         session()->flash('success', 'You have successfully placed your Order.');
         ///Sending Email
         // orderEmail($order->id ,'customer');

        return response()->json([
            'status' => true,
            'url' => $response['url'],
            'message' => 'Order Save Successfully'
        ]);

    }
    public function storeOrder(Request $req)
    {

    }

}


