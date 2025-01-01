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

namespace EliasHaeussler\PHPUnitAttributes\Tests\E2E;

use EliasHaeussler\PHPUnitAttributes as Src;
use PHPUnit\Framework;

/**
 * RequiresPackageAttributeWithPackagePatternTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RequiresPackageAttributeWithPackagePatternTest extends Framework\TestCase
{
    #[Framework\Attributes\Test]
    #[Src\Attribute\RequiresPackage('phpunit/*-coverage', '< 10')]
    public function fakeTest(): void
    {
        self::assertTrue(true);
    }

    #[Framework\Attributes\Test]
    #[Src\Attribute\RequiresPackage('foo/*')]
    public function anotherFakeTest(): void
    {
        self::assertTrue(true);
    }
}
