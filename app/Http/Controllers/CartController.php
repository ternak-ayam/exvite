<?php

namespace App\Http\Controllers;

use Lang;
use Auth;
use App\Models\Subscription;
use App\Models\Cart;
use App\Models\Jasa;
use App\Models\OrderDetails;
use App\Models\OrderJasa;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index() {
        $balance = WalletController::index()->balance;
        $jasa = Cart::with('user', 'jasa.seller', 'plan')->where('user_id', Auth::user()->id)->get();
        return view('buyer.cart', ['balance' => $balance, 'data'=> $jasa]);
        // return response()->json($jasa);
    }
    public function cart_data() {
        $jasa = Cart::where('user_id', Auth::user()->id)->get();
        return response()->json($jasa);
    }

    public function delete(Request $request) {
        Cart::whereIn('cart_id', explode(",", $request->id))->delete();
        return response()->json(['status' => Lang::get('validation.cart.delete.success')]);
    }
    public function update(Request $request) {
        //
    }
    public function finish(Request $request) {
        if(empty($request->cart_id)) {
            return response()->json(['status' => Lang::get('validation.cart.next.failed'), 'code' => 401]);
        } else {
            $carts = array();
            foreach($request->cart_id as $c) {
                $type = Cart::where('cart_id', $c)->first();
                $order_id = date('hi').rand();
                switch($type->product_type) {
                    case "Jasa" :
                        $p = Cart::with('user', 'jasa.seller')->where('cart_id', $type->cart_id)->first();
                        $carts[] = array(
                        'id'=>$p->cart_id,
                        'name' => $p->jasa->jasa_name,
                        'price' => $p->unit_price,
                        'picture' => $p->jasa->jasa_thumbnail,
                        'quantity' => $p->quantity,
                        'category' => $p->jasa->jasa_subcategory,
                        'type' => $p->product_type,
                        'note' => $p->note,
                        'deadline' => $p->deadline,
                        'example' => $p->example,
                        'example_ori' => $p->example_ori,
                    );
                    break;

                    case "Subscription" :
                        $p = Cart::with('plan')->where('cart_id', $type->cart_id)->first();
                        $carts[] = array(
                        'id'=>$p->cart_id,
                        'name' => $p->plan->plan_name,
                        'price' => $p->unit_price,
                        'picture' => 'https://assets.exova.id/img/1.png',
                        'quantity' => $p->quantity,
                        'category' => 'Membership',
                        'type' => $p->product_type,
                        'note' => $p->note,
                    );
                    break;

                    case "Create" :
                        // Belum selesai
                        $p = Cart::with('user', 'jasa.seller')->where('cart_id', $type->cart_id)->first();
                        $carts[] = array(
                        'id'=>$p->cart_id,
                        'name' => $p->jasa->jasa_name,
                        'price' => $p->unit_price,
                        'picture' => $p->jasa->jasa_thumbnail,
                        'quantity' => $p->quantity,
                        'category' => $p->jasa->jasa_subcategory,
                        'type' => $p->product_type,
                        'note' => $p->note,
                    );
                    break;
                }
            }
            return $request->session()->put('cart_shopping', $carts);
        }
    }
    public function tes_session(Request $request) {
        $data = $request->session()->get('cart_shopping');
        return response()->json($data);
    }
    public function data($type, $id) {
        Cart::where('product_type', $type)->whereIn('cart_id', explode(",", $id))->delete();
        return response()->json(['status' => Lang::get('validation.cart.delete.success')]);
    }

    public function add(Request $request) {
        switch($request->type) {
            case "Subscription":
                $check = Cart::where('user_id', Auth::user()->id)->where('product_type', $request->type)->first();
                if(!empty($check)) {
                    return response()->json(['status' => Lang::get('validation.cart.add.limit'), 'code' => 400]);
                }

                $data = Subscription::where('plan_id', $request->id)->first();
                Cart::create([
                    'product_id' => $data->plan_id,
                    'user_id' => Auth::user()->id,
                    'product_type' => $request->type,
                    'unit_price' => $data->price_per_year,
                    'quantity' => 1,
                    'note' => Lang::get('validation.cart.note.membership'),
                ]);
                break;
                //
        }
        return response()->json(['status' => Lang::get('validation.cart.add.success')]);
    }

    public function products(Request $request) {
        $data = Cart::where('plan_id', $request->id)->first();
            $products = array([
                'id'=>$data->plan_id,
                'name' => $data->plan_name,
                'price' => $data->price_per_year,
                'picture' => '',
                'quantity' => 1,
                'category' => 'Subscription',
                'type' => 'Subscription',
                'note' => '',
                ]);
        $request->session()->put('cart_shopping', $products);
        // $data = array();
        // $products = array();
        // foreach($products as $d) {
        //     $data[] = Subscription::where('plan_id', $d['id'])->get();
        // }
        // $products = array();
        // foreach($data as $d) {
        //     $products[] = array(
        //         'name' => $d->plan_name,
        //         'price' => $d->price_per_year,
        //         'quantity' => 1,
        //         'category' => 'a',
        //         'type' => '',
        //     );
        // }
        // if(empty($data)) {
        //     $request->session()->put('cart_shopping', $products);
        //         return response()->json(['status' => 'Ditambahkan']);
        //     } else {
        //             $array_keys = array_keys($request->session()->get('cart_shopping'));
        //             if(in_array($key, $array_keys)) {
        //                 return response()->json(['status' => 'Produk sudah ada']);
        //             } else {
        //                 $request->session()->put('cart_shopping', array_merge($request->session()->get('cart_shopping'), $cart));
        //             }
        //         return response()->json(['status' => 'Ditambahkan']);
        //         $request->session()->flush();
        //     }
        // $data = $request->session()->get('cart_shopping');
        // return response()->json($products);
        return redirect('buyer.payments');
    }
}
