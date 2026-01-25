<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\SettingRequest;

class SettingsController extends Controller
{
    public function index(){
        $settings=Setting::first();
        return response()->json(['status'=>'success','data'=>$settings]);
    }

    public function update(SettingRequest $request){
        $settings=Setting::first() ?? new Setting();

        $settings->company_name=$request->company_name;
        $settings->address=$request->address;
        $settings->gst_number=$request->gst_number;
        $settings->invoice_prefix=$request->invoice_prefix;
        $settings->financial_year_start=$request->financial_year_start;
        $settings->financial_year_end=$request->financial_year_end;

        if($request->hasFile('logo')){
            $file=$request->file('logo');
            $path=$file->store('logos','public');
            $settings->logo=$path;
        }

        $settings->save();

        logActivity('SETTINGS_UPDATED','Company settings updated');
        return response()->json(['message'=>'Settings saved successfully','settings'=>$settings]);
    }
}
