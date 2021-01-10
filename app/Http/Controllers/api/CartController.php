<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Http\Resources\Cart as CartResource;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        //Retreive Request Data
        $qty = $request->input('qty');

        //Retreive Product data From DataBase By Requested Id
        $product = Product::find($id);
        $product_id = $product->id;
        $product_name = $product->description;
        $price = $product->price;
        $options = $product->images_urls;

        //Calculation
        $tax = $price * (env('TAX') / 100);
        $subtotal = ($price * $qty) + $tax;
        $uniqueId = Str::uuid();

        //Add Results To An Array
        $item = [
            'row_id' => $uniqueId,
            'product_id' => $product_id,
            'name' => $product_name,
            'qty' => $qty,
            'price' => $price,
            'options' => $options,
            'tax' => $tax,
            'subtotal' => $subtotal
        ];

        //Retreive Discount Data From DataBase
        $discounts = Discount::where('discount_code', 'TestDevinweb')->first();

        // Assuming That The Cart Id ($cartId) Is Belongs To The Customer
        $cartId = 36;

        //If The Cart Id Exists, Then Add The Product To The Cart Items
        if ($cart = Cart::find($cartId)) {

            $total = 0;
            foreach ($cart->content as $items) {
                $total += $items['price'];
            }

            $discount = [
                'code' => $discounts->discount_code,
                'discounted_amount' => ($total + $price) * ($discounts->percentage_value / 100),
                'value' => $discounts->percentage_value,
            ];

            //Add The New Item array To The Existing Cart Items
            $items = Arr::prepend($cart->content, $item);

            //Update The Cart
            $cart->content = $items;
            $cart->discount = $discount;
            $cart->save();
            return new CartResource($cart);
        }

        //If The Cart Id Does Not Exist, Then Create New Cart And Add The Product To It
        else {

            //Create A Discount Array
            $discount = [
                'code' => $discounts->discount_code,
                'discounted_amount' => $price * ($discounts->percentage_value / 100),
                'value' => $discounts->percentage_value,
            ];

            //Add The New Item array To The New Cart Items
            $items = [$item];

            //Create The Record
            $cart = new Cart();
            $cart->content = $items;
            $cart->discount = $discount;
            $cart->save();
            return new CartResource($cart);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Retreive specific product record by id
        $cart = Cart::find($id);
        return new CartResource($cart);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Retreive Request Data
        $row_id = $request->input('row_id');
        $qty = $request->input('qty');

        //Retreive Product data From DataBase By Requested Id
        $product = Product::find($id);
        $product_id = $product->id;
        $product_name = $product->description;
        $price = $product->price;
        $options = $product->images_urls;

        //Calculation
        $tax = $price * (env('TAX') / 100);
        $subtotal = ($price * $qty) + $tax;
        $uniqueId = $row_id;

        //Add Results To An Array
        $item = [
            'row_id' => $uniqueId,
            'product_id' => $product_id,
            'name' => $product_name,
            'qty' => $qty,
            'price' => $price,
            'options' => $options,
            'tax' => $tax,
            'subtotal' => $subtotal
        ];

        $cartId = 36;
        $cart = Cart::find($cartId);

        //Remove Product If Exists In Cart Items
        $collection = new Collection();
        foreach ($cart->content as $items) {
            if (($items['product_id'] != $row_id) && ($items['product_id'] != $id)) {
                $collection->push((object)$items);
            }
        }

        $total = 0;
        foreach ($cart->content as $items) {
            $total += $items['price'];
        }

        $discounts = Discount::where('discount_code', 'TestDevinweb')->first();
        $discount = [
            'code' => $discounts->discount_code,
            'discounted_amount' => ($total + $price) * ($discounts->percentage_value / 100),
            'value' => $discounts->percentage_value,
        ];

        $items = $collection->push((object)$item);

        //Update The Cart
        $cart->content = $items;
        $cart->discount = $discount;
        $cart->save();
        return new CartResource($cart);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $cartId = 36;
        $cart = Cart::find($cartId);
        //Remove Product If Exists In Cart Items
        $collection = new Collection();
        foreach ($cart->content as $items) {
            if ($items['product_id'] != $id) {
                $collection->push((object)$items);
            }
        }

        //Update The Cart
        $cart->content = $collection;
        $cart->save();
        return new CartResource($cart);
    }
}
