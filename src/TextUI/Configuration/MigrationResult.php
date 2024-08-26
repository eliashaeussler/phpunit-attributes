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

namespace EliasHaeussler\PHPUnitAttributes\TextUI\Configuration;

use Symfony\Component\Console;

use function class_exists;
use function sprintf;

/**
 * MigrationResult.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class MigrationResult
{
    private function __construct(
        private readonly Migration $migration,
        private readonly string $value,
        private readonly ?string $legacyValue,
    ) {}

    public static function forMigration(
        Migration $migration,
        string $value,
        ?string $legacyValue = null,
    ): self {
        return new self($migration, $value, $legacyValue);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function legacyValue(): ?string
    {
        return $this->legacyValue;
    }

    public function wasMigrated(): bool
    {
        return null !== $this->legacyValue && $this->value !== $this->legacyValue;
    }

    public function getDiffAsString(bool $colorize = true): ?string
    {
        if (!$this->wasMigrated()) {
            return null;
        }

        $legacyText = sprintf('- <parameter name="%s" value="%s" />', $this->migration->legacyName(), $this->legacyValue);
        $newText = sprintf('+ <parameter name="%s" value="%s" />', $this->migration->newName(), $this->value);

        // Try to colorize diff
        if ($colorize && class_exists(Console\Color::class)) {
            $redColor = new Console\Color('red');
            $greenColor = new Console\Color('green');
            $legacyText = $redColor->apply($legacyText);
            $newText = $greenColor->apply($newText);
        }

        return $legacyText.PHP_EOL.$newText;
    }
}
