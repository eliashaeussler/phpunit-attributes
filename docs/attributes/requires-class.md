# [`#[RequiresClass]`](../../src/Attribute/RequiresClass.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain class exists. The given class must be loadable by the current
class loader (which normally is Composer's default class loader).

## Configuration

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

## Example

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

### Require single class

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

### Require single class and provide custom message

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

### Require single class and define custom outcome behavior

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

### Require multiple classes

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
