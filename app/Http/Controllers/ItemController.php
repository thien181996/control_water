<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemMoniter;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function getItem($serial)
    {
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            return response()->json(['status' => 'success', 'msg' => '', 'data' => $item]);
        }

        return response()->json(['status' => 'danger', 'msg' => '', 'data' => null]);
    }

    public function storeItem(Request $request)
    {
        $distance = $request->distance;
        $water_status = $request->water_status;
        $serial = $request->serial;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->distance = $distance;
            $item->water_status = $water_status;
            $item->save();
            return response()->json(['status' => 'success', 'msg' => 'Update success', 'data' => null]);
        }
        Item::create([
            'distance_max' => 0,
            'distance_min' => 0,
            'distance' => $distance,
            'water_status' => $water_status,
            'serial' => $serial,
        ]);

        return response()->json(['status' => 'success', 'msg' => 'Create success', 'data' => null]);
    }
}
