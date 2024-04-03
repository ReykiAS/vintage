<?php
namespace App\Traits;

trait Searchable
{
     public function scopeFilter($query, $request)
    {
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        if ($request->has('sort_by')) {
            $sortOrder = $request->has('sort_order') ? $request->sort_order : 'asc';
            $query->orderBy($request->sort_by, $sortOrder);
        }

        return $query;
    }
}
