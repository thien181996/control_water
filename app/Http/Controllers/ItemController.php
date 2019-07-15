<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    const TANK_EMPTY = 0;
    const TANK_UNFULL = 1;
    const TANK_FULL = 2;
    const PUMP_ON = 1;
    const PUMP_OFF = 0;

    public function getItem($serial)
    {
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            return response()->json(['status' => 'success', 'msg' => '', 'data' => $item]);
        }

        return response()->json(['status' => 'danger', 'msg' => '', 'data' => null]);
    }

    // water_status: 1-full 0-empty
    // pump_status: 1-on 0-off
    // auto_status: 1-auto 0-manual
    // tank_status: 0-empty 1-unfull 2-full
    // tank water status(1:on,0:off)
    //   0    1     1
    //   0    0     0
    //   1    1     0
    //   1    0     0
    //   2    1     0
    //   2    0     0
    public function storeItem(Request $request)
    {
        $distance = (int)$request->distance;
        $water_status = $request->water_status;
        $serial = $request->serial;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->distance = $distance;
            $item->water_status = $water_status;
            $item->save();
//            dd($item);
        } else {
            $item = Item::create([
                'distance_max' => 0,
                'distance_min' => 0,
                'distance' => $distance,
                'water_status' => $water_status,
                'serial' => $serial,
            ]);
        }
        $distance_max = $item->distance_max;
        $distance_min = $item->distance_min;
        $tank_status = null;
        if ($item->auto_status) {
            if ($item->water_status && $distance >= $distance_max) {
                $item->pump_status = self::PUMP_ON;
                $item->save();
                return response()->json(self::PUMP_ON, 200);
            } else if ($distance <= $distance_min) {
                $item->pump_status = self::PUMP_OFF;
                $item->save();
                return response()->json(self::PUMP_OFF, 200);
            } else {
                return response()->json($item->pump_status, 200);
            }
        } else {
            return response()->json($item->pump_status, 200);
        }


    }

    public function saveItem(Request $request)
    {
//        return $request->all();
        $pump_status = $request->pump_status;
        $pump_status = $pump_status ? 1:0;
        $serial = $request->serial;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->auto_status = 0;
            $item->pump_status = $pump_status;
            $item->save();

            return response()->json(1, 200);
        }

        return response()->json(0, 200);
    }
}
