<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;

class CommonService
{
    private static $_modelInstance = [];

    public static function getModel($modelName, $type = '')
    {
        $arrType = [
            'db' => 'DB',
            'cache' => 'Cache',
        ];

        //Check instance
        $instanceName = strtolower((!empty($type) ? ($type . '_') : '') . $modelName);

        if (!empty($type)) {
            $modelName = $arrType[$type] . '\\' . $modelName;
        }

        if (array_key_exists($instanceName, self::$_modelInstance)) {
            return self::$_modelInstance[$instanceName];
        }

        $adapterName = '\App\Models\\' . $modelName;
        if (!class_exists($adapterName)) {
            if (empty($type)) {
                $adapterName = '\App\Models\\DB\\' . $modelName;
            }
            if (!class_exists($adapterName)) {
                $adapterName = '\App\Models\StdClass';
            }
        }

        self::$_modelInstance[$instanceName] = new $adapterName();

        return self::$_modelInstance[$instanceName];
    }

    /**
     * paginate custom
     *
     * @param mixed $items
     * @param int $perPage
     * @param int $intPage
     * @return \Illuminate\Pagination\Paginator | Collection | Array
     */
    public static function doPaginate($items, $perPage, $intPage = null)
    {
        if ((int) $perPage == 0) {
            $perPage = 1000000000000;
        }

        $intPage = $intPage ? $intPage : (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        return new LengthAwarePaginator($items->forPage($intPage, $perPage), $items->count(), $perPage, $intPage);
    }

    public static function formatErrors(Validator $validator, $returnMessage = false)
    {
        if ($returnMessage) {
            return $validator->errors()->toArray();
        }

        $obj = $validator->failed();
        $result = [];
        $rulesMap = [];

        foreach ($obj as $input => $rules) {
            $messages = [];

            foreach (array_keys($rules) as $rule) {
                if (Str::startsWith($rule, 'App\Rules')) {
                    $messages[] = $rulesMap[class_basename($rule)] ?? 'invalid';
                } else {
                    $messages[] = $rule;
                }
            }

            $result[$input] = strtolower(implode(',', $messages));
        }

        return $result;
    }

    public static function setResponseApi($params)
    {
        $params = array_merge([
            'code' => Response::HTTP_OK,
            'data' => null,
            'errors' => null,
        ], $params);

        $arrMessage = [
            Response::HTTP_OK => 'Success',
            Response::HTTP_BAD_REQUEST => 'Invalid Parameters',
            Response::HTTP_UNAUTHORIZED => 'Unauthorized',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_NOT_FOUND => 'Page Not Found',
            Response::HTTP_TOO_MANY_REQUESTS => 'Too Many Attempts',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
            Response::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
        ];

        $return = [
            'message' => $arrMessage[$params['code']],
        ];
        if ($params['data']) {
            $return['data'] = $params['data'];
        }
        if ($params['errors']) {
            $return['errors'] = $params['errors'];
        }

        return response()->json($return, $params['code']);
    }

    public static function writeLogFile($params)
    {
        $params = array_merge([
            'folder' => '',
            'filename' => '',
            'level' => Level::Info,
            'message' => '',
            'context' => [],
            'rotate' => true,
        ], $params);

        $logger = new Logger(app()->environment());
        $logFilePath = 'logs/' . (!empty($params['folder']) ? $params['folder'] . '/' : '') . $params['filename'];

        if ($params['rotate']) {
            $logger->pushHandler(new RotatingFileHandler(storage_path($logFilePath), 0, $params['level'], true, 0664));
        } else {
            $logger->pushHandler(new StreamHandler(storage_path($logFilePath), $params['level'], true, 0664));
        }

        switch ($params['level']) {
            case Level::Debug:
                $logger->debug($params['message'], $params['context']);

                break;
            case Level::Info:
            default:
                $logger->info($params['message'], $params['context']);

                break;
        }
    }
}
