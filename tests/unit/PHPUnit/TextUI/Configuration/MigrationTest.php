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
use PHPUnit\Runner;

/**
 * MigrationTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\PHPUnit\TextUI\Configuration\Migration::class)]
final class MigrationTest extends Framework\TestCase
{
    private Src\PHPUnit\TextUI\Configuration\Migration $subject;
    private Runner\Extension\ParameterCollection $parameters;

    public function setUp(): void
    {
        $this->subject = Src\PHPUnit\TextUI\Configuration\Migration::forParameter('foo', 'baz');
        $this->parameters = Runner\Extension\ParameterCollection::fromArray([
            'baz' => 'bazValue',
        ]);
    }

    #[Framework\Attributes\Test]
    public function withValueMappingRegistersMappingForLegacyValue(): void
    {
        $this->subject->withValueMapping('fooValue', 'bazValue');

        $actual = $this->subject->resolve($this->parameters);

        self::assertNotNull($actual);
        self::assertSame('fooValue', $actual->value());
    }

    #[Framework\Attributes\Test]
    public function withValueMappingNormalizesLegacyValueBeforeMapping(): void
    {
        $this->subject->withValueMapping('fooValue', 'bazValue', true);

        $parameters = Runner\Extension\ParameterCollection::fromArray([
            'baz' => '   BAZValue   ',
        ]);

        $actual = $this->subject->resolve($parameters);

        self::assertNotNull($actual);
        self::assertSame('fooValue', $actual->value());
    }

    #[Framework\Attributes\Test]
    public function resolveReturnsEmptyMigrationIfNewParameterNameAlreadyExists(): void
    {
        $parameters = Runner\Extension\ParameterCollection::fromArray([
            'foo' => 'fooValue',
        ]);

        $expected = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration($this->subject, 'fooValue');

        self::assertEquals($expected, $this->subject->resolve($parameters));
    }

    #[Framework\Attributes\Test]
    public function resolveReturnsNullIfNeitherLegacyNorNewParametersAreSetAndNoDefaultValueIsDefined(): void
    {
        $parameters = Runner\Extension\ParameterCollection::fromArray([]);

        self::assertNull($this->subject->resolve($parameters));
    }

    #[Framework\Attributes\Test]
    public function resolveReturnsEmptyMigrationWithDefaultValueIfNeitherLegacyNorNewParametersAreSetAndDefaultValueIsDefined(): void
    {
        $parameters = Runner\Extension\ParameterCollection::fromArray([]);

        $expected = Src\PHPUnit\TextUI\Configuration\MigrationResult::forMigration($this->subject, 'fooValue');

        self::assertEquals($expected, $this->subject->resolve($parameters, 'fooValue'));
    }
}
