# [`#[RequiresConstant]`](../../src/Attribute/RequiresConstant.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain constant exists. The constant can be defined globally or at class
scope. The latter requires the appropriate class to be loadable by the current
class loader (which normally is Composer's default class loader).

## Configuration

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

## Example

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

### Require class constant

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

### Require class constant and provide custom message

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

### Require class constant and define custom outcome behavior

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

### Require multiple constants

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
