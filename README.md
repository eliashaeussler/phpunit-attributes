<div align="center">

# PHPUnit Attributes

[![Coverage](https://img.shields.io/coverallsCoverage/github/eliashaeussler/phpunit-attributes?logo=coveralls)](https://coveralls.io/github/eliashaeussler/phpunit-attributes)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/phpunit-attributes?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/phpunit-attributes/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/phpunit-attributes/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/phpunit-attributes/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/phpunit-attributes/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/phpunit-attributes/actions/workflows/tests.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/phpunit-attributes/php?logo=php)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)

</div>

A Composer library with additional attributes to enhance testing with PHPUnit.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/phpunit-attributes?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/phpunit-attributes?color=brightgreen)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)

```bash
composer require --dev eliashaeussler/phpunit-attributes
```

## ⚡ Usage

The library ships with a ready-to-use PHPUnit extension. It must be registered
in your PHPUnit configuration file:

```diff
 <?xml version="1.0" encoding="UTF-8"?>
 <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
 >
+    <extensions>
+        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension" />
+    </extensions>
     <testsuites>
         <testsuite name="unit">
             <directory>tests</directory>
         </testsuite>
     </testsuites>
     <source>
         <include>
             <directory>src</directory>
         </include>
     </source>
 </phpunit>
```

Some attributes can be configured with custom extension parameters. These must
be added to the extension registration section like follows:

```diff
     <extensions>
-        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension" />
+        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
+            <parameter name="fancyParameterName" value="fancyParameterValue" />
+        </bootstrap>
     </extensions>
```

## 🎢 Attributes

The following attributes are shipped with this library.

### [`#[RequiresPackage]`](src/Attribute/RequiresPackage.php)

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

#### Configuration

By default, test cases with unsatisfied requirements are skipped. However, this
behavior can be configured by using the `failOnUnsatisfiedPackageRequirements`
extension parameter. If set to `true`, test cases with unsatisfied requirements
will fail (defaults to `false`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="failOnUnsatisfiedPackageRequirements" value="true" />
    </bootstrap>
</extensions>
```

#### Examples

##### Require explicit Composer package

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

##### Require any Composer package matching a given pattern

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

##### Require Composer package with given version constraint

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

##### Require Composer package and provide custom message

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

##### Multiple requirements

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

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
