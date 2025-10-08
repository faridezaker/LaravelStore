<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = null, $message = '', $status = 200, $meta = null, $links = null) {
            $response = [
                'success' => true,
                'message' => $message,
                'data' => $data,
            ];

            if ($meta) {
                $response['meta'] = $meta;
            }

            if ($links) {
                $response['links'] = $links;
            }

            return response()->json($response, $status);
        });


        Response::macro("error",function ($data = null,$message = ' ',$status = 400){
            return Response::json([
                "success"=>false,
                "data"=>$data,
                "message"=>$message,
            ],$status);
        });
    }
}
