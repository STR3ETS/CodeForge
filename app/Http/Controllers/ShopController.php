<?php

namespace App\Http\Controllers;

use App\Models\ShopBundle;
use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = ShopItem::where('active', true)
            ->orderByRaw("FIELD(rarity, 'legendary', 'epic', 'rare', 'common')")
            ->orderBy('price')
            ->get();

        $ownedIds = $user->cosmetics()->pluck('shop_items.id')->toArray();
        $equippedIds = $user->equippedCosmetics()->pluck('shop_items.id')->toArray();

        // Daily featured bundle — rotate based on day of year
        $allBundles = ShopBundle::where('active', true)->with('items')->get();
        $featuredBundle = $allBundles->count()
            ? $allBundles->values()->get(now()->dayOfYear % $allBundles->count())
            : null;

        return view('dashboard.shop', [
            'user' => $user,
            'items' => $items,
            'ownedIds' => $ownedIds,
            'equippedIds' => $equippedIds,
            'featuredBundle' => $featuredBundle,
        ]);
    }

    public function buy(Request $request)
    {
        $user = $request->user();
        $item = ShopItem::where('active', true)->findOrFail($request->input('item_id'));

        // Pro-only check for epic & legendary
        if (in_array($item->rarity, ['epic', 'legendary']) && ($user->plan ?? 'free') !== 'pro') {
            return response()->json(['ok' => false, 'error' => 'Dit item is alleen voor Pro-leden.'], 422);
        }

        // Already owned
        if ($user->cosmetics()->where('shop_item_id', $item->id)->exists()) {
            return response()->json(['ok' => false, 'error' => 'Je hebt dit item al.'], 422);
        }

        // Level check
        if ($user->level < $item->level_required) {
            return response()->json(['ok' => false, 'error' => "Je moet level {$item->level_required} zijn."], 422);
        }

        // Coins check
        if ($user->coins < $item->price) {
            return response()->json(['ok' => false, 'error' => 'Niet genoeg coins.'], 422);
        }

        $user->coins -= $item->price;
        $user->save();

        $user->cosmetics()->attach($item->id, ['equipped' => false]);

        return response()->json([
            'ok' => true,
            'coins' => $user->coins,
        ]);
    }

    public function buyBundle(Request $request)
    {
        $user = $request->user();
        $bundle = ShopBundle::where('active', true)->with('items')->findOrFail($request->input('bundle_id'));

        // Pro-only check for epic & legendary bundles
        if (in_array($bundle->rarity, ['epic', 'legendary']) && ($user->plan ?? 'free') !== 'pro') {
            return response()->json(['ok' => false, 'error' => 'Dit pakket is alleen voor Pro-leden.'], 422);
        }

        // Level check
        if ($user->level < $bundle->level_required) {
            return response()->json(['ok' => false, 'error' => "Je moet level {$bundle->level_required} zijn."], 422);
        }

        // Filter out already owned items
        $ownedIds = $user->cosmetics()->pluck('shop_items.id')->toArray();
        $newItems = $bundle->items->filter(fn($item) => !in_array($item->id, $ownedIds));

        if ($newItems->isEmpty()) {
            return response()->json(['ok' => false, 'error' => 'Je hebt alle items uit dit pakket al.'], 422);
        }

        // Calculate price: bundle price proportional to items not yet owned
        $totalItems = $bundle->items->count();
        $price = (int) round($bundle->price * ($newItems->count() / $totalItems));

        if ($user->coins < $price) {
            return response()->json(['ok' => false, 'error' => 'Niet genoeg coins.'], 422);
        }

        $user->coins -= $price;
        $user->save();

        foreach ($newItems as $item) {
            $user->cosmetics()->attach($item->id, ['equipped' => false]);
        }

        return response()->json([
            'ok' => true,
            'coins' => $user->coins,
            'newItemIds' => $newItems->pluck('id')->values()->toArray(),
        ]);
    }

    public function equip(Request $request)
    {
        $user = $request->user();
        $item = ShopItem::findOrFail($request->input('item_id'));

        // Must own it
        if (!$user->cosmetics()->where('shop_item_id', $item->id)->exists()) {
            return response()->json(['ok' => false, 'error' => 'Je hebt dit item niet.'], 422);
        }

        // Custom badge flairs: store JSON with text, emoji, and color
        $pivotData = ['equipped' => true];
        if ($item->type === 'badge_flair' && str_contains($item->slug, 'custom')) {
            $customText = trim((string) $request->input('custom_text', ''));
            if ($customText === '') {
                return response()->json(['ok' => false, 'error' => 'Vul een tekst in.'], 422);
            }
            if (mb_strlen($customText) > 20) {
                return response()->json(['ok' => false, 'error' => 'Max 20 tekens.'], 422);
            }

            $allowedColors = ['red', 'orange', 'yellow', 'green', 'emerald', 'cyan', 'blue', 'indigo', 'purple', 'pink', 'slate', 'rainbow'];
            $color = $request->input('custom_color', 'slate');
            if (!in_array($color, $allowedColors)) {
                $color = 'slate';
            }

            $emoji = trim((string) $request->input('custom_emoji', '✦'));
            if (mb_strlen($emoji) > 4) {
                $emoji = '✦';
            }

            $pivotData['custom_value'] = json_encode([
                'text' => $customText,
                'emoji' => $emoji,
                'color' => $color,
            ]);
        }

        // Unequip all of same type, then equip this one
        $sameTypeIds = ShopItem::where('type', $item->type)->pluck('id');
        $user->cosmetics()->updateExistingPivot($sameTypeIds->toArray(), ['equipped' => false]);
        $user->cosmetics()->updateExistingPivot($item->id, $pivotData);

        return response()->json(['ok' => true]);
    }

    public function unequip(Request $request)
    {
        $user = $request->user();
        $item = ShopItem::findOrFail($request->input('item_id'));

        $user->cosmetics()->updateExistingPivot($item->id, ['equipped' => false]);

        return response()->json(['ok' => true]);
    }
}
