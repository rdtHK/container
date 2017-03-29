#Container

A small dependency injection container that can also be used
as a service locator.

## TODO

Missing things that should be added before 1.0:

- A proper README.

## Examples
Raw values are returned directly.

```php
$container = new Container();
$container->bind('one', 1);
$container->get('one'); // 1
```

Builder functions are executed the first time 'get'
is called and their return value is cached and returned
on subsequent calls.

```php
$container = new Container();
$calls = 0;
$container->bind('foo', function ($container) use (&$calls) {
    $calls++;
    return 'bar';
});

echo $container->get('foo'); // 'bar'
echo $calls; // 1
echo $container->get('foo'); // 'bar'
echo $calls; // 1
```

$container = new Container();
$container->bind('foo', function ($container) use (&$calls) {
    $calls++;
    return 'bar';
});

echo $container->build('foo'); // 'bar'
echo $calls; // 1
echo $container->build('foo'); // 'bar'
echo $calls; // 2


$container->bind(MyInterface::class, function (MyClass $obj, $container) {
    return $obj;
});
