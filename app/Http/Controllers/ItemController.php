<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function getItem(Request $request)
    {
        $serial = $request->serial;

        $item = Item::where('serial', $serial)->first();
        if ($item) {
            return response()->json(['status' => 'success', 'msg' => '', 'data' => $item]);
        }

        return response()->json(['status' => 'danger', 'msg' => '', 'data' => null]);
    }
}
