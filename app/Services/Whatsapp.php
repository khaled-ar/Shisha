<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Cache,
    Http,
    Log
};

class Whatsapp {

    public static function send_code($number) {
/*return json_decode(static::check_limit());
        $can_send_message = json_decode(static::check_limit())->data->can_send_message;
        if(! $can_send_message) {
            return false;
        }*/

        $code = substr(str_shuffle('0123456789'), 0, 5);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-key' => config('services.whatsapp_api_key')
        ])
        ->post('https://hypermsg.net/api/whatsapp/messages/send', [
            'whatsapp_number_id' => 24,
            'phone_number' => $number,
            'message' => "رمز التحقق الخاص بك هو: {$code} لا تشاركه مع احد، علما انه صالح لمدة خمس دقائق فقط",
            // 'raw' => "{\n    \"whatsapp_number_id\": 24, \n    \"phone_number\": \"{$number}\",\n    \"message\": \"رمز التحقق الخاص بك هو: {$code} لا تشاركه مع احد، علما انه صالح لمدة خمس دقائق فقط\"\n}"
        ]);

        if ($response->successful()) {
            Cache::put($number, $code, 60 * 5);
            return true;
        }
        Log::error($response->body());
        return false;
    }

    public static function check_limit() {

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'x-api-key' => config('services.whatsapp_api_key')
        ])
        ->get('https://hypermsg.net/api/whatsapp/messages/limits');

        return $response;
    }

}
