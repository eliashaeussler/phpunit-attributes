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

use BackedEnum;
use Codeception\Event;
use Codeception\Events;
use Codeception\Extension;

use function is_a;
use function is_object;
use function is_string;

/**
 * CodeceptionAttributesExtension.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class CodeceptionAttributesExtension extends Extension
{
    /**
     * @var array<string, string>
     */
    protected static array $events = [
        Events::TEST_START => 'onTestStart',
    ];

    protected array $config = [
        'handleMissingClasses' => Enum\OutcomeBehavior::Skip,
        'handleUnsatisfiedPackageRequirements' => Enum\OutcomeBehavior::Skip,
    ];

    /**
     * @var list<Codeception\Event\Subscriber\Subscriber>
     */
    private array $subscribers = [];

    public function _initialize(): void
    {
        $this->subscribers[] = new Codeception\Event\Subscriber\RequiresClassAttributeSubscriber(
            new Metadata\ClassRequirements(),
            $this->parseConfigurationEnum(
                'handleMissingClasses',
                Enum\OutcomeBehavior::class,
                Enum\OutcomeBehavior::Skip,
            ),
            $this->output,
        );
        $this->subscribers[] = new Codeception\Event\Subscriber\RequiresPackageAttributeSubscriber(
            new Metadata\PackageRequirements(),
            $this->parseConfigurationEnum(
                'handleUnsatisfiedPackageRequirements',
                Enum\OutcomeBehavior::class,
                Enum\OutcomeBehavior::Skip,
            ),
            $this->output,
        );
    }

    public function onTestStart(Event\TestEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->notify($event);
        }
    }

    /**
     * @template T of BackedEnum
     *
     * @param class-string<T> $enum
     * @param T               $default
     *
     * @return T
     */
    private function parseConfigurationEnum(string $config, string $enum, BackedEnum $default): BackedEnum
    {
        $value = $this->config[$config] ?? null;

        if (null === $value) {
            return $default;
        }

        if (is_string($value)) {
            return $enum::tryFrom($value) ?? $default;
        }

        if (is_object($value) && is_a($value, $enum)) {
            return $value;
        }

        return $default;
    }
}
