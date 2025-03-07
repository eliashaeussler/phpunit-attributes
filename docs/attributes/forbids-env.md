# [`#[ForbidsEnv]`](../../src/Attribute/ForbidsEnv.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain environment variable does *not* exist. The environment variable
is checked using [`getenv`](https://www.php.net/manual/en/function.getenv.php)
and via the [`$_ENV`](https://www.php.net/manual/en/reserved.variables.environment.php)
superglobal.

## Configuration

By default, test cases forbidding environment variables are skipped. However,
this behavior can be configured by using the `handleAvailableEnvironmentVariables`
extension parameter. If set to `fail`, test cases with available environment
variables will fail (defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleAvailableEnvironmentVariables" value="fail" />
    </bootstrap>
</extensions>
```

## Example

```php
final class DummyTest extends TestCase
{
    #[ForbidsEnv('AN_ANNOYING_ENV')]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

### Forbid single environment variable

Class level:

```php
#[ForbidsEnv('AN_ANNOYING_ENV')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsEnv('AN_ANNOYING_ENV')]
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Forbid single environment variable and provide custom message

Class level:

```php
#[ForbidsEnv('AN_ANNOYING_ENV', 'This test forbids an annoying env var.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsEnv('AN_ANNOYING_ENV', 'This test forbids an annoying env var.')]
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV is available, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Forbid single environment variable and define custom outcome behavior

Class level:

```php
#[ForbidsEnv('AN_ANNOYING_ENV', outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if AN_ANNOYING_ENV is available.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if AN_ANNOYING_ENV is available.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsEnv('AN_ANNOYING_ENV', outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if AN_ANNOYING_ENV is available.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

### Forbid multiple environment variables

Class level:

```php
#[ForbidsEnv('AN_ANNOYING_ENV')]
#[ForbidsEnv('ANOTHER_VERY_ANNOYING_ENV')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV and/or ANOTHER_VERY_ANNOYING_ENV environment variables are available.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV and/or ANOTHER_VERY_ANNOYING_ENV environment variables are available.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[ForbidsEnv('AN_ANNOYING_ENV')]
    #[ForbidsEnv('ANOTHER_VERY_ANNOYING_ENV')]
    public function testDummyAction(): void
    {
        // Skipped if AN_ANNOYING_ENV and/or ANOTHER_VERY_ANNOYING_ENV environment variables are available.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>
