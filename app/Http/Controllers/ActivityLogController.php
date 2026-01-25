<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(){

        $data=ActivityLog::latest()->take(10)->get();
        
        return response()->json([
            'data'=>$data
        ]);
    }
}
