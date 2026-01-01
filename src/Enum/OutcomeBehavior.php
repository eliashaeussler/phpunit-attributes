<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/phpunit-attributes".
 *
 * Copyright (C) 2024-2026 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\PHPUnitAttributes\Enum;

use function reset;
use function usort;

/**
 * OutcomeBehavior.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
enum OutcomeBehavior: string
{
    case Fail = 'fail';
    case Skip = 'skip';

    /**
     * @param list<self> $outputBehaviors
     */
    public static function fromSet(array $outputBehaviors): ?self
    {
        if ([] === $outputBehaviors) {
            return null;
        }

        usort($outputBehaviors, static fn (self $a, self $b) => $b->getPriority() <=> $a->getPriority());

        return reset($outputBehaviors);
    }

    private function getPriority(): int
    {
        return match ($this) {
            self::Fail => 20,
            self::Skip => 10,
        };
    }
}
