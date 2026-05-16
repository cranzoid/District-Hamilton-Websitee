<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart contents page.
     */
    public function index()
    {
        // Get the cart from the session
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0;
        
        // Process cart items to include full details
        foreach ($cart as $id => $details) {
            $menuItem = MenuItem::with(['category', 'addOns'])->find($details['item_id']);
            
            if ($menuItem && $menuItem->is_available) {
                $itemTotal = $details['price'] * $details['quantity'];
                
                // Add selected add-ons to the item if any
                if (isset($details['add_ons']) && count($details['add_ons']) > 0) {
                    $selectedAddOns = AddOn::whereIn('id', $details['add_ons'])->get();
                    
                    // Calculate the add-ons total
                    $addOnsTotal = $selectedAddOns->sum('price') * $details['quantity'];
                    $itemTotal += $addOnsTotal;
                    
                    // Add add-on details to the item
                    $details['selected_add_ons'] = $selectedAddOns;
                } else {
                    $details['selected_add_ons'] = collect([]);
                }
                
                // Store the item details
                $details['menu_item'] = $menuItem;
                $details['item_total'] = $itemTotal;
                $details['image_path'] = 'storage/' . $menuItem->image_path;
                
                // Add to the cart items collection
                $items[$id] = $details;
                
                // Add to the subtotal
                $subtotal += $itemTotal;
            }
        }
        
        return view('cart.index', compact('items', 'subtotal'));
    }
    
    /**
     * Add an item to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'add_ons' => 'nullable|array',
            'add_ons.*' => 'exists:add_ons,id',
            'special_instructions' => 'nullable|string|max:255',
        ]);

        $menuItem = MenuItem::with(['addOns', 'category'])->findOrFail($request->item_id);

        if (!$menuItem->is_available) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This item is currently not available.'], 400);
            }
            return redirect()->back()->with('error', 'This item is currently not available.');
        }

        // Drinks are view-only — not available for online ordering
        if ($menuItem->category && $menuItem->category->section === \App\Models\Category::SECTION_DRINK) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Drinks are not available for online ordering.'], 400);
            }
            return redirect()->back()->with('error', 'Drinks are not available for online ordering.');
        }

        $price = $menuItem->price;
        $cart  = session()->get('cart', []);

        // Normalize add-ons and instructions for deduplication comparison
        $addOns = $request->add_ons ?? [];
        sort($addOns);
        $instructions = trim($request->special_instructions ?? '');

        // Find an existing cart slot with identical item + add-ons + instructions
        $existingKey = null;
        foreach ($cart as $key => $cartItem) {
            $existingAddOns = $cartItem['add_ons'] ?? [];
            sort($existingAddOns);
            if (
                $cartItem['item_id'] === $menuItem->id &&
                $existingAddOns === $addOns &&
                trim($cartItem['special_instructions'] ?? '') === $instructions
            ) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $cart[$existingKey]['quantity'] += $request->quantity;
        } else {
            $cartItemId = uniqid();
            $cart[$cartItemId] = [
                'item_id'              => $menuItem->id,
                'name'                 => $menuItem->name,
                'price'                => $price,
                'quantity'             => $request->quantity,
                'add_ons'              => $addOns,
                'special_instructions' => $instructions,
                'image_path'           => 'storage/' . $menuItem->image_path,
            ];
        }

        session()->put('cart', $cart);

        $totalQty = array_sum(array_column($cart, 'quantity'));

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Item added to cart successfully!',
                'cart_count' => $totalQty,
                'item'       => [
                    'name'     => $menuItem->name,
                    'price'    => $price,
                    'quantity' => $request->quantity,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Item added to cart successfully!');
    }
    
    /**
     * Update a cart item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string', // This is the cart item ID, not the menu item ID
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Get the cart from the session
        $cart = session()->get('cart', []);
        
        // Check if the item exists in the cart
        if (isset($cart[$request->item_id])) {
            // Update the quantity
            $cart[$request->item_id]['quantity'] = $request->quantity;
            
            // Update the session
            session()->put('cart', $cart);
            
            // Return JSON response if requested
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully!',
                    'cart_count' => count($cart)
                ]);
            }
            
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.'
            ], 404);
        }
        
        return redirect()->back()->with('error', 'Item not found in cart.');
    }
    
    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string', // This is the cart item ID, not the menu item ID
        ]);
        
        // Get the cart from the session
        $cart = session()->get('cart', []);
        
        // Check if the item exists in the cart
        if (isset($cart[$request->item_id])) {
            // Remove the item from the cart
            unset($cart[$request->item_id]);
            
            // Update the session
            session()->put('cart', $cart);
            
            // Return JSON response if requested
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart successfully!',
                    'cart_count' => count($cart)
                ]);
            }
            
            return redirect()->back()->with('success', 'Item removed from cart successfully!');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.'
            ], 404);
        }
        
        return redirect()->back()->with('error', 'Item not found in cart.');
    }
    
    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        // Clear the cart in the session
        session()->forget('cart');
        
        // Return JSON response if requested
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!'
            ]);
        }
        
        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }
} 