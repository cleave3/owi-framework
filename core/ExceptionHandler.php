<?php

namespace App\core;

use Throwable;
use ErrorException;

class ExceptionHandler
{
    public function __construct()
    {
        set_exception_handler([$this, 'handle']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
            $this->handle(new ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }

    public function handle(Throwable $e)
    {
        $this->log($e);
        $this->render($e);
    }

    protected function log(Throwable $e)
    {
        $logMessage = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        error_log($logMessage, 3, __DIR__ . '/../../error.log');
    }

    protected function render(Throwable $e)
    {
        // Check for JSON request
        if ($this->isJsonRequest()) {
            $this->renderJson($e);
            return;
        }

        $debug = getenv('APP_DEBUG') === 'true';

        if ($debug) {
            $this->renderDebug($e);
        } else {
            $this->renderGeneric();
        }
    }

    protected function isJsonRequest()
    {
        return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

    protected function renderJson(Throwable $e)
    {
        http_response_code(500);
        $debug = getenv('APP_DEBUG') === 'true';

        $response = [
            'error' => true,
            'message' => $debug ? $e->getMessage() : 'Server Error',
        ];

        if ($debug) {
            $response['trace'] = explode("\n", $e->getTraceAsString());
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    protected function renderDebug(Throwable $e)
    {
        http_response_code(500);
        echo "<h1>" . get_class($e) . "</h1>";
        echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

    protected function renderGeneric()
    {
        http_response_code(500);
        echo "<h1>500 Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
}
