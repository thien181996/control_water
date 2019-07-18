<?php

namespace App\Http\Controllers;

use App\Events\ItemChanged;
use App\Item;
use Illuminate\Http\Request;
use Pusher\Pusher;
use ExponentPhpSDK;

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
//        $expo = ExponentPhpSDK\Expo::normalSetup();
        $distance = (int)$request->distance;
        $water_status = $request->water_status;
        $serial = $request->serial;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->distance = $distance;
            $item->water_status = $water_status;
            $item->save();
        } else {
            $item = Item::create([
                'distance_max' => 0,
                'distance_min' => 0,
                'distance' => $distance,
                'water_status' => $water_status,
                'serial' => $serial,
            ]);
        }

        event(new ItemChanged($item, $item->serial));
        $distance_max = $item->distance_max;
        $distance_min = $item->distance_min;
        $tank_status = null;
        if ($item->auto_status) {
            if ($item->water_status) {
                $item->warning_status = 0;
                if ($item->water_status && $distance >= $distance_max) {
                    $item->pump_status = self::PUMP_ON;
                    $item->save();
//                    $this->sendNotification($expo, $item->token, "Máy bơm đã được bật và đang bơm nước", "Chế độ: Tự động");
                    return response()->json(self::PUMP_ON, 200);
                } else if ($distance <= $distance_min) {
                    $item->pump_status = self::PUMP_OFF;
                    $item->save();
//                    $this->sendNotification($expo, $item->token, "Đã đầy nước máy bơm đã được tắt", "Chế độ: Tự động");
                    return response()->json(self::PUMP_OFF, 200);
                } else {
                    return response()->json($item->pump_status, 200);
                }
            }
            if ($item->warning_status) {
                return response()->json(self::PUMP_OFF, 200);
            } else {
                $item->warning_status = 1;
                $item->save();
//                $this->sendNotification($expo, $item->token, "Bể nguồn đã cạn nước", "Cảnh báo");
                return response()->json(self::PUMP_OFF, 200);
            }

        } else {
            if ($item->distance <= $distance_min) {
//                $this->sendNotification($expo, $item->token, "Đã đầy nước máy bơm đã được tắt và tự chuyển về chế độ tự động", "Chế độ: Thủ công");
                $item->auto_status = 1;
                $item->pump_status = self::PUMP_OFF;
                $item->save();
                return response()->json(self::PUMP_OFF, 200);
            }

            return response()->json($item->pump_status, 200);
        }
    }

    public function saveItem(Request $request)
    {
//        return $request->all();
        $pump_status = $request->pump_status ? 1:0;
        $auto_status = $request->auto_status ? 1:0;
        $distance_max = $request->distance_max;
        $distance_min = $request->distance_min;
        $serial = $request->serial;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->auto_status = $auto_status;
            $item->pump_status = $pump_status;
            $item->distance_max = $distance_max;
            $item->distance_min = $distance_min;
            $item->save();

            return response()->json(1, 200);
        }

        return response()->json(0, 200);
    }

    public function storeToken(Request $request)
    {
        $serial = $request->serial;
        $token = $request->token;
        $item = Item::where('serial', $serial)->first();
        if ($item) {
            $item->token = $token;
            $item->save();

            return response()->json(1, 200);
        }
        return response()->json(0, 200);
    }

    public function sendNotification($expo, $token, $body, $title)
    {
        $interestDetails = ['unique identifier', $token];
        $expo->subscribe($interestDetails[0], $interestDetails[1]);
        $notification = ['body' => $body,'title' => $title];
        $expo->notify($interestDetails[0], $notification);
    }
}
