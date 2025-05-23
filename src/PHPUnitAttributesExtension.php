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

namespace EliasHaeussler\PHPUnitAttributes;

use PHPUnit\Event\Facade;
use PHPUnit\Runner;
use PHPUnit\TextUI\Configuration;

use function array_unshift;
use function implode;

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
        $requiresClassMigrationResult = $this->registerClassAttributeTracers($facade, $parameters);
        $this->registerConstantAttributeTracers($facade, $parameters);
        $requiresPackageMigrationResult = $this->registerPackageAttributeTracers($facade, $parameters);
        $this->registerEnvAttributeTracers($facade, $parameters);

        $this->triggerDeprecationForMigratedConfigurationParameters(
            $configuration->colors(),
            $requiresPackageMigrationResult,
            $requiresClassMigrationResult,
        );
    }

    private function registerClassAttributeTracers(
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): TextUI\Configuration\MigrationResult {
        // RequiresClass
        // @todo Remove support of legacy parameter in v3 of the library
        $migrationResult = $this->migrateParameter(
            'handleMissingClasses',
            'failOnMissingClasses',
            $parameters,
        );

        $facade->registerTracer(
            new Event\Tracer\RequiresClassAttributeTracer(
                new Metadata\ClassRequirements(),
                Enum\OutcomeBehavior::tryFrom($migrationResult->value()) ?? Enum\OutcomeBehavior::Skip,
            ),
        );

        // ForbidsClass
        $facade->registerTracer(
            new Event\Tracer\ForbidsClassAttributeTracer(
                new Metadata\ClassRequirements(),
                $this->resolveOutcomeBehavior('handleAvailableClasses', $parameters),
            ),
        );

        return $migrationResult;
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
    ): TextUI\Configuration\MigrationResult {
        // RequiresPackage
        // @todo Remove support of legacy parameter in v3 of the library
        $migrationResult = $this->migrateParameter(
            'handleUnsatisfiedPackageRequirements',
            'failOnUnsatisfiedPackageRequirements',
            $parameters,
        );

        $facade->registerTracer(
            new Event\Tracer\RequiresPackageAttributeTracer(
                new Metadata\PackageRequirements(),
                Enum\OutcomeBehavior::tryFrom($migrationResult->value()) ?? Enum\OutcomeBehavior::Skip,
            ),
        );

        // ForbidsPackage
        $facade->registerTracer(
            new Event\Tracer\ForbidsPackageAttributeTracer(
                new Metadata\PackageRequirements(),
                $this->resolveOutcomeBehavior('handleSatisfiedPackageRequirements', $parameters),
            ),
        );

        return $migrationResult;
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

    /**
     * @param non-empty-string $new
     * @param non-empty-string $legacy
     */
    private function migrateParameter(
        string $new,
        string $legacy,
        Runner\Extension\ParameterCollection $parameters,
    ): TextUI\Configuration\MigrationResult {
        return TextUI\Configuration\Migration::forParameter($new, $legacy)
            ->withValueMapping(Enum\OutcomeBehavior::Fail->value, 'true', true)
            ->withValueMapping(Enum\OutcomeBehavior::Skip->value, 'false', true)
            ->resolve($parameters, Enum\OutcomeBehavior::Skip->value)
        ;
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

    private function triggerDeprecationForMigratedConfigurationParameters(
        bool $colorize,
        TextUI\Configuration\MigrationResult ...$migrationResults,
    ): void {
        $deprecationMessages = [];

        foreach ($migrationResults as $migrationResult) {
            if ($migrationResult->wasMigrated()) {
                $deprecationMessages[] = $migrationResult->getDiffAsString($colorize);
            }
        }

        if ([] === $deprecationMessages) {
            return;
        }

        // Early return if no deprecations are to be triggered
        array_unshift(
            $deprecationMessages,
            'Your XML configuration contains deprecated extension parameters. Migrate your XML configuration:',
        );

        $emitter = Facade::emitter();
        $emitter->testRunnerTriggeredPhpunitDeprecation(implode(PHP_EOL, $deprecationMessages));
    }
}
