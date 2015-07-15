#Container

A small dependency injection container that can also be used
as a service locator.

## TODO

Missing things that should be added before 1.0:

- A proper README.

## Examples

```php
<?php
$container = new Container();
$container->add(1);
$container->get(); // 1
?>
```

```php
<?php
$container = new Container();
$calls = 0;
$container->add(function ($container) use (&$calls) {
    $calls++;
    return "foo";
});

echo $container->get(); // foo
echo $calls; // 1
echo $container->get(); // "foo"
echo $calls // 1
?>
```
```php
<?php
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
$foo = $container->get(
    'Foo',
    [
        '__construct' => ['bar'],
        'setProperty' => ['baz'],
    ]
);
echo $foo->bar; // 1
echo $foo->baz; // ABC
?>
```
