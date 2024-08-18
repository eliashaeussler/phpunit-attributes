<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/phpunit-attributes".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\PHPUnitAttributes\Tests\Reflection;

use EliasHaeussler\PHPUnitAttributes as Src;
use EliasHaeussler\PHPUnitAttributes\Tests;
use PHPUnit\Framework;

/**
 * AttributeReflectorTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Reflection\AttributeReflector::class)]
final class AttributeReflectorTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function forClassReturnsAttributesForGivenClass(): void
    {
        $expected = [
            new Src\Attribute\RequiresPackage('phpunit/phpunit', '< 11', 'PHPUnit < 11 must be installed'),
            new Src\Attribute\RequiresPackage('phpstan/phpstan'),
        ];

        $actual = Src\Reflection\AttributeReflector::forClass(
            Tests\Fixtures\FakeTest::class,
            Src\Attribute\RequiresPackage::class,
        );

        self::assertEquals($expected, $actual);
    }

    #[Framework\Attributes\Test]
    public function forClassMethodReturnsAttributesForGivenClassMethod(): void
    {
        $expected = [
            new Src\Attribute\RequiresPackage('phpunit/phpunit', '>= 10', 'PHPUnit >= 10 must be installed'),
            new Src\Attribute\RequiresPackage('phpstan/phpstan-phpunit'),
        ];

        $actual = Src\Reflection\AttributeReflector::forClassMethod(
            Tests\Fixtures\FakeTest::class,
            'fakeMethod',
            Src\Attribute\RequiresPackage::class,
        );

        self::assertEquals($expected, $actual);
    }
}
