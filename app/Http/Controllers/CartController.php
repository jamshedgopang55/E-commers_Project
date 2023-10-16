<?php

namespace App\Http\Controllers;

use App\Models\country;
use App\Models\product;
use App\Models\order;
use App\Models\order_item;
use Illuminate\Http\Request;
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
        //IF Cart Is Empty Redirect TO Cart Page
        if (Cart::count() == 0) {
            session()->flash('success', '');
            return redirect()->route('front.cart');
        }
        //If User is not Login Redirect TO Login Page

        if (Auth::check() == false) {
                session(['url.intended' => url()->current()]);

            return redirect()->route('account.login');
        }

        session()->forget('url.intended');

        $countries = country::orderBy('name', 'asc')->get();

        $customerAddress = CustomerAddress::where('user_id' , Auth::user()->id)->first();
        $data['customerAddress'] = $customerAddress;
        $data['countries'] = $countries;

        return view('front.ckeckout',$data);
    }
    public function processCheckout(Request $req){
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
            // 'payment_method' => 'required',
            // 'card_number' => 'required',
            // 'expiry_date' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
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

            /// Store data in Order Table
            if($req->payment_method == 'cod'){
                $shipping = 0;
                $discount = 0;
                $subTotal = Cart::subtotal(2,'.','');
                $grandTotal = $shipping+$subTotal;

                $order = new order;
                $order->user_id = $user->id;
                $order->subtotal = $subTotal;
                $order->shipping = $shipping;
                $order->grand_total = $grandTotal;


                $order->first_name =$req->first_name;
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
                foreach (Cart::content() as  $item) {
                    $orderItem->product_id = $item->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price*$item->qty;
                    $orderItem->save();
                }
                session()->flash('success','You have successfully placed your Order.');
                Cart::destroy();
                return response()->json([
                    'status' => true,
                    'orderId' => $order->id,
                    'message' => 'Order Save Successfully'
                ]);
            }

        }

    }
    public function thankYou($id){
        return view('front.thankyou',[
            'id' => $id
        ]);
    }
}
