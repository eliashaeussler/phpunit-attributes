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
 * ClassRequirementsTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Metadata\ClassRequirements::class)]
final class ClassRequirementsTest extends Framework\TestCase
{
    private Src\Metadata\ClassRequirements $subject;

    public function setUp(): void
    {
        $this->subject = new Src\Metadata\ClassRequirements();
    }

    /**
     * @param non-empty-string|null $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfClassDoesNotExistDataProvider')]
    public function validateForAttributeReturnsMessageIfClassDoesNotExist(?string $message, string $expected): void
    {
        $attribute = new Src\Attribute\RequiresClass('Foo\\Baz', $message);

        self::assertSame($expected, $this->subject->validateForAttribute($attribute));
    }

    /**
     * @param non-empty-string|null $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfClassExistsDataProvider')]
    public function validateForAttributeReturnsMessageIfClassExists(?string $message, string $expected): void
    {
        $attribute = new Src\Attribute\ForbidsClass(self::class, $message);

        self::assertSame($expected, $this->subject->validateForAttribute($attribute));
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfClassExists(): void
    {
        $attribute = new Src\Attribute\RequiresClass(self::class);

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfClassDoesNotExist(): void
    {
        $attribute = new Src\Attribute\ForbidsClass('Foo\\Baz');

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    /**
     * @return Generator<string, array{non-empty-string|null, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfClassDoesNotExistDataProvider(): Generator
    {
        yield 'no message' => [null, Src\TextUI\Messages::forMissingClass('Foo\\Baz')];
        yield 'custom message' => ['Foo\\Baz is missing, sorry!', 'Foo\\Baz is missing, sorry!'];
    }

    /**
     * @return Generator<string, array{non-empty-string|null, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfClassExistsDataProvider(): Generator
    {
        yield 'no message' => [null, Src\TextUI\Messages::forAvailableClass(self::class)];
        yield 'custom message' => ['Class is available, sorry!', 'Class is available, sorry!'];
    }
}
