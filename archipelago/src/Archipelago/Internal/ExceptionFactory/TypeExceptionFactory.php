<?php

/**
 * @internal
 */
class Archipelago_Internal_ExceptionFactory_TypeExceptionFactory
{
    /**
     * @var self|null
     */
    private static $instance = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string|object $pathOrObject
     * @param string $method
     * @param int $position
     * @param string $expected
     * @param bool $nullable
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    public function makeArgumentObject($pathOrObject, $method, $position, $expected, $nullable, $value)
    {
        $expression = $this->makeObjectExpression($expected, $nullable);

        return $this->makeArgument($pathOrObject, $method, $position, $expression, $value);
    }

    /**
     * @param string|object $pathOrObject
     * @param string $method
     * @param int $position
     * @param string $expected
     * @param bool $nullable
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    public function makeArgumentNonObject($pathOrObject, $method, $position, $expected, $nullable, $value)
    {
        $expression = $this->makeNonObjectExpression($expected, $nullable);

        return $this->makeArgument($pathOrObject, $method, $position, $expression, $value);
    }

    /**
     * @param string|object $pathOrObject
     * @param string $method
     * @param string $expected
     * @param bool $nullable
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    public function makeReturnObject($pathOrObject, $method, $expected, $nullable, $value)
    {
        $expression = $this->makeObjectExpression($expected, $nullable);

        return $this->makeReturn($pathOrObject, $method, $expression, $value);
    }

    /**
     * @param string|object $pathOrObject
     * @param string $method
     * @param string $expected
     * @param bool $nullable
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    public function makeReturnNonObject($pathOrObject, $method, $expected, $nullable, $value)
    {
        $expression = $this->makeNonObjectExpression($expected, $nullable);

        return $this->makeReturn($pathOrObject, $method, $expression, $value);
    }

    /**
     * @param string $message
     * @return Archipelago_TypeException
     */
    public function make($message)
    {
        require_once dirname(__FILE__) . '/../../Exception.php';
        require_once dirname(__FILE__) . '/../../BadValueException.php';
        require_once dirname(__FILE__) . '/../../TypeException.php';

        return new Archipelago_TypeException($message);
    }

    /**
     * @param object|string $pathOrObject
     * @param string $method
     * @param int $position
     * @param string $expectedExpression
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    private function makeArgument($pathOrObject, $method, $position, $expectedExpression, $value)
    {
        $type = $this->formatType($value);

        $message = sprintf('argument %d should be %s, got %s', $position, $expectedExpression, $type);

        return $this->makeException($pathOrObject, $method, $message);
    }

    /**
     * @param object|string $pathOrObject
     * @param string $method
     * @param string $expectedExpression
     * @param mixed $value
     * @return Archipelago_TypeException
     */
    private function makeReturn($pathOrObject, $method, $expectedExpression, $value)
    {
        $type = $this->formatType($value);

        $message = sprintf('should return %s, got %s', $expectedExpression, $type);

        return $this->makeException($pathOrObject, $method, $message);
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function formatType($value)
    {
        require_once dirname(__FILE__) . '/../TypeFormatter.php';

        return Archipelago_Internal_TypeFormatter::format($value);
    }

    /**
     * @param string $expected
     * @param bool $nullable
     * @return string
     */
    private function makeObjectExpression($expected, $nullable)
    {
        return $this->addNullableToExpressionInNullable(sprintf('an instance of %s', $expected), $nullable);
    }

    /**
     * @param string $expected
     * @param bool $nullable
     * @return string
     */
    private function makeNonObjectExpression($expected, $nullable)
    {
        return $this->addNullableToExpressionInNullable(sprintf('%s', $expected), $nullable);
    }

    /**
     * @param string $expression
     * @param bool $nullable
     * @return string
     */
    private function addNullableToExpressionInNullable($expression, $nullable)
    {
        return $nullable
            ? sprintf('%s %s', $expression, 'or null')
            : $expression;
    }

    /**
     * @param object|string $pathOrObject
     * @param string $method
     * @param string $message
     * @return Archipelago_TypeException
     */
    private function makeException($pathOrObject, $method, $message)
    {
        $class = is_object($pathOrObject)
            ? get_class($pathOrObject)
            : $pathOrObject;

        $message = sprintf('%s::%s() %s.', $class, $method, $message);

        return $this->make($message);
    }
}
