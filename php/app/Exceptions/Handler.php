<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Database\QueryException;
use Predis\Connection\ConnectionException;

use App\Exceptions\{
    LogicException,
    FatalException
};

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $rendered = parent::render($request, $e);

        // 拦截数据库异常
        if ($e instanceof QueryException)
        {
            \Utils\Log::danger('数据库异常', [
                'Exception' => \Utils\Util::buildException($e)
            ]);
            return \Utils\Response::response(
                \Utils\ErrorCode::ERROR,
                '服务器异常',
                env('DEBUG') ? \Utils\Util::buildException($e) : []
            );
        }
        // 拦截Validator异常 
        elseif ($e instanceof ValidationException) {
            $errors = $e->errors();
            return \Utils\Response::response(
                \Utils\ErrorCode::VALIDATOR_ERROR,
                current($errors)[0],
                $e->errors()
            );
        }
        // 拦截Redis异常 
        elseif ($e instanceof ConnectionException) {
            \Utils\Log::danger('Redis数据库异常', [
                'Exception' => \Utils\Util::buildException($e)
            ]);
            return \Utils\Response::response(
                \Utils\ErrorCode::ERROR,
                '服务器异常',
                env('DEBUG') ? \Utils\Util::buildException($e) : []
            );
        } elseif($e instanceof HttpException) {
            return $rendered;
        }
        // 其他异常
        else {
            try {
                if (property_exists($e, 'payload'))
                {
                    $payload = $e->payload;
                } else {
                    $payload = [];
                }
                switch ($rendered->getStatusCode()) {
                    case 404:
                        return \Utils\Response::response(
                            \Utils\ErrorCode::NOT_FOUND,
                            $e->getMessage(),
                            $payload
                        );
                        break;
                    case 405:
                        return \Utils\Response::response(
                            \Utils\ErrorCode::NOT_ALLOW_METHOD,
                            '不支持该请求方式',
                            $payload
                        );
                        break;
                    case 500:
                        if ($e->getCode())
                        {
                            return \Utils\Response::response(
                                $e->getCode(), 
                                $e->getMessage(), 
                                $payload
                            );
                        } else {
                            \Utils\Log::warning(
                                $e->getMessage(), 
                                \Utils\Util::buildException($e)
                            );
                            return \Utils\Response::response(
                                \Utils\ErrorCode::DEBUG_ERROR,
                                $e->getMessage(),
                                \Utils\Util::buildException($e)
                            );
                        }
                        break;
                    default:
                        \Utils\Log::warning(
                            $e->getMessage(), 
                            \Utils\Util::buildException($e)
                        );
                        return \Utils\Response::response(
                            \Utils\ErrorCode::DEBUG_ERROR,
                            $e->getMessage(),
                            \Utils\Util::buildException($e)
                        );
                        break;
                }
            } catch(\Exception $e)
            {
                return \Utils\Response::response(\Utils\ErrorCode::ERROR, $e->getMessage(), $payload);
            }
        }
    }
}
