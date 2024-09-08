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

namespace EliasHaeussler\PHPUnitAttributes\Tests\PHPUnit\TextUI\Configuration;

use EliasHaeussler\PHPUnitAttributes as Src;
use PHPUnit\Framework;

/**
 * MigrationResultTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\PHPUnit\TextUI\Configuration\MigrationResult::class)]
final class MigrationResultTest extends Framework\TestCase
{
    private Src\PHPUnit\TextUI\Configuration\Migration $migration;
    private Src\PHPUnit\TextUI\Configuration\MigrationResult $subject;

    public function setUp(): void
    {
        $this->migration = Src\PHPUnit\TextUI\Configuration\Migration::forParameter('foo', 'baz');
        $this->subject = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration(
            $this->migration,
            'fooValue',
            'bazValue',
        );
    }

    #[Framework\Attributes\Test]
    public function wasMigratedReturnsTrueIfValuesAreNotEqual(): void
    {
        self::assertTrue($this->subject->wasMigrated());
    }

    #[Framework\Attributes\Test]
    public function wasMigratedReturnsFalseIfNoLegacyValueIsGiven(): void
    {
        $subject = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration($this->migration, 'fooValue');

        self::assertFalse($subject->wasMigrated());
    }

    #[Framework\Attributes\Test]
    public function wasMigratedReturnsFalseIfValuesAreEqual(): void
    {
        $subject = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration(
            $this->migration,
            'fooValue',
            'fooValue',
        );

        self::assertFalse($subject->wasMigrated());
    }

    #[Framework\Attributes\Test]
    public function getDiffAsStringReturnsNullIfParameterWasNotMigrated(): void
    {
        $subject = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration($this->migration, 'fooValue');

        self::assertNull($subject->getDiffAsString());
    }

    #[Framework\Attributes\Test]
    public function getDiffAsStringReturnsDiffWithoutColors(): void
    {
        $expected = <<<DIFF
- <parameter name="baz" value="bazValue" />
+ <parameter name="foo" value="fooValue" />
DIFF;

        self::assertSame($expected, $this->subject->getDiffAsString(false));
    }

    #[Framework\Attributes\Test]
    public function getDiffAsStringReturnsDiffWithColors(): void
    {
        $expected = <<<DIFF
\033[31m- <parameter name="baz" value="bazValue" />\033[39m
\033[32m+ <parameter name="foo" value="fooValue" />\033[39m
DIFF;

        self::assertSame($expected, $this->subject->getDiffAsString());
    }
}
