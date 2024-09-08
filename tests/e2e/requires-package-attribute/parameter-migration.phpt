--TEST--
Deprecated configuration options are migrated
--FILE--
<?php

declare(strict_types=1);

require dirname(__DIR__, 3) . '/vendor/autoload.php';

// Display PHPUnit deprecations (PHPUnit >= 10.5.32, >= 11.3.3)
$reflectionClass = new \ReflectionClass(\PHPUnit\TextUI\Configuration\Configuration::class);
if ($reflectionClass->hasMethod('displayDetailsOnPhpunitDeprecations')) {
    $_SERVER['argv'][] = '--display-phpunit-deprecations';
}

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/parameter-migration/phpunit.xml';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

F.                                                                  2 / 2 (100%)

Time: %s, Memory: %s

There was 1 PHPUnit test runner deprecation:

1) Your XML configuration contains deprecated extension parameters. Migrate your XML configuration:
- <parameter name="failOnUnsatisfiedPackageRequirements" value="true" />
+ <parameter name="handleUnsatisfiedPackageRequirements" value="fail" />

--

There was 1 failure:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RequiresPackageAttributeFailsWithMigratedParameterTest::fakeTest
Package "foo/baz" is required.

%s
%s
%s

FAILURES!
Tests: 2, Assertions: 2, Failures: 1,%sDeprecations: 1.
