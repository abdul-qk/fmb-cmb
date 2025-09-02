<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\PurchaseOrderDetail;
use App\Models\Event;

class ApprovePurchaseOrderRequest extends FormRequest
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
            'current_vendor' => 'required|integer|exists:vendors,id',
            'amount' => 'required|numeric|min:1',
            'place_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'discount' => 'required|numeric|min:0',
            
            'items' => 'required|array|min:1',
            'items.*.event_id' => 'required|integer|exists:events,id',
            'items.*.item_id' => 'required|integer|exists:items,id',
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
                $event = Event::find(request("items.$index.event_id"));
                if ($purchaseOrderDetail && $value > $purchaseOrderDetail->quantity) {
                    $fail('The quantity of ' . $purchaseOrderDetail->item->name . ' for event ' . $event->name . ' must not be greater than the quantity in the purchase order.');
                }
            },
        ],
            'items.*.total' => 'numeric|min:1',
        ];
    }
}
