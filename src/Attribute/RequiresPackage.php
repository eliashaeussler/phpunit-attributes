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

namespace EliasHaeussler\PHPUnitAttributes\Attribute;

use Attribute;
use EliasHaeussler\PHPUnitAttributes\Enum;

/**
 * RequiresPackage.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class RequiresPackage
{
    /**
     * @param non-empty-string      $package
     * @param non-empty-string|null $versionRequirement
     * @param non-empty-string|null $message
     */
    public function __construct(
        private string $package,
        private ?string $versionRequirement = null,
        private ?string $message = null,
        private ?Enum\OutcomeBehavior $outcomeBehavior = null,
    ) {}

    /**
     * @return non-empty-string
     */
    public function package(): string
    {
        return $this->package;
    }

    /**
     * @return non-empty-string|null
     */
    public function versionRequirement(): ?string
    {
        return $this->versionRequirement;
    }

    /**
     * @return non-empty-string|null
     */
    public function message(): ?string
    {
        return $this->message;
    }

    public function outcomeBehavior(): ?Enum\OutcomeBehavior
    {
        return $this->outcomeBehavior;
    }
}
