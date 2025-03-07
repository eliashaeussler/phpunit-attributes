--TEST--
The #[ForbidsEnv] attribute causes tests with unsatisified requirement to be skipped
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--display-skipped';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/skip-on-unsatisfied-requirement/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

S.                                                                  2 / 2 (100%)

Time: %s, Memory: %s

There was 1 skipped test:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\ForbidsEnvAttributeSkipsOnUnsatisfiedRequirementTest::fakeTest
Environment variable "FOO_BAZ" is forbidden.

OK, but some tests were skipped!
Tests: 2, Assertions: 1, Skipped: 1.
