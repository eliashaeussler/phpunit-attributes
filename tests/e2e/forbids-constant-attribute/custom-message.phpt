--TEST--
The #[ForbidsConstant] attribute is applied with custom message
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--display-skipped';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/fixtures/custom-message/phpunit.xml';

require dirname(__DIR__, 3) . '/vendor/autoload.php';

define('FOO_BAZ', 'bar');

(new \PHPUnit\TextUI\Application())->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

S                                                                   1 / 1 (100%)

Time: %s, Memory: %s

There was 1 skipped test:

1) EliasHaeussler\PHPUnitAttributes\Tests\E2E\ForbidsConstantAttributeWithCustomMessageTest::fakeTest
You've obviously defined some FOO...

OK, but some tests were skipped!
Tests: 1, Assertions: 0, Skipped: 1.
