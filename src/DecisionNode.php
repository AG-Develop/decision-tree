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
    public function assert(object $object)
    {
        if (null !== $this->extract) {
            $object = call_user_func_array($this->extract, [$object]);
        }

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
