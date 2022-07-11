<?php

namespace App\Exceptions;

use Exception;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $msg = "";
        if($exception instanceof NotFoundHttpException)
        {
            $msg .= "Status Code: 404\n";
        }
        $method = $request->method();
        $url = $request->fullUrl();
        $agent = $request->header("User-Agent");
        $authorization = $request->header("Authorization");
        $ip = $request->ip();
        $msg .= "Method: ".$method."\n";
        $msg .= "URL: ".$url."\n";
        $msg .= "User-Agent: ".$agent."\n";
        $msg .= "Authorization: ".$authorization."\n";
        $msg .= "IP: ".$ip."\n";
        $input = $request->all();
        $msg .= "Data: ".json_encode($input)."\n";
        $referrer = $request->headers->get("referer");
        $msg .= "Referrer: ".$referrer."\n";
        $msg .= "Message: ".$exception->getMessage()."\n";
        $msg .= "Time: ".date("Y-m-d H:i:s")."\n";

        // $response = Curl::to('https://api.flock.com/hooks/sendMessage/9b4160ac-f4fe-4fe0-a750-bc786eb9d9d0')
        //             ->withData( array( 'text' => $msg ) )
        //             ->asJson()
        //             ->post();

        // $response = Curl::to('https://hooks.slack.com/services/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')
        $response = Curl::to('https://chat.googleapis.com/v1/spaces/AAAACSCs7Gs/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=8zYMQyTK5aHtYJyxrIETCmnYTBu4k-QeJOOGUWZHWpI%3D')
                    ->withData( array( 'text' => $msg ) )
                    ->asJson()
                    ->post();
                    
        /*
         * Redirect if token mismatch error
         * Usually because user stayed on the same screen too long and their session expired
         */
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('frontend.auth.login');
        }

        /*
         * All instances of GeneralException redirect back with a flash message to show a bootstrap alert-error
         */
        if ($exception instanceof GeneralException) {
            return redirect()->back()->withInput()->withFlashDanger($exception->getMessage());
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('frontend.auth.login'));
    }
}
