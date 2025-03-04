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

namespace EliasHaeussler\PHPUnitAttributes\Tests\TextUI;

use EliasHaeussler\PHPUnitAttributes as Src;
use PHPUnit\Framework;
use PHPUnit\Metadata\Version\ConstraintRequirement;

/**
 * MessagesTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\TextUI\Messages::class)]
final class MessagesTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    public function forMissingRequiredPackageReturnMessageForGivenPackage(): void
    {
        self::assertSame(
            'Package "foo/baz" is required.',
            Src\TextUI\Messages::forMissingRequiredPackage('foo/baz'),
        );
    }

    #[Framework\Attributes\Test]
    public function forMissingRequiredPackageReturnMessageForGivenPackageAndVersionRequirement(): void
    {
        $versionRequirement = ConstraintRequirement::from('> 10');

        self::assertSame(
            'Package "foo/baz" (> 10) is required.',
            Src\TextUI\Messages::forMissingRequiredPackage('foo/baz', $versionRequirement),
        );
    }

    #[Framework\Attributes\Test]
    public function forMissingClassReturnsMessageForGivenClassName(): void
    {
        self::assertSame(
            'Class "'.self::class.'" is required.',
            Src\TextUI\Messages::forMissingClass(self::class),
        );
    }

    #[Framework\Attributes\Test]
    public function forUndefinedConstantReturnsMessageForGivenConstant(): void
    {
        self::assertSame(
            'Constant "FOO_BAZ" is required.',
            Src\TextUI\Messages::forUndefinedConstant('FOO_BAZ'),
        );
    }

    #[Framework\Attributes\Test]
    public function forDefinedConstantReturnsMessageForGivenConstant(): void
    {
        self::assertSame(
            'Constant "FOO_BAZ" is forbidden.',
            Src\TextUI\Messages::forDefinedConstant('FOO_BAZ'),
        );
    }
}
