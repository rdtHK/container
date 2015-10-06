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
$container->add('one', 1);
$container->get('one'); // 1
```

Builder functions are executed the first time 'get'
is called and their return value is cached and returned
on subsequent calls.

```php
$container = new Container();
$calls = 0;
$container->add('foo', function ($container) use (&$calls) {
    $calls++;
    return 'bar';
});

echo $container->get('foo'); // 'bar'
echo $calls; // 1
echo $container->get('foo'); // 'bar'
echo $calls; // 1
```

```php
class Foo {
    public $bar;
    public $baz;

    public function __construct($bar) {
        $this->bar = $bar;
    }

    public function setProperty($property) {
        $this->baz = $property;
    }
}

$container = new Container();
$container->set('bar', 1);
$container->set('baz', function ($container) {
    return "ABC";
});
$container->set(
    'Foo',
    [
        '__construct' => ['bar'],
        'setProperty' => ['baz'],
    ]
);
$foo = $container->get('Foo');
echo $foo->bar; // 1
echo $foo->baz; // ABC
```
