<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
use Gloudemans\Shoppingcart\Facades\Cart;

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
                session()->flash('success', "<strong>".$product->tittle ."</strong>". ' added in Cart.');
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
            session()->flash('success', "<strong>".$product->tittle ."</strong>". ' added in Cart.');

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
}
