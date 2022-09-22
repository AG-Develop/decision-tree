<?php

namespace Coff\DecisionTree;

class DecisionNode
{
    protected $callback;
    protected $extract;

    public function __construct(
        callable $callback,
        callable $extract = null,
    ) {
        $this->callback = $callback;
        $this->extract = $extract;
    }

    /**
     * @throws \Throwable
     */
    public function assert(object $object, $context = null)
    {
        if (null !== $this->extract) {
            $object = call_user_func_array($this->extract, [$object]);
        }

        $params = [$object];

        if (null !== $context) {
            $params[] = $context;
        }

        $result = call_user_func_array($this->callback, $params);

        switch (true) {
            case $result instanceof self:
                return $result->assert($object, $context);
            case $result instanceof \Throwable:
                throw $result;
            case is_callable($result):
                return call_user_func_array($result, $params);
        }

        return $result;
    }

}
