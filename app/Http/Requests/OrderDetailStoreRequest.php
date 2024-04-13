<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderDetailStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'address' => 'required|string',
            'delivery_details' => 'nullable|string',
            'payment_details' => 'nullable|string',
            'qty' => 'required_without:cart_id|integer|min:1',
            'cart_id' => 'required_without:product_id|exists:carts,id',
            'product_id' => 'required_without:cart_id|exists:products,id',
            'protection_fee' => 'required|numeric|min:0',
            'shipping_fee' => 'required|numeric|min:0',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'address.required' => 'Alamat pengiriman harus diisi.',
            'delivery_details.string' => 'Detail pengiriman harus berupa teks.',
            'payment_details.string' => 'Detail pembayaran harus berupa teks.',
            'qty.required_without' => 'Jumlah pesanan harus diisi jika tidak memesan dari cart.',
            'qty.integer' => 'Jumlah pesanan harus berupa bilangan bulat positif.',
            'qty.min' => 'Jumlah pesanan minimal adalah 1.',
            'cart_id.required_without' => 'ID cart harus diisi jika tidak memesan langsung produk.',
            'cart_id.exists' => 'Cart yang dipilih tidak valid.',
            'product_id.required_without' => 'Produk harus dipilih jika tidak memesan dari cart.',
            'product_id.exists' => 'Produk yang dipilih tidak valid.',
            'protection_fee.required' => 'Biaya perlindungan harus diisi.',
            'protection_fee.numeric' => 'Biaya perlindungan harus berupa angka.',
            'protection_fee.min' => 'Biaya perlindungan minimal adalah 0.',
            'shipping_fee.required' => 'Biaya pengiriman harus diisi.',
            'shipping_fee.numeric' => 'Biaya pengiriman harus berupa angka.',
            'shipping_fee.min' => 'Biaya pengiriman minimal adalah 0.',
        ];
    }
}
