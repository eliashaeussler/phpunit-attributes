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

/**
 * RequiresClassAttributeSubscriber.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RequiresClassAttributeSubscriber implements Subscriber
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
        private readonly Metadata\ClassRequirements $classRequirements,
        private readonly Enum\OutcomeBehavior $behaviorOnMissingClasses,
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
            Attribute\RequiresClass::class,
        );
        $behaviors = $this->testClassBehaviorsCache[$testClassName] = $this->checkClassNames($classAttributes);

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
            Attribute\RequiresClass::class,
        );
        $behaviors = $this->checkClassNames($methodAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors, $metadata);
        }
    }

    /**
     * @param list<Attribute\RequiresClass> $attributes
     *
     * @return array<non-empty-string, Enum\OutcomeBehavior>
     */
    private function checkClassNames(array $attributes): array
    {
        $notSatisfied = [];

        foreach ($attributes as $attribute) {
            $message = $this->classRequirements->validateForAttribute($attribute);

            if (null !== $message) {
                $notSatisfied[$message] = $attribute->outcomeBehavior() ?? $this->behaviorOnMissingClasses;
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

        match (Enum\OutcomeBehavior::fromSet($outcomeBehaviors) ?? $this->behaviorOnMissingClasses) {
            Enum\OutcomeBehavior::Fail, Enum\OutcomeBehavior::Skip => $metadata->setSkip($message),
        };
    }

    private function displayNoticeOnUnsupportedConfiguredOutcomeBehavior(): void
    {
        if (Enum\OutcomeBehavior::Fail === $this->behaviorOnMissingClasses
            && !in_array($this->behaviorOnMissingClasses, self::$noticesPrinted, true)
        ) {
            self::$noticesPrinted[] = $this->behaviorOnMissingClasses;

            $message = IO\Messages::forUnsupportedConfiguredOutcomeBehavior(
                'handleMissingClasses',
                $this->behaviorOnMissingClasses,
                Enum\OutcomeBehavior::Skip,
            );

            $this->output->writeln('<comment>'.$message.'</comment>');
        }
    }
}
