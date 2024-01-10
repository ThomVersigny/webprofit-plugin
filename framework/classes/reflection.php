<?php
class CustomReflection
{
    public static function getAttributes($method)
    {
        $attribute = [];
        $regex = '%^\s*\*\s*@+(?P<method>/?(?:[a-z0-9])+)+\((?P<params>.*)\)\s*$%im';
        preg_match_all($regex, $method->getDocComment(), $matches);
        for ($i = 0; $i < count($matches["method"]); $i++) {
            if (strpos($matches["params"][$i], ',') !== false) {
                $attribute[$matches["method"][$i]] = explode(", ", str_replace('"', "", $matches["params"][$i]));
            } else {
                $attribute[$matches["method"][$i]] = str_replace('"', "", $matches["params"][$i]);
            }
        }
        return $attribute;
    }
    public static function getConstructorParameters(ReflectionClass $reflectionClass)
    {
        $parameters = [];
        $reflectionMethod = $reflectionClass->getConstructor();
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterClass = $parameter->getClass();
            if ($parameterClass != null) {
                $parameters[] = $parameterClass;
            }
        }
        return $parameters;
    }
}

class ClassInfo extends ReflectionClass
{
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        $attribute = [];
        $regex = '%^\s*\*\s*@+(?P<method>/?(?:[a-z0-9])+)+\("+(?P<value>/?(?:[a-zA-Z0-9~@#$^*()_+=[\]{}|\\,.?/: -]+/?)+)+"\)\s*$%im';
        preg_match_all($regex, $this->getDocComment(), $matches);
        for ($i = 0; $i < count($matches["method"]); $i++) {
            $attribute[$matches["method"][$i]] = $matches["value"][$i];
        }
        return $attribute;
    }
    public function getConstructorParameterTypes()
    {
        $parameters = [];
        $reflectionMethod = $this->getConstructor();
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterClass = $parameter->getClass();
            if ($parameterClass != null) {
                $parameters[] = $parameterClass;
            }
        }
        return $parameters;
    }
    public function getConstructorParameterNames()
    {
        $parameters = [];
        $reflectionMethod = $this->getConstructor();
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterClass = $parameter->getClass();
            if ($parameterClass != null) {
                $parameters[] = $parameterClass->name;
            }
        }
        return $parameters;
    }
}

class PropertyInfo extends ReflectionProperty
{
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        $attribute = [];
        $regex = '%^\s*\*\s*@+(?P<method>/?(?:[a-z0-9])+)+\("+(?P<value>/?(?:[a-zA-Z0-9~@#$^*()_+=[\]{}|\\,.?/: -]+/?)+)+"\)\s*$%im';
        preg_match_all($regex, $this->getDocComment(), $matches);
        for ($i = 0; $i < count($matches["method"]); $i++) {
            $attribute[$matches["method"][$i]] = $matches["value"][$i];
        }
        return $attribute;
    }
}

class MethodInfo extends ReflectionMethod
{
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        $attribute = [];
        $regex = '%^\s*\*\s*@+(?P<method>/?(?:[a-z0-9])+)+\("+(?P<value>/?(?:[a-zA-Z0-9~@#$^*()_+=[\]{}|\\,.?/: -]+/?)+)+"\)\s*$%im';
        preg_match_all($regex, $this->getDocComment(), $matches);
        for ($i = 0; $i < count($matches["method"]); $i++) {
            $attribute[$matches["method"][$i]] = $matches["value"][$i];
        }
        return $attribute;
    }
}