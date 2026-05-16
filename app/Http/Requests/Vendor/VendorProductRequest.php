<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Validator;

class VendorProductRequest extends AuthorizedRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_id' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'vendor_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.id' => ['nullable', 'integer', 'exists:product_attributes,id'],
            'attributes.*.name' => ['required_with:attributes', 'string', 'max:120'],
            'attributes.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'attributes.*.values' => ['nullable', 'array'],
            'attributes.*.values.*.id' => ['nullable', 'integer', 'exists:product_attribute_values,id'],
            'attributes.*.values.*.value' => ['required_with:attributes.*.values', 'string', 'max:120'],
            'attributes.*.values.*.color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'attributes.*.values.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.combination' => ['required_with:variants', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.vendor_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.image' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! is_array($this->input('variants')) || $validator->errors()->isNotEmpty()) {
                    return;
                }

                $seen = [];

                $attributeNames = collect($this->input('attributes', []))
                    ->pluck('name')
                    ->filter()
                    ->map(fn ($name) => (string) $name)
                    ->values();

                foreach ($this->input('variants', []) as $index => $variant) {
                    $combination = collect($variant['combination'] ?? [])
                        ->when($attributeNames->isNotEmpty(), fn ($values) => $values->only($attributeNames->all()))
                        ->filter(fn ($value, $key) => filled($key) && filled($value))
                        ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
                        ->sortKeys()
                        ->all();

                    if ($combination === []) {
                        $validator->errors()->add("variants.{$index}.combination", 'Each variant must include at least one option.');

                        continue;
                    }

                    $key = json_encode($combination);

                    if (isset($seen[$key])) {
                        $validator->errors()->add("variants.{$index}.combination", 'Variant combinations must be unique per product.');
                    }

                    $seen[$key] = true;
                }
            },
        ];
    }
}
