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
        $this->registerClassAttributeTracers($facade, $parameters);
        $this->registerConstantAttributeTracers($facade, $parameters);
        $this->registerPackageAttributeTracers($facade, $parameters);
        $this->registerEnvAttributeTracers($facade, $parameters);
    }

    private function registerClassAttributeTracers(
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        // RequiresClass
        $facade->registerTracer(
            new Event\Tracer\RequiresClassAttributeTracer(
                new Metadata\ClassRequirements(),
                $this->resolveOutcomeBehavior('handleMissingClasses', $parameters),
            ),
        );

        // ForbidsClass
        $facade->registerTracer(
            new Event\Tracer\ForbidsClassAttributeTracer(
                new Metadata\ClassRequirements(),
                $this->resolveOutcomeBehavior('handleAvailableClasses', $parameters),
            ),
        );
    }

    private function registerConstantAttributeTracers(
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        // RequiresConstant
        $facade->registerTracer(
            new Event\Tracer\RequiresConstantAttributeTracer(
                new Metadata\ConstantRequirements(),
                $this->resolveOutcomeBehavior('handleUndefinedConstants', $parameters),
            ),
        );

        // ForbidsConstant
        $facade->registerTracer(
            new Event\Tracer\ForbidsConstantAttributeTracer(
                new Metadata\ConstantRequirements(),
                $this->resolveOutcomeBehavior('handleDefinedConstants', $parameters),
            ),
        );
    }

    private function registerPackageAttributeTracers(
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        // RequiresPackage
        $facade->registerTracer(
            new Event\Tracer\RequiresPackageAttributeTracer(
                new Metadata\PackageRequirements(),
                $this->resolveOutcomeBehavior('handleUnsatisfiedPackageRequirements', $parameters),
            ),
        );

        // ForbidsPackage
        $facade->registerTracer(
            new Event\Tracer\ForbidsPackageAttributeTracer(
                new Metadata\PackageRequirements(),
                $this->resolveOutcomeBehavior('handleSatisfiedPackageRequirements', $parameters),
            ),
        );
    }

    private function registerEnvAttributeTracers(
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        // RequiresEnv
        $facade->registerTracer(
            new Event\Tracer\RequiresEnvAttributeTracer(
                new Metadata\EnvRequirements(),
                $this->resolveOutcomeBehavior('handleMissingEnvironmentVariables', $parameters),
            ),
        );

        // ForbidsEnv
        $facade->registerTracer(
            new Event\Tracer\ForbidsEnvAttributeTracer(
                new Metadata\EnvRequirements(),
                $this->resolveOutcomeBehavior('handleAvailableEnvironmentVariables', $parameters),
            ),
        );
    }

    private function resolveOutcomeBehavior(
        string $name,
        Runner\Extension\ParameterCollection $parameters,
    ): Enum\OutcomeBehavior {
        if ($parameters->has($name)) {
            $behavior = Enum\OutcomeBehavior::tryFrom($parameters->get($name));
        } else {
            $behavior = null;
        }

        return $behavior ?? Enum\OutcomeBehavior::Skip;
    }
}
