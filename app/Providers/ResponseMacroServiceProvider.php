<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data = null, string $message = 'Success') {
            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $data,
            ], HttpResponse::HTTP_OK);
        });
        Response::macro('error', function (
            string $message = 'Internal Server Error',
            $errorCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
            array $errors = []
        ) {
            $data = [
                'status' => false,
                'message' => $message,
            ];

            if (!empty($errors)) {
                $data['errors'] = $errors;
            }

            if ($errorCode === 0 || is_string($errorCode)) {
                $errorCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json($data, $errorCode);
        });
    }
}
