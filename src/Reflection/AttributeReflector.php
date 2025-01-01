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

namespace EliasHaeussler\PHPUnitAttributes\Reflection;

use ReflectionAttribute;
use ReflectionClass;

/**
 * AttributeReflector.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class AttributeReflector
{
    /**
     * @template T of object
     *
     * @param class-string    $className
     * @param class-string<T> $attributeClassName
     *
     * @return list<T>
     */
    public static function forClass(string $className, string $attributeClassName): array
    {
        $reflectionClass = new ReflectionClass($className);
        $reflectionAttributes = $reflectionClass->getAttributes($attributeClassName);

        return self::buildAttributeInstances($reflectionAttributes);
    }

    /**
     * @template T of object
     *
     * @param class-string    $className
     * @param class-string<T> $attributeClassName
     *
     * @return list<T>
     */
    public static function forClassMethod(string $className, string $methodName, string $attributeClassName): array
    {
        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionAttributes = $reflectionMethod->getAttributes($attributeClassName);

        return self::buildAttributeInstances($reflectionAttributes);
    }

    /**
     * @template T of object
     *
     * @param array<ReflectionAttribute<T>> $reflectedAttributes
     *
     * @return list<T>
     */
    private static function buildAttributeInstances(array $reflectedAttributes): array
    {
        $instances = [];

        foreach ($reflectedAttributes as $attribute) {
            $instances[] = $attribute->newInstance();
        }

        return $instances;
    }
}
