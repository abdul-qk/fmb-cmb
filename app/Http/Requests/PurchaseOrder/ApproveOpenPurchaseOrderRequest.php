<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\PurchaseOrderDetail;

class ApproveOpenPurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_vendor' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:1',
            'place_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'discount' => 'required|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.unit_price' => 'numeric',
            'items.*.unit_id' => 'integer',
            'items.*.purchase_order_detail_id' => 'required|integer|exists:purchase_order_details,id',
            'items.*.quantity' => [
            'required',
            'numeric',
            'gt:0',
            function ($attribute, $value, $fail) {
                preg_match('/items\.(\d+)\.quantity/', $attribute, $matches);
                $index = $matches[1] ?? null;
                if (is_null($index) || !request()->has("items.$index.purchase_order_detail_id")) {
                    return $fail('Invalid item data.');
                }
                $purchaseOrderDetailId = request("items.$index.purchase_order_detail_id");
                $purchaseOrderDetail = PurchaseOrderDetail::with('item')->find($purchaseOrderDetailId);
                if ($purchaseOrderDetail && $value > $purchaseOrderDetail->quantity) {
                    $fail('The quantity for ' . $purchaseOrderDetail->item->name . ' must not be greater than the quantity in the purchase order.');
                }
            },
        ],
            'items.*.total' => 'numeric|min:1',
        ];
    }
}
