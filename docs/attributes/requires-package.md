# [`#[RequiresPackage]`](../../src/Attribute/RequiresPackage.php)

_Scope: Class & Method level_

This attribute can be used to define specific package requirements for single
tests as well as complete test classes. A required package is expected to be
installed via Composer. You can optionally define a version constraint and a
custom message.

> [!IMPORTANT]
> The attribute determines installed Composer packages from the build-time
> generated `InstalledVersions` class built by Composer. In order to properly
> read from this class , it's essential to include Composer's generated
> autoloader in your PHPUnit bootstrap script:
>
> ```xml
> <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
>          xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
>          bootstrap="vendor/autoload.php"
> >
>     <!-- ... -->
> </phpunit>
> ```
>
> You can also pass the script as command option: `phpunit --bootstrap vendor/autoload.php`

## Configuration

By default, test cases with unsatisfied requirements are skipped. However, this
behavior can be configured by using the `handleUnsatisfiedPackageRequirements`
extension parameter. If set to `fail`, test cases with unsatisfied requirements
will fail (defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleUnsatisfiedPackageRequirements" value="fail" />
    </bootstrap>
</extensions>
```

## Example

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console')]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

### Require explicit Composer package

Class level:

```php
#[RequiresPackage('symfony/console')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if symfony/console is not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if symfony/console is not installed.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console')]
    public function testDummyAction(): void
    {
        // Skipped if symfony/console is not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require any Composer package matching a given pattern

Class level:

```php
#[RequiresPackage('symfony/*')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if no symfony/* packages are installed.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if no symfony/* packages are installed.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/*')]
    public function testDummyAction(): void
    {
        // Skipped if no symfony/* packages are installed.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require Composer package with given version constraint

Class level:

```php
#[RequiresPackage('symfony/console', '>= 7')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if installed version of symfony/console is < 7.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if installed version of symfony/console is < 7.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console', '>= 7')]
    public function testDummyAction(): void
    {
        // Skipped if installed version of symfony/console is < 7.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require Composer package and provide custom message

Class level:

```php
#[RequiresPackage('symfony/console', message: 'This test requires the Symfony Console.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if symfony/console is not installed, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if symfony/console is not installed, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console', message: 'This test requires the Symfony Console.')]
    public function testDummyAction(): void
    {
        // Skipped if symfony/console is not installed, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require Composer package and define custom outcome behavior

Class level:

```php
#[RequiresPackage('symfony/console', outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if symfony/console is not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if symfony/console is not installed.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console', outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if symfony/console is not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

### Multiple requirements

Class level:

```php
#[RequiresPackage('symfony/console')]
#[RequiresPackage('guzzlehttp/guzzle')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if symfony/console and/or guzzlehttp/guzzle are not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if symfony/console and/or guzzlehttp/guzzle are not installed.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresPackage('symfony/console')]
    #[RequiresPackage('guzzlehttp/guzzle')]
    public function testDummyAction(): void
    {
        // Skipped if symfony/console and/or guzzlehttp/guzzle are not installed.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>
