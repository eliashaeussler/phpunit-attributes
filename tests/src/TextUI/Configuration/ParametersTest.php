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

namespace EliasHaeussler\PHPUnitAttributes\Tests\TextUI\Configuration;

use EliasHaeussler\PHPUnitAttributes as Src;
use Generator;
use PHPUnit\Framework;

/**
 * ParametersTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\TextUI\Configuration\Parameters::class)]
final class ParametersTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('parseBooleanValueHandlesFalseStringAsFalseBooleanDataProvider')]
    public function parseBooleanValueHandlesFalseStringAsFalseBoolean(string $value): void
    {
        self::assertFalse(Src\TextUI\Configuration\Parameters::parseBooleanValue($value, true));
    }

    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('parseBooleanValueHandlesTrueStringAsTrueBooleanDataProvider')]
    public function parseBooleanValueHandlesTrueStringAsTrueBoolean(string $value): void
    {
        self::assertTrue(Src\TextUI\Configuration\Parameters::parseBooleanValue($value, false));
    }

    #[Framework\Attributes\Test]
    public function parseBooleanValueReturnsTrueIfNeitherTrueNorFalseStringsAreMatched(): void
    {
        self::assertTrue(Src\TextUI\Configuration\Parameters::parseBooleanValue('foo', true));
    }

    /**
     * @return Generator<string, array{string}>
     */
    public static function parseBooleanValueHandlesFalseStringAsFalseBooleanDataProvider(): Generator
    {
        yield 'lowercase' => ['false'];
        yield 'uppercase' => ['FALSE'];
        yield 'mixed case' => ['FalsE'];
    }

    /**
     * @return Generator<string, array{string}>
     */
    public static function parseBooleanValueHandlesTrueStringAsTrueBooleanDataProvider(): Generator
    {
        yield 'lowercase' => ['true'];
        yield 'uppercase' => ['TRUE'];
        yield 'mixed case' => ['TruE'];
    }
}
