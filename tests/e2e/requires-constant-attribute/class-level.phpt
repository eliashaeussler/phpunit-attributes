--TEST--
The #[RequiresConstant] attribute is applied on class level
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--display-skipped';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/class-level/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

SSSS                                                                4 / 4 (100%)

Time: %s, Memory: %s

There were 4 skipped tests:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RepeatedRequiresConstantAttributeTest::fakeTest
Constant "FOO_BAZ_BAR" is required.

2) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RepeatedRequiresConstantAttributeTest::anotherFakeTest
Constant "FOO_BAZ_BAR" is required.

3) EliasHaeussler\PHPUnitAttributes\Tests\E2E\SingleRequiresConstantAttributeTest::fakeTest
Constant "FOO_BAZ" is required.

4) EliasHaeussler\PHPUnitAttributes\Tests\E2E\SingleRequiresConstantAttributeTest::anotherFakeTest
Constant "FOO_BAZ" is required.

OK, but some tests were skipped!
Tests: 4, Assertions: 0, Skipped: 4.
