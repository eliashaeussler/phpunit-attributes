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

/**
 * ConstantRequirementsTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Metadata\ConstantRequirements::class)]
final class ConstantRequirementsTest extends Framework\TestCase
{
    private Src\Metadata\ConstantRequirements $subject;

    public function setUp(): void
    {
        $this->subject = new Src\Metadata\ConstantRequirements();
    }

    /**
     * @param non-empty-string|null $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfConstantIsUndefinedDataProvider')]
    public function validateForAttributeReturnsMessageIfConstantIsUndefined(?string $message, string $expected): void
    {
        $attribute = new Src\Attribute\RequiresConstant('FOO_BAZ', $message);

        self::assertSame($expected, $this->subject->validateForAttribute($attribute));
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfConstantIsDefined(): void
    {
        $attribute = new Src\Attribute\RequiresConstant('PHP_VERSION');

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    /**
     * @return Generator<string, array{non-empty-string|null, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfConstantIsUndefinedDataProvider(): Generator
    {
        yield 'no message' => [null, Src\TextUI\Messages::forUndefinedConstant('FOO_BAZ')];
        yield 'custom message' => ['FOO_BAZ is undefined, sorry!', 'FOO_BAZ is undefined, sorry!'];
    }
}
