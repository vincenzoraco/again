<?php

namespace VincenzoRaco\Again;

use Closure;
use InvalidArgumentException;

class Again
{
    private float $maxIterations = INF;

    private int $iteration = 0;

    private ?Closure $condition = null;

    public function __construct(
        private readonly Closure $action,
    ) {}

    public static function perform(
        Closure $action,
    ): static {
        return new static($action);
    }

    public function limitTo(
        int $maxIterations,
    ): static {
        $this->maxIterations = (float) $maxIterations;

        return $this;
    }

    public function until(
        Closure $condition,
    ): static {
        $this->condition = $condition;

        return $this;
    }

    public function execute(): AgainStopReason
    {
        $action = $this->action;
        $condition = $this->condition;

        if (! $this->isConditionSet() && $this->isMaxIterationsInfinite()) {
            throw new InvalidArgumentException(
                'You must define either a condition or limit the iterations'
            );
        }

        while (true) {
            if ($this->iteration >= $this->maxIterations) {
                return AgainStopReason::MAX_ITERATIONS_REACHED;
            }

            if ($condition && ! $condition($this->iteration)) {
                return AgainStopReason::CONDITION_MET;
            }

            $action($this->iteration);

            $this->iteration++;
        }
    }

    public function isConditionSet(): bool
    {
        return $this->getCondition() !== null;
    }

    public function isMaxIterationsInfinite(): bool
    {
        return $this->getMaxIterations() === INF;
    }

    public function getCondition(): ?Closure
    {
        return $this->condition;
    }

    public function getMaxIterations(): float
    {
        return $this->maxIterations;
    }

    public function getIterations(): int
    {
        return $this->iteration;
    }
}
