# [`#[RequiresEnv]`](../../src/Attribute/RequiresEnv.php)

_Scope: Class & Method level_

With this attribute, tests or test cases can be marked as to be only executed
if a certain environment variable exists. The environment variable is checked
using [`getenv`](https://www.php.net/manual/en/function.getenv.php) and via the
[`$_ENV`](https://www.php.net/manual/en/reserved.variables.environment.php)
superglobal.

## Configuration

By default, test cases requiring environment variables are skipped. However,
this behavior can be configured by using the `handleMissingEnvironmentVariables`
extension parameter. If set to `fail`, test cases with missing environment
variables will fail (defaults to `skip`):

```xml
<extensions>
    <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
        <parameter name="handleMissingEnvironmentVariables" value="fail" />
    </bootstrap>
</extensions>
```

## Example

```php
final class DummyTest extends TestCase
{
    #[RequiresEnv('AN_IMPORTANT_ENV')]
    public function testDummyAction(): void
    {
        // ...
    }
}
```

<details>
<summary>More examples</summary>

### Require single environment variable

Class level:

```php
#[RequiresEnv('AN_IMPORTANT_ENV')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresEnv('AN_IMPORTANT_ENV')]
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require single environment variable and provide custom message

Class level:

```php
#[RequiresEnv('AN_IMPORTANT_ENV', 'This test requires an important env var.')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing, along with custom message.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresEnv('AN_IMPORTANT_ENV', 'This test requires an important env var.')]
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV is missing, along with custom message.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

### Require single environment variable and define custom outcome behavior

Class level:

```php
#[RequiresEnv('AN_IMPORTANT_ENV', outcomeBehavior: OutcomeBehavior::Fail)]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Fails if AN_IMPORTANT_ENV is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Fails if AN_IMPORTANT_ENV is missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresEnv('AN_IMPORTANT_ENV', outcomeBehavior: OutcomeBehavior::Fail)]
    public function testDummyAction(): void
    {
        // Fails if AN_IMPORTANT_ENV is missing.
    }

    public function testOtherDummyAction(): void
    {
        // Does not fail.
    }
}
```

### Require multiple environment variables

Class level:

```php
#[RequiresEnv('AN_IMPORTANT_ENV')]
#[RequiresEnv('ANOTHER_VERY_IMPORTANT_ENV')]
final class DummyTest extends TestCase
{
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV and/or ANOTHER_VERY_IMPORTANT_ENV environment variables are missing.
    }

    public function testOtherDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV and/or ANOTHER_VERY_IMPORTANT_ENV environment variables are missing.
    }
}
```

Method level:

```php
final class DummyTest extends TestCase
{
    #[RequiresEnv('AN_IMPORTANT_ENV')]
    #[RequiresEnv('ANOTHER_VERY_IMPORTANT_ENV')]
    public function testDummyAction(): void
    {
        // Skipped if AN_IMPORTANT_ENV and/or ANOTHER_VERY_IMPORTANT_ENV environment variables are missing.
    }

    public function testOtherDummyAction(): void
    {
        // Not skipped.
    }
}
```

</details>
