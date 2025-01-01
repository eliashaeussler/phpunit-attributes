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
use PHPUnit\Metadata;

/**
 * PackageRequirementsTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Metadata\PackageRequirements::class)]
final class PackageRequirementsTest extends Framework\TestCase
{
    private Src\Metadata\PackageRequirements $subject;

    public function setUp(): void
    {
        $this->subject = new Src\Metadata\PackageRequirements();
    }

    /**
     * @param non-empty-string $packageName
     * @param non-empty-string $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfPackageIsNotInstalledDataProvider')]
    public function validateForAttributeReturnsMessageIfPackageIsNotInstalled(string $packageName, string $message): void
    {
        $attribute = new Src\Attribute\RequiresPackage($packageName, message: $message);

        self::assertSame($message, $this->subject->validateForAttribute($attribute));
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfPackageIsInstalledAndNoSpecificVersionIsRequired(): void
    {
        $attribute = new Src\Attribute\RequiresPackage('phpunit/phpunit');

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    /**
     * @param non-empty-string $packageName
     * @param non-empty-string $versionRequirement
     * @param non-empty-string $message
     */
    #[Framework\Attributes\Test]
    #[Framework\Attributes\DataProvider('validateForAttributeReturnsMessageIfVersionRequirementIsNotSatisfiedDataProvider')]
    public function validateForAttributeReturnsMessageIfVersionRequirementIsNotSatisfied(
        string $packageName,
        string $versionRequirement,
        string $message,
    ): void {
        $attribute = new Src\Attribute\RequiresPackage($packageName, $versionRequirement, $message);

        self::assertSame($message, $this->subject->validateForAttribute($attribute));
    }

    #[Framework\Attributes\Test]
    public function validateForAttributeReturnsNullIfPackageIsInstalledAndVersionRequirementIsSatisfied(): void
    {
        $attribute = new Src\Attribute\RequiresPackage('phpunit/phpunit', '>= 10');

        self::assertNull($this->subject->validateForAttribute($attribute));
    }

    /**
     * @return Generator<string, array{non-empty-string, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfPackageIsNotInstalledDataProvider(): Generator
    {
        yield 'package name' => ['foo/baz', Src\TextUI\Messages::forMissingRequiredPackage('foo/baz')];
        yield 'package name with custom message' => ['foo/baz', 'foo/baz is missing, sorry!'];
        yield 'package pattern' => ['foo/*', Src\TextUI\Messages::forMissingRequiredPackage('foo/*')];
        yield 'package pattern with custom message' => ['foo/*', 'foo/* packages are missing, sorry!'];
    }

    /**
     * @return Generator<string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function validateForAttributeReturnsMessageIfVersionRequirementIsNotSatisfiedDataProvider(): Generator
    {
        $versionRequirement = Metadata\Version\ConstraintRequirement::from('< 10');

        yield 'package name' => [
            'phpunit/phpunit',
            '< 10',
            Src\TextUI\Messages::forMissingRequiredPackage('phpunit/phpunit', $versionRequirement),
        ];
        yield 'package name with custom message' => [
            'phpunit/phpunit',
            '< 10',
            'PHPUnit < 10 is missing, sorry!',
        ];
        yield 'package pattern' => [
            'phpunit/*-coverage',
            '< 10',
            Src\TextUI\Messages::forMissingRequiredPackage('phpunit/php-code-coverage'),
        ];
        yield 'package pattern with custom message' => [
            'phpunit/*-coverage',
            '< 10',
            'phpunit/php-code-coverage < 10 is missing, sorry!',
        ];
    }
}
