--TEST--
The #[DummyAttribute] attribute is applied on method level
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--display-skipped';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/method-level/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

S.S.                                                                4 / 4 (100%)

Time: %s, Memory: %s

There were 2 skipped tests:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RepeatedDummyAttributeTest::fakeTest
foo
baz

2) EliasHaeussler\PHPUnitAttributes\Tests\E2E\SingleDummyAttributeTest::fakeTest
baz

OK, but some tests were skipped!
Tests: 4, Assertions: 2, Skipped: 2.
