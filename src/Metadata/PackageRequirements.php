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

namespace EliasHaeussler\PHPUnitAttributes\Metadata;

use Composer\InstalledVersions;
use EliasHaeussler\PHPUnitAttributes\Attribute;
use EliasHaeussler\PHPUnitAttributes\TextUI;
use PHPUnit\Metadata;

use function class_exists;
use function fnmatch;

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
    public function validateForAttribute(Attribute\RequiresPackage|Attribute\ForbidsPackage $attribute): ?string
    {
        $packages = $this->findPackages($attribute->package());
        $versionRequirement = $attribute->versionRequirement();
        $message = $attribute->message();

        // Early return if package cannot be resolved
        if ([] === $packages) {
            if ($attribute instanceof Attribute\RequiresPackage) {
                return $message ?? TextUI\Messages::forMissingPackage($attribute->package());
            }

            return null;
        }

        // Early return if package is installed, but no version requirement is given
        if (null === $versionRequirement) {
            if ($attribute instanceof Attribute\ForbidsPackage) {
                return $message ?? TextUI\Messages::forInstalledPackage($attribute->package());
            }

            return null;
        }

        $requirement = Metadata\Version\ConstraintRequirement::from($versionRequirement);

        foreach ($packages as $package) {
            $packageVersionSatisfied = $this->isPackageVersionSatisfied($package, $requirement);

            if (!$packageVersionSatisfied && $attribute instanceof Attribute\RequiresPackage) {
                return $message ?? TextUI\Messages::forMissingPackage($package, $requirement);
            }

            if ($packageVersionSatisfied && $attribute instanceof Attribute\ForbidsPackage) {
                return $message ?? TextUI\Messages::forInstalledPackage($package, $requirement);
            }
        }

        return null;
    }

    /**
     * @param non-empty-string $packageName
     *
     * @return list<non-empty-string>
     */
    private function findPackages(string $packageName): array
    {
        if (!@class_exists(InstalledVersions::class)) {
            return []; // @codeCoverageIgnore
        }

        $installedPackages = [];

        foreach (InstalledVersions::getInstalledPackages() as $installedPackage) {
            if (fnmatch($packageName, $installedPackage)) {
                $installedPackages[] = $installedPackage;
            }
        }

        return $installedPackages;
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
