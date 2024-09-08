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

namespace EliasHaeussler\PHPUnitAttributes\Codeception\Event\Subscriber;

use Codeception\Event;
use Codeception\Lib;
use Codeception\Test;
use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\Enum;
use EliasHaeussler\PHPUnitAttributes\IO;
use EliasHaeussler\PHPUnitAttributes\Metadata;
use EliasHaeussler\PHPUnitAttributes\Reflection;

use function in_array;

/**
 * RequiresPackageAttributeSubscriber.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RequiresPackageAttributeSubscriber implements Subscriber
{
    /**
     * @var list<Enum\OutcomeBehavior>
     */
    private static array $noticesPrinted = [];

    /**
     * @var array<class-string, array<non-empty-string, Enum\OutcomeBehavior>>
     */
    private array $testClassBehaviorsCache = [];

    public function __construct(
        private readonly Metadata\PackageRequirements $packageRequirements,
        private readonly Enum\OutcomeBehavior $behaviorOnUnsatisfiedPackageRequirements,
        private readonly Lib\Console\Output $output,
    ) {
        $this->displayNoticeOnUnsupportedConfiguredOutcomeBehavior();
    }

    public function notify(Event\TestEvent $event): void
    {
        $test = $event->getTest();
        $metadata = $test->getMetadata();
        /** @var class-string $testClassName */
        [$testClassName, $testMethodName] = explode(':', $test->getSignature(), 2);

        $this->processAttributesOnClassLevel($testClassName, $metadata);
        $this->processAttributesOnMethodLevel($testClassName, $testMethodName, $metadata);
    }

    /**
     * @param class-string $testClassName
     */
    private function processAttributesOnClassLevel(string $testClassName, Test\Metadata $metadata): void
    {
        $behaviors = $this->testClassBehaviorsCache[$testClassName] ?? [];

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors, $metadata);
        }

        $classAttributes = Reflection\AttributeReflector::forClass(
            $testClassName,
            Attribute\RequiresPackage::class,
        );
        $behaviors = $this->testClassBehaviorsCache[$testClassName] = $this->checkPackageRequirements($classAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors, $metadata);
        }
    }

    /**
     * @param class-string $testClassName
     */
    private function processAttributesOnMethodLevel(
        string $testClassName,
        string $testMethodName,
        Test\Metadata $metadata,
    ): void {
        $methodAttributes = Reflection\AttributeReflector::forClassMethod(
            $testClassName,
            $testMethodName,
            Attribute\RequiresPackage::class,
        );
        $behaviors = $this->checkPackageRequirements($methodAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors, $metadata);
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
    private function handleOutcomeBehavior(array $behaviors, Test\Metadata $metadata): void
    {
        $message = implode(PHP_EOL, array_keys($behaviors));
        $outcomeBehaviors = array_values(
            array_filter(
                $behaviors,
                static fn (?Enum\OutcomeBehavior $outcomeBehavior) => null !== $outcomeBehavior,
            ),
        );

        match (Enum\OutcomeBehavior::fromSet($outcomeBehaviors) ?? $this->behaviorOnUnsatisfiedPackageRequirements) {
            Enum\OutcomeBehavior::Fail, Enum\OutcomeBehavior::Skip => $metadata->setSkip($message),
        };
    }

    private function displayNoticeOnUnsupportedConfiguredOutcomeBehavior(): void
    {
        if (Enum\OutcomeBehavior::Fail === $this->behaviorOnUnsatisfiedPackageRequirements
            && !in_array($this->behaviorOnUnsatisfiedPackageRequirements, self::$noticesPrinted, true)
        ) {
            self::$noticesPrinted[] = $this->behaviorOnUnsatisfiedPackageRequirements;

            $message = IO\Messages::forUnsupportedConfiguredOutcomeBehavior(
                'handleUnsatisfiedPackageRequirements',
                $this->behaviorOnUnsatisfiedPackageRequirements,
                Enum\OutcomeBehavior::Skip,
            );

            $this->output->writeln('<comment>'.$message.'</comment>');
        }
    }
}
