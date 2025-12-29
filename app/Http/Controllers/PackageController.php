<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DeliveryDate;
use App\Models\Item;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    // Route: packages.index
    public function index(Request $request)
    {
        $packages = Package::all();
        $packagesCount = Package::count();

        $activePackages = Package::where('is_active', true)->get();
        $activePackagesCount = Package::where('is_active', true)->count();

        $activeDeliveryDates = DeliveryDate::where('is_active', true)->get();
        $activeDeliveryDatesCount = DeliveryDate::where('is_active', true)->count();

        return view('admin.supplyManagement', compact('packages', 'activePackages', 'activeDeliveryDates', 'packagesCount', 'activePackagesCount', 'activeDeliveryDatesCount'));
    }

    public function managePackage(Request $request)
    {
        $activeTab = $request->get('tab', session('active_tab', 'addNewPackage'));
        $packages = Package::all();
        $existingItems = Item::withSum('packages as total_quantity', 'package_items.quantity');

        $sort = $request->get('sort');
        $order = $request->get('order', 'asc');

        if ($sort === 'price') {
            $existingItems->orderBy('estimated_price', $order);
        }

        if ($sort === 'quantity') {
            $existingItems->orderBy('total_quantity', $order);
        }

        $existingItems = $existingItems->get();

        // Top 3 most expensive items
        $topThreeExpensive = Item::orderByDesc('estimated_price')->take(3)->get();

        // Top 3 high used items
        $topHighUsedItem = Item::withSum('packages as total_quantity_used', 'package_items.quantity')
            ->orderBy('total_quantity_used', 'desc')
            ->take(3)
            ->get();

        // Top 3 rare used items
        $topRareUsedItem = Item::withSum('packages as total_quantity_used', 'package_items.quantity')
            ->orderBy('total_quantity_used', 'asc')
            ->take(3)
            ->get();

        return view('admin.packageManagement', compact(
            'packages',
            'existingItems',
            'activeTab',
            'topThreeExpensive',
            'topHighUsedItem',
            'topRareUsedItem'
        ));
    }


    // Route: packages.store (Add new packages)
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'description' => 'required|string',
            'attach_items.*.item_id' => 'nullable|exists:items,id',
            'attach_items.*.quantity' => 'nullable|numeric|min:1',
            'new_items.*.name' => 'nullable|string|max:255',
            'new_items.*.quantity' => 'nullable|numeric|min:1',
            'new_items.*.price' => 'nullable|numeric|min:0',
        ]);


        // Create package
        $package = Package::create([
            'name' => $request->package_name,
            'description' => $request->description,
        ]);

        // Attach existing items
        if ($request->has('attach_items')) {
            foreach ($request->input('attach_items') as $item) {
                if (!empty($item['item_id']) && !empty($item['quantity'])) {
                    $package->items()->attach($item['item_id'], ['quantity' => $item['quantity']]);
                }
            }
        }

        // Attach new items
        if ($request->has('new_items')) {
            foreach ($request->new_items as $item) {
                if (!empty($item['name']) && !empty($item['quantity'])) {

                    $existingItem = Item::where('name', $item['name'])->first();

                    if ($existingItem) {
                        // Update the price if admin provides it
                        if (!empty($item['price'])) {
                            $existingItem->estimated_price = $item['price'];
                            $existingItem->save();
                        }
                    } else {
                        // Create a new item
                        $existingItem = Item::create([
                            'name' => $item['name'],
                            'estimated_price' => $item['price']
                        ]);
                    }

                    // Attach item to package
                    $package->items()->syncWithoutDetaching([
                        $existingItem->id => ['quantity' => $item['quantity']]
                    ]);
                }
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'package',
            'message' => "Package '{$package->name}' successfully created.",
        ]);

        return redirect()->route('admin.packages')
            ->with('success', 'Package created successfully!');
    }

    // Return back data in json format (Edit existing packages)
    public function json(Package $package)
    {
        $package->load('items'); // eager load items
        $packagePrice = 0;

        $items = $package->items->map(function ($item) {

            $qty = $item->pivot->quantity ?? 1;
            $subtotal = $item->estimated_price * $qty;

            return [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $qty,
                'subtotal' => number_format($subtotal, 2, '.', '')
            ];
        });
        $packagePrice = $items->sum('subtotal');

        // use for ajax 
        return response()->json([
            'description' => $package->description,
            'id' => $package->id,
            'name' => $package->name,
            'items' => $items,
            'packagePrice' => $packagePrice
        ]);
    }

    // Route: packages.updateItems (Edit existing packages)
    public function updateItems(Request $request)
    {

        $request->validate([
            'description' => 'nullable|string',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|numeric|min:1',
            'attach_items.*.item_id' => 'nullable|exists:items,id',
            'attach_items.*.quantity' => 'nullable|numeric|min:1',
            'new_items.*.name' => 'nullable|string|max:255',
            'new_items.*.quantity' => 'nullable|numeric|min:1',
            'new_items.*.price' => 'nullable|numeric|min:0',
        ]);

        $targetPackage = Package::findOrFail($request->package_id);

        if ($request->has('description')) {
            $targetPackage->update([
                'description' => $request->description
            ]);
        }

        if ($request->has('quantities')) {
            foreach ($request->input('quantities') as $itemId => $quantity) {
                if (!empty($quantity)) {
                    $targetPackage->items()->updateExistingPivot($itemId, [
                        'quantity' => $quantity,
                    ]);
                }
            }
        }

        // Attach existing items
        if ($request->has('attach_items')) {
            foreach ($request->input('attach_items') as $item) {
                if (!empty($item['item_id']) && !empty($item['quantity'])) {
                    $targetPackage->items()->syncWithoutDetaching([
                        $item['item_id'] => ['quantity' => $item['quantity']]
                    ]);
                }
            }
        }

        // Attach new items
        if ($request->has('new_items')) {
            foreach ($request->input('new_items') as $item) {
                if (!empty($item['name']) && !empty($item['quantity'])) {

                    $existingItem = Item::where('name', $item['name'])->first();

                    if ($existingItem) {
                        // Update the price if admin provides it
                        if (!empty($item['price'])) {
                            $existingItem->estimated_price = $item['price'];
                            $existingItem->save();
                        }
                    } else {
                        // Create a new item
                        $existingItem = Item::create([
                            'name' => $item['name'],
                            'estimated_price' => $item['price']
                        ]);
                    }

                    // Attach item to package
                    $targetPackage->items()->syncWithoutDetaching([
                        $existingItem->id => ['quantity' => $item['quantity']]
                    ]);
                }
            }
        }

        if ($request->has('remove_items')) {
            foreach ($request->input('remove_items') as $item) {
                $targetPackage->items()->detach($item);
            }
        }

        $addedItems = $request->input('new_items') ? count($request->input('new_items')) : 0;
        $removedItems = $request->input('remove_items') ? count($request->input('remove_items')) : 0;
        $updatedQty = $request->input('quantities') ? count($request->input('quantities')) : 0;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'package',
            'message' => "Package '{$targetPackage->name}' updated. Added: {$addedItems}, Removed: {$removedItems}, Quantities updated: {$updatedQty}.",
        ]);


        return redirect()
            ->route('admin.packages', ['tab' => 'editExistPackage'])
            ->with('success', 'Package updated successfully!');
    }

    // Route: item.delete
    public function delete(Request $request)
    {
        $item = Item::find($request->existingItem_delete);

        if ($item) {
            $item->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }


    // Route: packages.destroy
    public function destroy(Request $request)
    {
        $package = Package::findOrFail($request->package_id);

        // detach items first if you use many-to-many relation
        $package->items()->detach();

        $package->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'package',
            'message' => "Package '{$package->name}' deleted along with all its items.",
        ]);

        return redirect()
            ->route('admin.packages', ['tab' => 'editExistPackage'])
            ->with('success', 'Package deleted successfully!');
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'updated_price' => 'required|numeric|min:0',
        ]);

        $item = Item::findOrFail($id);
        $item->estimated_price = $request->input('updated_price');
        $item->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'item',
            'message' => "Price for item '{$item->name}' updated to {$item->estimated_price}.",
        ]);


        return redirect()->back()->with('success', 'Price updated successfully!');
    }
}
