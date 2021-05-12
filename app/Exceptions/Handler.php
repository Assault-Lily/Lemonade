<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        if(app()->isDownForMaintenance()){
            return response()->view('errors.maintenance',['exception' => $e],$status);
        }else{
            return response()->view('errors.error',['exception' => $e],$status);
        }
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (((int)($response->getStatusCode() / 100) === 5) and !config('app.debug')){
            $message = 'サーバエラーが発生しました'.PHP_EOL.$e->getMessage().PHP_EOL.$request->fullUrl();
            Log::channel('slack')->error($message);
        }

        return $response;
    }
}
