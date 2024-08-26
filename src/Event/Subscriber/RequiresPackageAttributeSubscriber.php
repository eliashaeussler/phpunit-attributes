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

namespace EliasHaeussler\PHPUnitAttributes\Event\Subscriber;

use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\Enum;
use EliasHaeussler\PHPUnitAttributes\Metadata;
use EliasHaeussler\PHPUnitAttributes\Reflection;
use PHPUnit\Event;
use PHPUnit\Framework;

use function array_filter;
use function array_keys;
use function array_values;
use function implode;

/**
 * RequiresPackageAttributeSubscriber.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RequiresPackageAttributeSubscriber implements Event\Test\PreparedSubscriber
{
    /**
     * @var array<non-empty-string, array<non-empty-string, Enum\OutcomeBehavior>>
     */
    private array $testClassBehaviorsCache = [];

    public function __construct(
        private readonly Metadata\PackageRequirements $packageRequirements,
        private readonly Enum\OutcomeBehavior $behaviorOnUnsatisfiedPackageRequirements,
    ) {}

    public function notify(Event\Test\Prepared $event): void
    {
        $test = $event->test();

        if (!($test instanceof Event\Code\TestMethod)) {
            return;
        }

        $testClassName = $test->className();
        $behaviors = $this->testClassBehaviorsCache[$testClassName] ?? [];

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }

        $classAttributes = Reflection\AttributeReflector::forClass(
            $testClassName,
            Attribute\RequiresPackage::class,
        );
        $behaviors = $this->testClassBehaviorsCache[$testClassName] = $this->checkPackageRequirements($classAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }

        $methodAttributes = Reflection\AttributeReflector::forClassMethod(
            $testClassName,
            $test->methodName(),
            Attribute\RequiresPackage::class,
        );
        $behaviors = $this->checkPackageRequirements($methodAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }
    }

    /**
     * @param list<Attribute\RequiresPackage> $attributes
     *
     * @return array<non-empty-string, Enum\OutcomeBehavior>
     */
    private function checkPackageRequirements(array $attributes): array
    {
        $notSatisfied = [];

        foreach ($attributes as $attribute) {
            $message = $this->packageRequirements->validateForAttribute($attribute);

            if (null !== $message) {
                $notSatisfied[$message] = $attribute->outcomeBehavior() ?? $this->behaviorOnUnsatisfiedPackageRequirements;
            }
        }

        return $notSatisfied;
    }

    /**
     * @param array<non-empty-string, Enum\OutcomeBehavior> $behaviors
     */
    private function handleOutcomeBehavior(array $behaviors): never
    {
        $message = implode(PHP_EOL, array_keys($behaviors));
        $outcomeBehaviors = array_values(
            array_filter(
                $behaviors,
                static fn (?Enum\OutcomeBehavior $outcomeBehavior) => null !== $outcomeBehavior,
            ),
        );

        match (Enum\OutcomeBehavior::fromSet($outcomeBehaviors) ?? $this->behaviorOnUnsatisfiedPackageRequirements) {
            Enum\OutcomeBehavior::Fail => Framework\Assert::fail($message),
            Enum\OutcomeBehavior::Skip => Framework\Assert::markTestSkipped($message),
        };
    }
}
