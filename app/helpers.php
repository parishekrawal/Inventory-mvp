<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

function logActivity($action,$description=null){
    ActivityLog::create([
        'user_id'=>Auth::id(),
        'action'=>$action,
        'description'=>$description
    ]);
}