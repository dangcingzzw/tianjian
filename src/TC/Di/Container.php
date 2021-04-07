<?php
namespace TC\Di;
class Container
{

    private static $_pool=[];

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function block($object,$properties=[])
    {
        foreach ($properties as $property => $value) {
            $object->$property = $value;
        }
    }

    public static function createObject($config)
    {
        $class = $config['class'];
        unset($config['class']);

        $object = new $class();
        self::block($object,$config);

        if(method_exists($object,'init')) {
            $object->init();
        }
        return $object;
    }

    public static function set($name,$config)
    {
        return self::$_pool[ $name ] = $config;
    }

    public static function get($name)
    {
        if(!isset(self::$_pool[ $name ])) {
            return null;
        }

        $object = self::$_pool[ $name ];
        if(is_array($object) && isset($object['class'])) {
            $instance = self::createObject($object);
            static::set($name,$instance);

            return $instance;
        }

        return $object;
    }

    public static function insure($config,$defaultClass=null)
    {
        if(is_array($config)) {
            if(isset($config['class'])) {
                return self::createObject($config);
            }

            if(isset($defaultClass)) {
                $config['class'] = $defaultClass;

                return self::createObject($config);
            }
        }

        return $config;
    }
}