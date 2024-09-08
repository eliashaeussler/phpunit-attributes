--TEST--
Autoload errors in before methods are properly handled
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--display-skipped';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/handle-autoload-errors/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

SS                                                                  2 / 2 (100%)

Time: %s, Memory: %s

There were 2 skipped tests:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RequiresPackageAttributeHandlesAutoloadErrorsTest::fakeTest
Package "foo/baz" is required.

2) EliasHaeussler\PHPUnitAttributes\Tests\E2E\RequiresPackageAttributeHandlesAutoloadErrorsTest::anotherFakeTest
Package "foo/baz" is required.

OK, but some tests were skipped!
Tests: 2, Assertions: 0, Skipped: 2.
