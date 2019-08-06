<?php

use Illuminate\Auth\Access\AuthorizationException;
use Carbon\Carbon;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\Debug\Exception\FatalThrowableError;

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the configuration path.
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->basePath() . '/app' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public path.
     * @param  string $path
     * @return string
     */
    function public_path($path = '')
    {
        return app()->basePath() . '/public' . ($path ? '/' . $path : $path);
    }

}

if (!function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     * @param  string $path
     * @param  bool $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('cache')) {
    /**
     * Get / set the specified cache value.
     * If an array is passed, we'll assume you want to put to the cache.
     * @param  dynamic  key|key,default|data,expiration|null
     * @return mixed
     * @throws Exception
     */
    function cache()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return app('cache');
        }

        if (is_string($arguments[0])) {
            return app('cache')->get($arguments[0], isset($arguments[1]) ? $arguments[1] : null);
        }

        if (!is_array($arguments[0])) {
            throw new Exception(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        if (!isset($arguments[1])) {
            throw new Exception(
                'You must specify an expiration time when setting a value in the cache.'
            );
        }

        return app('cache')->put(key($arguments[0]), reset($arguments[0]), $arguments[1]);
    }
}
if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     * @param  array|string $key
     * @param  mixed $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        return data_get(app('request')->all(), $key, $default);
    }
}
if (!function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     * @param  string $message
     * @param  array $context
     * @return \Illuminate\Log\LogManager|null
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (!function_exists('exception')) {
    /**
     * Quickly throw out the wrong
     * @param $message
     * @param int $code
     * @throws Exception
     */
    function exception($message, $code = 500)
    {
        abort($code,$message);
    }
}

if (!function_exists('getReportTime')) {
    /**
     * Rapid creation time
     * @param $time
     * @param int $type
     * @return string|false
     */
    function getReportTime($time, $type)
    {
        if ($time) {
            $time = is_numeric($time) ? $time : strtotime($time);
            if ($type == 1) {
                return date('Y-m-d 00:00:00', $time);
            } else {
                if ($time < 946656000) {
                    return date('Y-m-d 23:59:59');
                }
                return date('Y-m-d 23:59:59', $time);
            }
        }
        return false;
    }
}
if (!function_exists('resolve')) {
    /**
     * Resolve a service from the container.
     * @param  string $name
     * @return mixed
     */
    function resolve($name)
    {
        return app($name);
    }
}

if (!function_exists('dateToString')) {
    function dateToString($time)
    {
        if ($time instanceof Carbon) {
            return $time->toDateTimeString();
        } else {
            return $time;
        }
    }
}

if (!function_exists('numberToBitArray')) {
    function numberToBitArray($number, $n)
    {
        $return = [];
        for ($i = 1; $i < $n; $i++) {
            if ($number & 0x01 == 0x01) {
                $return[$i] = 1;
            }
            $number = $number >> 1;
        }
        if(!$return){
            $return = null;
        }
        return $return;
    }
}

if (!function_exists('bitJsonToNumber')) {
    function bitJsonToNumber($json, $number)
    {
        if (!$json) {
            $return = pow(2, ($number + 1)) - 1;
        } else {
            $array = json_decode($json, true);
            $return = 0;
            foreach ($array as $key => $val) {
                $return += $val ? pow(2, ($key - 1)) : 0;
            }
        }
        return $return;
    }
}

if (! function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        static $manifests = [];

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(public_path($manifestDirectory.'/hot'))) {
            $url = file_get_contents(public_path($manifestDirectory.'/hot'));

            if (Str::startsWith($url, ['http://', 'https://'])) {
                return new HtmlString(Str::after($url, ':').$path);
            }

            return new HtmlString("//localhost:8080{$path}");
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            report(new Exception("Unable to locate Mix file: {$path}."));

            if (! app('config')->get('app.debug')) {
                return $path;
            }
        }

        return new HtmlString($manifestDirectory.$manifest[$path]);
    }
}

if (! function_exists('report')) {
    /**
     * Report an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    function report($exception)
    {
        if ($exception instanceof Throwable &&
            ! $exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }

        app(ExceptionHandler::class)->report($exception);
    }
}