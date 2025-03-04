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

namespace EliasHaeussler\PHPUnitAttributes\Event\Tracer;

use EliasHaeussler\PHPUnitAttributes\Enum;
use EliasHaeussler\PHPUnitAttributes\Reflection;
use PHPUnit\Event;
use PHPUnit\Framework;

use function array_filter;
use function array_keys;
use function array_values;
use function implode;

/**
 * AbstractAttributeTracer.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 *
 * @template TAttribute of object
 */
abstract class AbstractAttributeTracer implements Event\Tracer\Tracer
{
    protected Enum\OutcomeBehavior $defaultOutcomeBehavior = Enum\OutcomeBehavior::Skip;

    /**
     * @var array<class-string, array<non-empty-string, Enum\OutcomeBehavior>>
     */
    protected array $testClassBehaviorsCache = [];

    public function trace(Event\Event $event): void
    {
        if ($event instanceof Event\Test\BeforeTestMethodCalled) {
            $this->processAttributesOnClassLevel($event->testClassName());

            return;
        }

        if (!($event instanceof Event\Test\Prepared)) {
            return;
        }

        $test = $event->test();

        if ($test instanceof Event\Code\TestMethod) {
            $this->processAttributesOnClassLevel($test->className());
            $this->processAttributesOnMethodLevel($test->className(), $test->methodName());
        }
    }

    /**
     * @param class-string $testClassName
     */
    protected function processAttributesOnClassLevel(string $testClassName): void
    {
        $behaviors = $this->testClassBehaviorsCache[$testClassName] ?? [];

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }

        $classAttributes = Reflection\AttributeReflector::forClass($testClassName, $this->getAttributeClassName());
        $behaviors = $this->testClassBehaviorsCache[$testClassName] = $this->resolveBehaviorsFromAttributes($classAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }
    }

    /**
     * @param class-string $testClassName
     */
    protected function processAttributesOnMethodLevel(string $testClassName, string $testMethodName): void
    {
        $methodAttributes = Reflection\AttributeReflector::forClassMethod(
            $testClassName,
            $testMethodName,
            $this->getAttributeClassName(),
        );
        $behaviors = $this->resolveBehaviorsFromAttributes($methodAttributes);

        if ([] !== $behaviors) {
            $this->handleOutcomeBehavior($behaviors);
        }
    }

    /**
     * @param list<TAttribute> $attributes
     *
     * @return array<non-empty-string, Enum\OutcomeBehavior>
     */
    abstract protected function resolveBehaviorsFromAttributes(array $attributes): array;

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

        match (Enum\OutcomeBehavior::fromSet($outcomeBehaviors) ?? $this->defaultOutcomeBehavior) {
            Enum\OutcomeBehavior::Fail => Framework\Assert::fail($message),
            Enum\OutcomeBehavior::Skip => Framework\Assert::markTestSkipped($message),
        };
    }

    /**
     * @return class-string<TAttribute>
     */
    abstract protected function getAttributeClassName(): string;
}
