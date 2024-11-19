# Again

A handy utility package that lets you write loops in a declarative way and avoid infinite loops.

## Installing

```shell
composer require vincenzoraco/again
```

## Usage

When you need to execute a block of code repeatedly until a specific condition is met, you might typically use a `while`
loop. For example:

```php
while (!$this->isSent()) {
    $this->sendEmail();
}
```

This approach works, but it can lead to an **infinite loop** if the condition is never met or the exit condition is not
properly defined. To prevent this, it is essential to add safeguards, such as limiting the number of iterations.

### Introducing Again

**Again** simplifies repetitive task execution with built-in safeguards to prevent infinite loops. It allows you to
specify
both a maximum number of iterations and a stopping condition, ensuring your loop behaves as expected.

Here’s how **Again** can help:

```php
Again::perform(fn(int $i) => $this->sendEmail())
  ->limitTo(3)  // Stop after 3 attempts
  ->until(fn(int $i) => !$this->isSent())  // Stop if email is sent
  ->execute();
```

In this example, the email sending will attempt up to **3 times** until `$this->isSent()` is `true`. If the condition
isn't
met after 3 attempts, the loop will automatically stop, preventing an endless loop.

### Example with Custom Logic

You can also define your loop behavior using more complex logic by passing custom conditions or limits. For example:

```php
$action = Again::perform($actionLogic)
  ->limitTo($limit)  // Limit the number of iterations
  ->until($until)  // Define the condition to stop
  ->execute();
```

### Know Why the Loop Ended

Sometimes you need to know why the loop has ended, whether it reached the maximum number of iterations or the stop
condition was met. **Again** provides the `AgainStopReason` enum to indicate the reason for the loop termination.

For example:

```php
$stopReason = Again::perform($actionLogic)
  ->limitTo($limit)
  ->until($until)
  ->execute();  // Execute and get the stop reason

// Check the reason for stopping
if ($stopReason === AgainStopReason::MAX_ITERATIONS_REACHED) {
    $this->notifyToSlack('Maximum retries reached.');
    // Handle the case when max iterations are reached
}

if ($stopReason === AgainStopReason::CONDITION_MET) {
    // Handle the case when the condition was met and the loop stopped
}
```

### Key Features:

- **Limit the number of iterations:** Use the `limitTo()` method to specify a maximum number of executions, preventing
  infinite loops.
- **Custom stop condition:** Use the `until()` method to define the condition that will cause the loop to stop when it
  evaluates to `true`.
- **Flexible execution:** Pass custom logic or actions to be executed on each iteration.
- **Track loop termination reason:** The `execute()` method returns an `AgainStopReason` value, letting you know why the
  loop ended.

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/vincenzoraco/again/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/vincenzoraco/again/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and
PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
