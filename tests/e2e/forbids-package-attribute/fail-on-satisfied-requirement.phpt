--TEST--
The #[ForbidsPackage] attribute causes tests with satisified requirement to fail
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/fail-on-unsatisfied-requirement/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

F.                                                                  2 / 2 (100%)

Time: %s, Memory: %s

There was 1 failure:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\ForbidsPackageAttributeFailsOnSatisfiedRequirementTest::fakeTest
Package "phpunit/phpunit" is forbidden.

%s
%s
%s

FAILURES!
Tests: 2, Assertions: 2, Failures: 1.
