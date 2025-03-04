--TEST--
The #[ForbidsConstant] attribute causes tests with unsatisified requirement to fail
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/fail-on-unsatisfied-requirement/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

define('FOO_BAZ', 'bar');

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

F.                                                                  2 / 2 (100%)

Time: %s, Memory: %s

There was 1 failure:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\ForbidsConstantAttributeFailsOnUnsatisfiedRequirementTest::fakeTest
Constant "FOO_BAZ" is forbidden.

%s
%s
%s

FAILURES!
Tests: 2, Assertions: 2, Failures: 1.
