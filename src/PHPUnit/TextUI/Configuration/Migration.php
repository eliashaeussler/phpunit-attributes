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

namespace EliasHaeussler\PHPUnitAttributes\PHPUnit\TextUI\Configuration;

use PHPUnit\Runner;

use function preg_quote;

/**
 * Migration.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class Migration
{
    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private array $valueMapping = [];

    /**
     * @param non-empty-string $newName
     * @param non-empty-string $legacyName
     */
    private function __construct(
        private readonly string $newName,
        private readonly string $legacyName,
    ) {}

    /**
     * @param non-empty-string $new
     * @param non-empty-string $legacy
     */
    public static function forParameter(string $new, string $legacy): self
    {
        return new self($new, $legacy);
    }

    /**
     * @param non-empty-string $newValue
     * @param non-empty-string $legacyValue
     */
    public function withValueMapping(string $newValue, string $legacyValue, bool $normalize = false): self
    {
        if ($normalize) {
            $legacyValueRegex = '/^\\s*'.preg_quote($legacyValue, '/').'\\s*$/i';
        } else {
            $legacyValueRegex = '/^'.preg_quote($legacyValue, '/').'$/';
        }

        $this->valueMapping[$legacyValueRegex] = $newValue;

        return $this;
    }

    /**
     * @param non-empty-string|null $default
     *
     * @phpstan-return ($default is null ? MigrationResult|null : MigrationResult)
     */
    public function resolve(Runner\Extension\ParameterCollection $parameters, ?string $default = null): ?MigrationResult
    {
        if ($parameters->has($this->newName)) {
            return MigrationResult::forMigration($this, $parameters->get($this->newName));
        }

        if (!$parameters->has($this->legacyName)) {
            return null !== $default ? MigrationResult::forMigration($this, $default) : null;
        }

        $legacyValue = $parameters->get($this->legacyName);
        $newValue = $legacyValue;

        foreach ($this->valueMapping as $legacyValueRegex => $newName) {
            if (1 === preg_match($legacyValueRegex, $legacyValue)) {
                $newValue = $newName;
                break;
            }
        }

        return MigrationResult::forMigration($this, $newValue, $legacyValue);
    }

    /**
     * @return non-empty-string
     */
    public function newName(): string
    {
        return $this->newName;
    }

    /**
     * @return non-empty-string
     */
    public function legacyName(): string
    {
        return $this->legacyName;
    }
}
