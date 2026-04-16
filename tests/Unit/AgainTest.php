<?php

declare(strict_types=1);

use VincenzoRaco\Again\Again;
use VincenzoRaco\Again\AgainStopReason;

it('prevents loop execution if no limit and condition are provided', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('You must define either a condition or limit the iterations');

    Again::perform(function () {})
        ->execute();
});

it('stops loop execution once iterations limit is reached', function () {
    $stopReason = Again::perform(function () {})
        ->limitTo(2)
        ->until(fn () => true)
        ->execute();

    $this->assertSame(AgainStopReason::MAX_ITERATIONS_REACHED, $stopReason);
});

it('stops loop execution once condition returns false', function () {
    $stopReason = Again::perform(function () {})
        ->limitTo(2)
        ->until(fn () => false)
        ->execute();

    $this->assertSame(AgainStopReason::CONDITION_MET, $stopReason);
});

it('allows to check if condition was set', function () {
    $againAction = Again::perform(function () {})
        ->limitTo(2);

    $this->assertFalse($againAction->isConditionSet());

    $againAction->until(fn () => false);

    $this->assertTrue($againAction->isConditionSet());
});

it('allows to check if max iterations is infinite', function () {
    $againAction = Again::perform(function () {})
        ->until(fn () => false);

    $this->assertTrue($againAction->isMaxIterationsInfinite());

    $againAction->limitTo(1);

    $this->assertFalse($againAction->isMaxIterationsInfinite());
});

it('allows to get the condition', function () {
    $againAction = Again::perform(function () {});

    $this->assertNull($againAction->getCondition());

    $againAction->until(fn () => false);

    $this->assertFalse($againAction->getCondition()());
});

it('allows to get the max iterations', function () {
    $againAction = Again::perform(function () {});

    $this->assertSame(PHP_INT_MAX, $againAction->getMaxIterations());

    $againAction->limitTo(2);

    $this->assertSame(2, $againAction->getMaxIterations());
});

it('throws when limitTo receives 0', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The maximum number of iterations must be at least 1');

    Again::perform(function () {})
        ->limitTo(0);
});

it('throws when limitTo receives -1', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The maximum number of iterations must be at least 1');

    Again::perform(function () {})
        ->limitTo(-1);
});

it('throws when limitTo receives -5', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The maximum number of iterations must be at least 1');

    Again::perform(function () {})
        ->limitTo(-5);
});

it('prevents execute() from being called more than once', function () {
    $again = Again::perform(function () {})
        ->limitTo(3);

    $again->execute();

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('execute() can only be called once per instance');

    $again->execute();
});

it('allows to get the iterations reached', function () {
    $againAction = Again::perform(function () {})
        ->limitTo(3)
        ->until(fn (int $i) => $i < 2);

    $stopReason = $againAction->execute();

    $this->assertSame(AgainStopReason::CONDITION_MET, $stopReason);

    $this->assertSame(2, $againAction->getIterations());
});
