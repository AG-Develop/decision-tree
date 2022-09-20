<?php

namespace Coff\DecisionTree;

class DecisionNode
{
    protected $callback;

    public function __construct(
        callable $callback
    ) {
        $this->callback = $callback;
    }

    /**
     * @throws \Throwable
     */
    public function assert(object $object)
    {
        $result = call_user_func_array($this->callback, [$object]);

        switch (true) {
            case $result instanceof self:
                return $result->assert($object);
            case $result instanceof \Throwable:
                throw $result;
            case is_callable($result):
                return call_user_func_array($result, []);
        }

        return $result;
    }
}
