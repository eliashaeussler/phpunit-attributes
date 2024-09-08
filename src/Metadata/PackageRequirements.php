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

namespace EliasHaeussler\PHPUnitAttributes\Metadata;

use Composer\InstalledVersions;
use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\IO;
use PHPUnit\Metadata;

use function class_exists;
use function fnmatch;
use function str_contains;

/**
 * PackageRequirements.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class PackageRequirements
{
    /**
     * @return non-empty-string|null
     */
    public function validateForAttribute(Attribute\RequiresPackage $attribute): ?string
    {
        $package = $this->findPackage($attribute->package());
        $versionRequirement = $attribute->versionRequirement();
        $message = $attribute->message();

        if (null === $package || !$this->isPackageInstalled($package)) {
            return $message ?? IO\Messages::forMissingRequiredPackage($package ?? $attribute->package());
        }

        if (null === $versionRequirement) {
            return null;
        }

        $requirement = Metadata\Version\ConstraintRequirement::from($versionRequirement);

        if (!$this->isPackageVersionSatisfied($package, $requirement)) {
            return $message ?? IO\Messages::forMissingRequiredPackage($package, $requirement);
        }

        return null;
    }

    /**
     * @param non-empty-string $packageName
     *
     * @return non-empty-string|null
     */
    private function findPackage(string $packageName): ?string
    {
        if (!str_contains($packageName, '*')) {
            return $packageName;
        }

        if (!@class_exists(InstalledVersions::class)) {
            return null; // @codeCoverageIgnore
        }

        foreach (InstalledVersions::getInstalledPackages() as $installedPackage) {
            if (fnmatch($packageName, $installedPackage)) {
                return $installedPackage;
            }
        }

        return null;
    }

    private function isPackageInstalled(string $packageName): bool
    {
        if (!@class_exists(InstalledVersions::class)) {
            return false; // @codeCoverageIgnore
        }

        return InstalledVersions::isInstalled($packageName);
    }

    private function isPackageVersionSatisfied(string $packageName, Metadata\Version\Requirement $requirement): bool
    {
        $packageVersion = InstalledVersions::getVersion($packageName);

        if (null === $packageVersion) {
            return false; // @codeCoverageIgnore
        }

        return $requirement->isSatisfiedBy($packageVersion);
    }
}
