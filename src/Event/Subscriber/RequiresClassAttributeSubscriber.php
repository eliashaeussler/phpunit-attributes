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
use EliasHaeussler\PHPUnitAttributes\Metadata;
use EliasHaeussler\PHPUnitAttributes\Reflection;
use PHPUnit\Event;
use PHPUnit\Framework;

use function implode;

/**
 * RequiresClassAttributeSubscriber.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class RequiresClassAttributeSubscriber implements Event\Test\PreparedSubscriber
{
    /**
     * @var array<non-empty-string, list<non-empty-string>>
     */
    private array $testClassMessagesCache = [];

    public function __construct(
        private readonly Metadata\ClassRequirements $classRequirements,
        private readonly bool $failOnMissingClasses,
    ) {}

    public function notify(Event\Test\Prepared $event): void
    {
        $test = $event->test();

        if (!($test instanceof Event\Code\TestMethod)) {
            return;
        }

        $testClassName = $test->className();
        $messages = $this->testClassMessagesCache[$testClassName] ?? [];

        if ([] !== $messages) {
            $this->skipTest($messages);
        }

        $classAttributes = Reflection\AttributeReflector::forClass(
            $testClassName,
            Attribute\RequiresClass::class,
        );
        $messages = $this->testClassMessagesCache[$testClassName] = $this->checkClassNames($classAttributes);

        if ([] !== $messages) {
            $this->skipTest($messages);
        }

        $methodAttributes = Reflection\AttributeReflector::forClassMethod(
            $testClassName,
            $test->methodName(),
            Attribute\RequiresClass::class,
        );
        $messages = $this->checkClassNames($methodAttributes);

        if ([] !== $messages) {
            $this->skipTest($messages);
        }
    }

    /**
     * @param list<Attribute\RequiresClass> $attributes
     *
     * @return list<non-empty-string>
     */
    private function checkClassNames(array $attributes): array
    {
        $notSatisfied = [];

        foreach ($attributes as $attribute) {
            $message = $this->classRequirements->validateForAttribute($attribute);

            if (null !== $message) {
                $notSatisfied[] = $message;
            }
        }

        return $notSatisfied;
    }

    /**
     * @param list<non-empty-string> $messages
     */
    private function skipTest(array $messages): never
    {
        $message = implode(PHP_EOL, $messages);

        if ($this->failOnMissingClasses) {
            Framework\Assert::fail($message);
        } else {
            Framework\Assert::markTestSkipped($message);
        }
    }
}