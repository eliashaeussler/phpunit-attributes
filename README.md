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
          bootstrap="vendor/autoload.php"
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

The following attributes are shipped with this library:

* [`#[RequiresClass]`](#requiresclass)
* [`#[RequiresConstant]`](#requiresconstant)
* [`#[RequiresPackage]`](#requirespackage)

### [`#[RequiresClass]`](src/Attribute/RequiresClass.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain class exists. The given class must be loadable by the current
class loader (which normally is Composer's default class loader).

#### Configuration

By default, test cases requiring non-existent classes are skipped. However, this
behavior can be configured by using the `handleMissingClasses` extension parameter.
If set to `fail`, test cases with missing classes will fail (defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleMissingClasses" value="fail" />
    </bootstrap>
</extensions>
```

#### Example

```php
final class DummyTest extends TestCase
{
    #[RequiresClass(AnImportantClass::class)]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

#### Require single class

Class level:

```php
#[RequiresClass(AnImportantClass::class)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnImportantClass is missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresClass(AnImportantClass::class)]
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

#### Require single class and provide custom message

Class level:

```php
#[RequiresClass(AnImportantClass::class, 'This test requires the `AnImportantClass` class.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass is missing, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnImportantClass is missing, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresClass(AnImportantClass::class, 'This test requires the `AnImportantClass` class.')]
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass is missing, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

#### Require single class and define custom outcome behavior

Class level:

```php
#[RequiresClass(AnImportantClass::class, outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if AnImportantClass is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if AnImportantClass is missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresClass(AnImportantClass::class, outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if AnImportantClass is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

#### Require multiple classes

Class level:

```php
#[RequiresClass(AnImportantClass::class)]
#[RequiresClass(AnotherVeryImportantClass::class)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass and/or AnotherVeryImportantClass are missing.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnImportantClass and/or AnotherVeryImportantClass are missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresClass(AnImportantClass::class)]
    #[RequiresClass(AnotherVeryImportantClass::class)]
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass and/or AnotherVeryImportantClass are missing.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>

### [`#[RequiresConstant]`](src/Attribute/RequiresConstant.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain constant exists. The constant can be defined globally or at class
scope. The latter requires the appropriate class to be loadable by the current
class loader (which normally is Composer's default class loader).

#### Configuration

By default, test cases requiring undefined constants are skipped. However, this
behavior can be configured by using the `handleUndefinedConstants` extension
parameter. If set to `fail`, test cases with undefined constants will fail
(defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleUndefinedConstants" value="fail" />
    </bootstrap>
</extensions>
```

#### Example

```php
final class DummyTest extends TestCase
{
    #[RequiresConstant('AN_IMPORTANT_CONSTANT')]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

#### Require class constant

Class level:

```php
#[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT')]
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

#### Require class constant and provide custom message

Class level:

```php
#[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT', 'This test requires an important constant.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT', 'This test requires an important constant.')]
    public function testDummyAction(): void
    {
        // Skipped if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

#### Require class constant and define custom outcome behavior

Class level:

```php
#[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT', outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresConstant(AnImportantClass::class . '::AN_IMPORTANT_CONSTANT', outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if AnImportantClass::AN_IMPORTANT_CONSTANT is undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

#### Require multiple constants

Class level:

```php
#[RequiresConstant('SOME_IMPORTANT_CONSTANT')]
#[RequiresConstant('ANOTHER_VERY_IMPORTANT_CONSTANT')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if SOME_IMPORTANT_CONSTANT and/or ANOTHER_VERY_IMPORTANT_CONSTANT constants are undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if SOME_IMPORTANT_CONSTANT and/or ANOTHER_VERY_IMPORTANT_CONSTANT constants are undefined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresConstant('SOME_IMPORTANT_CONSTANT')]
    #[RequiresConstant('ANOTHER_VERY_IMPORTANT_CONSTANT')]
    public function testDummyAction(): void
    {
        // Skipped if SOME_IMPORTANT_CONSTANT and/or ANOTHER_VERY_IMPORTANT_CONSTANT constants are undefined.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>

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

#### Example

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

#### Require explicit Composer package

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

#### Require any Composer package matching a given pattern

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

#### Require Composer package with given version constraint

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

#### Require Composer package and provide custom message

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

#### Require Composer package and define custom outcome behavior

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

#### Multiple requirements

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

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
