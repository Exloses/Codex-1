<?php

namespace App\Http\Requests\Storefront;

use App\Http\Requests\AuthorizedRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CartRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'product_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'custom_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->isMethod('post') || $validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = Product::query()
                    ->select(['id', 'stock', 'is_active'])
                    ->with(['variants:id,product_id,stock'])
                    ->find($this->integer('product_id'));

                if (! $product || ! $product->is_active) {
                    return;
                }

                $variantId = $this->filled('product_variant_id')
                    ? $this->integer('product_variant_id')
                    : null;

                if ($product->variants->isNotEmpty() && ! $variantId) {
                    $validator->errors()->add('product_variant_id', 'Please select a product variant before adding this item to cart.');

                    return;
                }

                if ($variantId) {
                    $variant = ProductVariant::query()
                        ->select(['id', 'product_id', 'stock'])
                        ->whereKey($variantId)
                        ->first();

                    if (! $variant || $variant->product_id !== $product->id) {
                        $validator->errors()->add('product_variant_id', 'The selected product variant is invalid for this product.');

                        return;
                    }

                    if ($this->integer('quantity') > $variant->stock) {
                        $validator->errors()->add('quantity', 'The requested quantity exceeds the selected variant stock.');
                    }

                    return;
                }

                if ($this->integer('quantity') > $product->stock) {
                    $validator->errors()->add('quantity', 'The requested quantity exceeds product stock.');
                }
            },
        ];
    }
}
