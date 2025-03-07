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

namespace EliasHaeussler\PHPUnitAttributes\Metadata;

use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\TextUI;

use function getenv;

/**
 * EnvRequirements.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class EnvRequirements
{
    /**
     * @return non-empty-string|null
     */
    public function validateForAttribute(Attribute\RequiresEnv|Attribute\ForbidsEnv $attribute): ?string
    {
        $envVariableName = $attribute->envVariableName();
        $message = $attribute->message();
        $envVariableValue = getenv($envVariableName);

        if (false === $envVariableValue) {
            $envVariableValue = $_ENV[$envVariableName] ?? null;
        }

        if (null === $envVariableValue && $attribute instanceof Attribute\RequiresEnv) {
            return $message ?? TextUI\Messages::forMissingEnvironmentVariable($envVariableName);
        }

        if (null !== $envVariableValue && $attribute instanceof Attribute\ForbidsEnv) {
            return $message ?? TextUI\Messages::forAvailableEnvironmentVariable($envVariableName);
        }

        return null;
    }
}
