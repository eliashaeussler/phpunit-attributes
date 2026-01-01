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

namespace EliasHaeussler\PHPUnitAttributes\Event\Tracer;

use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\Enum;
use EliasHaeussler\PHPUnitAttributes\Metadata;

/**
 * RequiresConstantAttributeTracer.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 *
 * @extends AbstractAttributeTracer<Attribute\RequiresConstant>
 */
final class RequiresConstantAttributeTracer extends AbstractAttributeTracer
{
    public function __construct(
        private readonly Metadata\ConstantRequirements $constantRequirements,
        Enum\OutcomeBehavior $behaviorOnUndefinedConstants,
    ) {
        $this->defaultOutcomeBehavior = $behaviorOnUndefinedConstants;
    }

    protected function resolveBehaviorsFromAttributes(array $attributes): array
    {
        $notSatisfied = [];

        foreach ($attributes as $attribute) {
            $message = $this->constantRequirements->validateForAttribute($attribute);

            if (null !== $message) {
                $notSatisfied[$message] = $attribute->outcomeBehavior() ?? $this->defaultOutcomeBehavior;
            }
        }

        return $notSatisfied;
    }

    protected function getAttributeClassName(): string
    {
        return Attribute\RequiresConstant::class;
    }
}
