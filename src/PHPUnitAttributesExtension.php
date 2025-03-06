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
use function method_exists;

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
        [$requiresPackageMigrationResult, $requiresClassMigrationResult] = $this->migrateConfigurationParameters($parameters);

        $facade->registerTracer(
            new Event\Tracer\RequiresPackageAttributeTracer(
                new Metadata\PackageRequirements(),
                Enum\OutcomeBehavior::tryFrom($requiresPackageMigrationResult->value()) ?? Enum\OutcomeBehavior::Skip,
            ),
        );
        $facade->registerTracer(
            new Event\Tracer\RequiresClassAttributeTracer(
                new Metadata\ClassRequirements(),
                Enum\OutcomeBehavior::tryFrom($requiresClassMigrationResult->value()) ?? Enum\OutcomeBehavior::Skip,
            ),
        );

        if ($parameters->has('handleUndefinedConstants')) {
            $handleUndefinedConstants = Enum\OutcomeBehavior::tryFrom($parameters->get('handleUndefinedConstants'));
        } else {
            $handleUndefinedConstants = null;
        }

        $facade->registerTracer(
            new Event\Tracer\RequiresConstantAttributeTracer(
                new Metadata\ConstantRequirements(),
                $handleUndefinedConstants ?? Enum\OutcomeBehavior::Skip,
            ),
        );

        if ($parameters->has('handleDefinedConstants')) {
            $handleDefinedConstants = Enum\OutcomeBehavior::tryFrom($parameters->get('handleDefinedConstants'));
        } else {
            $handleDefinedConstants = null;
        }

        $facade->registerTracer(
            new Event\Tracer\ForbidsConstantAttributeTracer(
                new Metadata\ConstantRequirements(),
                $handleDefinedConstants ?? Enum\OutcomeBehavior::Skip,
            ),
        );

        $this->triggerDeprecationForMigratedConfigurationParameters(
            $configuration->colors(),
            $requiresPackageMigrationResult,
            $requiresClassMigrationResult,
        );
    }

    /**
     * @return list<TextUI\Configuration\MigrationResult>
     */
    private function migrateConfigurationParameters(Runner\Extension\ParameterCollection $parameters): array
    {
        // RequiresPackage
        // @todo Remove support of legacy parameter in v3 of the library
        $requiresPackageMigration = TextUI\Configuration\Migration::forParameter(
            'handleUnsatisfiedPackageRequirements',
            'failOnUnsatisfiedPackageRequirements',
        );
        $requiresPackageMigration->withValueMapping(Enum\OutcomeBehavior::Fail->value, 'true', true);
        $requiresPackageMigration->withValueMapping(Enum\OutcomeBehavior::Skip->value, 'false', true);
        $requiresPackageMigrationResult = $requiresPackageMigration->resolve($parameters, Enum\OutcomeBehavior::Skip->value);

        // RequiresClass
        // @todo Remove support of legacy parameter in v3 of the library
        $requiresClassMigration = TextUI\Configuration\Migration::forParameter(
            'handleMissingClasses',
            'failOnMissingClasses',
        );
        $requiresClassMigration->withValueMapping(Enum\OutcomeBehavior::Fail->value, 'true', true);
        $requiresClassMigration->withValueMapping(Enum\OutcomeBehavior::Skip->value, 'false', true);
        $requiresClassMigrationResult = $requiresClassMigration->resolve($parameters, Enum\OutcomeBehavior::Skip->value);

        return [
            $requiresPackageMigrationResult,
            $requiresClassMigrationResult,
        ];
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

        if ([] !== $deprecationMessages) {
            array_unshift(
                $deprecationMessages,
                'Your XML configuration contains deprecated extension parameters. Migrate your XML configuration:',
            );

            $emitter = Facade::emitter();

            if (method_exists($emitter, 'testRunnerTriggeredPhpunitDeprecation')) {
                $emitter->testRunnerTriggeredPhpunitDeprecation(implode(PHP_EOL, $deprecationMessages));
            } else {
                // @todo Remove once support for PHPUnit v10 (PHP 8.1) is dropped
                $emitter->testRunnerTriggeredDeprecation(implode(PHP_EOL, $deprecationMessages));
            }
        }
    }
}
