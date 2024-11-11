<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menampilkan semua item
        return response()->json(Item::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi dan simpan item baru
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $item = Item::create(array_merge(
            $request->all(),
            ['created_by' => auth()->id()]
        ));
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Menampilkan detail item berdasarkan id
        return response()->json(Item::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Update data item
        $item = Item::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Menghapus item
        $item = Item::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    public function stockSummary()
    {
        $totalStock = Item::sum('quantity');
        $totalValue = Item::sum(\DB::raw('price * quantity'));
        $averagePrice = Item::average('price');

        return response()->json([
            'total_stock' => $totalStock,
            'total_value' => $totalValue,
            'average_price' => $averagePrice,
        ]);
    }

    public function lowStockItems($threshold = 5)
    {
        $items = Item::where('quantity', '<', $threshold)->get();
        return response()->json($items);
    }

    public function itemsByCategory($categoryId)
    {
        $items = Item::where('category_id', $categoryId)->get();
        return response()->json($items);
    }
    public function categorySummary()
    {
        $categories = \DB::table('categories')
            ->leftJoin('items', 'categories.id', '=', 'items.category_id')
            ->select(
                'categories.id',
                'categories.name',
                \DB::raw('COUNT(items.id) as item_count'),
                \DB::raw('SUM(items.price * items.quantity) as total_value'),
                \DB::raw('AVG(items.price) as average_price')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return response()->json($categories);
    }

    public function supplierSummary()
    {
        $suppliers = \DB::table('suppliers')
            ->leftJoin('items', 'suppliers.id', '=', 'items.supplier_id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                \DB::raw('COUNT(items.id) as item_count'),
                \DB::raw('SUM(items.price * items.quantity) as total_value')
            )
            ->groupBy('suppliers.id', 'suppliers.name')
            ->get();

        return response()->json($suppliers);
    }


    public function systemSummary()
    {
        $totalItems = Item::count();
        $totalValue = Item::sum(\DB::raw('price * quantity'));
        $totalCategories = \DB::table('categories')->count();
        $totalSuppliers = \DB::table('suppliers')->count();

        return response()->json([
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'total_categories' => $totalCategories,
            'total_suppliers' => $totalSuppliers,
        ]);
    }

}
