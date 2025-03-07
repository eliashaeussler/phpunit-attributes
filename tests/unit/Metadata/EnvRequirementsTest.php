<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/phpunit-attributes".
 *
 * Copyright (C) 2024-2025 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\PHPUnitAttributes\Tests\Metadata;

use EliasHaeussler\PHPUnitAttributes as Src;
use Generator;
use PHPUnit\Framework;

use function putenv;

/**
 * EnvRequirementsTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Metadata\EnvRequirements::class)]
final class EnvRequirementsTest extends Framework\TestCase
{
    private Src\Metadata\EnvRequirements $subject;

    public function setUp(): void
    {
        $this->subject = new Src\Metadata\EnvRequirements();
    }

    /**
     * @param non-empty-string|null $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfEnvironmentVariableDoesNotExistDataProvider')]
    public function validateForAttributeReturnsMessageIfEnvironmentVariableDoesNotExist(?string $message, string $expected): void
    {
        $attribute = new Src\Attribute\RequiresEnv('FOO_BAZ', $message);

        self::assertSame($expected, $this->subject->validateForAttribute($attribute));
    }

    /**
     * @param non-empty-string|null $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfEnvironmentVariableExistsDataProvider')]
    public function validateForAttributeReturnsMessageIfEnvironmentVariableExists(?string $message, string $expected): void
    {
        $attribute = new Src\Attribute\ForbidsEnv('FOO_BAZ', $message);

        putenv('FOO_BAZ=bar');

        self::assertSame($expected, $this->subject->validateForAttribute($attribute));

        putenv('FOO_BAZ');
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfEnvironmentVariableExists(): void
    {
        $attribute = new Src\Attribute\RequiresEnv('FOO_BAZ');

        putenv('FOO_BAZ=bar');

        self::assertNull($this->subject->validateForAttribute($attribute));

        putenv('FOO_BAZ');
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfEnvironmentVariableDoesNotExist(): void
    {
        $attribute = new Src\Attribute\ForbidsEnv('FOO_BAZ');

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    /**
     * @return Generator<string, array{non-empty-string|null, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfEnvironmentVariableDoesNotExistDataProvider(): Generator
    {
        yield 'no message' => [null, Src\TextUI\Messages::forMissingEnvironmentVariable('FOO_BAZ')];
        yield 'custom message' => ['FOO_BAZ is missing, sorry!', 'FOO_BAZ is missing, sorry!'];
    }

    /**
     * @return Generator<string, array{non-empty-string|null, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfEnvironmentVariableExistsDataProvider(): Generator
    {
        yield 'no message' => [null, Src\TextUI\Messages::forAvailableEnvironmentVariable('FOO_BAZ')];
        yield 'custom message' => ['Env is available, sorry!', 'Env is available, sorry!'];
    }
}
