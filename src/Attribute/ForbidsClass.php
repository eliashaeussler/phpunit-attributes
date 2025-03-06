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
 * FOrbidsClass.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class ForbidsClass
{
    /**
     * @param class-string          $className
     * @param non-empty-string|null $message
     */
    public function __construct(
        private readonly string $className,
        private readonly ?string $message = null,
        private readonly ?Enum\OutcomeBehavior $outcomeBehavior = null,
    ) {}

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
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
