<?php

/**
 * @internal
 */
class Archipelago_Internal_TypeExceptionFactory
{
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

    //--------------------------------------------------------------------------

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
        $type = Archipelago_Internal_TypeFormatter::format($value);

        $message = sprintf('argument %d should be %s, got %s', $position, $expectedExpression, $type);

        return $this->makeSelf($pathOrObject, $method, $message);
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
        $type = Archipelago_Internal_TypeFormatter::format($value);

        $message = sprintf('should return %s, got %s', $expectedExpression, $type);

        return $this->makeSelf($pathOrObject, $method, $message);
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
    private function makeSelf($pathOrObject, $method, $message)
    {
        $class = is_object($pathOrObject)
            ? get_class($pathOrObject)
            : $pathOrObject;

        $message = sprintf('%s::%s() %s.', $class, $method, $message);

        return new Archipelago_TypeException($message);
    }
}
