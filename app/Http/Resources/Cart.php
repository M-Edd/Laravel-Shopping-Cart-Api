<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Cart extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $wrap = 'cart';

    public function toArray($request)
    {
        if (!$this->discount) {
            return parent::toArray($request);
        } else {

            $discount_amount = $this->discount['discounted_amount'];

            $tax = 0;
            $total_amount = 0;
            foreach ($this->content as $items) {
                $tax += $items['tax'];
                $total_amount += $items['subtotal'];
            }

            $summary = [
                'discount_amount' => $discount_amount,
                'tax' => $tax,
                'total_amount' => $total_amount - $discount_amount,
            ];

            return [
                'id' => $this->id,
                'items' => $this->content,
                'discount' => $this->discount,
                'summary' => $summary,
            ];
        }
    }
}
