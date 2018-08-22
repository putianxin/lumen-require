<?php
namespace Ptx;

class Env
{
    /**
     * @var array 缓存数据
     */
    private static $cache = [];

    public static function isDev()
    {
        return self::checkEnv(__FUNCTION__, ['dev', 'local']);
    }

    public static function isTest()
    {
        return self::checkEnv(__FUNCTION__, ['test']);
    }

    public static function isDevOrTest()
    {
        return self::checkEnv(__FUNCTION__, ['dev', 'local', 'test']);
    }

    public static function isProd()
    {
        return self::checkEnv(__FUNCTION__, ['production', 'staging']);
    }

    public static function isStaging()
    {
        return self::checkEnv(__FUNCTION__, ['staging']);
    }

    /**
     * 内部封装方法
     * @param string $name
     * @param array|string $env
     * @return bool
     */
    private static function checkEnv($name, $env): bool
    {
        if (!isset(self::$cache[$name])) {
            self::$cache[$name] = app()->environment($env);
        }
        return self::$cache[$name];
    }

    /**
     * 清空缓存
     * @return void
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }
}