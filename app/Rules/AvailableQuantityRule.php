<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Product;
use Illuminate\Http\Request;

class AvailableQuantityRule implements Rule
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function passes($attribute, $value): bool
    {
        $productId = $value;
        $quantity = $this->request->get('qty', 1); // Assuming quantity is in the request

        $product = Product::find($productId);

        if (!$product || $product->quantity < $quantity) {
            return false; // Insufficient quantity
        }

        return true; // Quantity is available
    }

    public function message(): string
    {
        return 'Insufficient product quantity available.';
    }
}
