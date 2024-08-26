--TEST--
Deprecated configuration options are migrated
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/parameter-migration/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

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

FAILURES!
Tests: 2, Assertions: 2, Failures: 1, Deprecations: 1.
