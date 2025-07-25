<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StreamerPageLayout;

class StreamerPageLayoutController extends Controller
{
    public function get(Request $request)
    {
        $userId = auth()->id();
        $streamerId = $request->input('streamer_id');
        $layout = StreamerPageLayout::where('user_id', $userId)
            ->where('streamer_id', $streamerId)
            ->first();
        return response()->json($layout ? $layout->layout : null);
    }

    public function save(Request $request)
    {
        $userId = auth()->id();
        $streamerId = $request->input('streamer_id');
        $layoutData = $request->input('layout');
        $layout = StreamerPageLayout::updateOrCreate(
            [
                'user_id' => $userId,
                'streamer_id' => $streamerId,
            ],
            [
                'layout' => $layoutData,
            ]
        );
        return response()->json(['success' => true]);
    }
}
