<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/phpunit-attributes".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\PHPUnitAttributes;

use PHPUnit\Runner;
use PHPUnit\TextUI\Configuration;

/**
 * PHPUnitAttributesExtension.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class PHPUnitAttributesExtension implements Runner\Extension\Extension
{
    public function bootstrap(
        Configuration\Configuration $configuration,
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        $facade->registerSubscribers(
            new Event\Subscriber\RequiresPackageAttributeSubscriber(
                new Metadata\PackageRequirements(),
                $this->parseBooleanParameter($parameters, 'failOnUnsatisfiedPackageRequirements'),
            ),
            new Event\Subscriber\RequiresClassAttributeSubscriber(
                new Metadata\ClassRequirements(),
                $this->parseBooleanParameter($parameters, 'failOnMissingClasses'),
            ),
        );
    }

    private function parseBooleanParameter(Runner\Extension\ParameterCollection $parameters, string $name): bool
    {
        if ($parameters->has($name)) {
            return TextUI\Configuration\Parameters::parseBooleanValue($parameters->get($name), false);
        }

        return false;
    }
}
