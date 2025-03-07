# [`#[ForbidsConstant]`](../../src/Attribute/ForbidsConstant.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain constant does *not* exist. The constant can be defined globally or
at class scope. The latter requires the appropriate class to be loadable by the
current class loader (which normally is Composer's default class loader).

## Configuration

By default, test cases forbidding defined constants are skipped. However, this
behavior can be configured by using the `handleDefinedConstants` extension
parameter. If set to `fail`, test cases with defined constants will fail
(defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleDefinedConstants" value="fail" />
    </bootstrap>
</extensions>
```

## Example

```php
final class DummyTest extends TestCase
{
    #[ForbidsConstant('AN_ANNOYING_CONSTANT')]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

### Forbid class constant

Class level:

```php
#[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT')]
    public function testDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Forbid class constant and provide custom message

Class level:

```php
#[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT', 'This test forbids an annoying constant.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT', 'This test forbids an annoying constant.')]
    public function testDummyAction(): void
    {
        // Skipped if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Forbid class constant and define custom outcome behavior

Class level:

```php
#[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT', outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsConstant(AnAnnoyingClass::class . '::AN_ANNOYING_CONSTANT', outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if AnAnnoyingClass::AN_ANNOYING_CONSTANT is defined.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

### Forbid multiple constants

Class level:

```php
#[ForbidsConstant('SOME_ANNOYING_CONSTANT')]
#[ForbidsConstant('ANOTHER_VERY_ANNOYING_CONSTANT')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if SOME_ANNOYING_CONSTANT and/or ANOTHER_VERY_ANNOYING_CONSTANT constants are defined.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if SOME_ANNOYING_CONSTANT and/or ANOTHER_VERY_ANNOYING_CONSTANT constants are defined.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsConstant('SOME_ANNOYING_CONSTANT')]
    #[ForbidsConstant('ANOTHER_VERY_ANNOYING_CONSTANT')]
    public function testDummyAction(): void
    {
        // Skipped if SOME_ANNOYING_CONSTANT and/or ANOTHER_VERY_ANNOYING_CONSTANT constants are defined.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>
