<?php
declare(strict_types=1);
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Container;

/**
 * Empty, no-op dependency injection container.
 */
class EmptyContainer implements Container
{
    /**
     * {@inheritDoc}
     */
    public function contains(string $name): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function containsType(string $type): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        throw new Exception\Missing("No container entry found for key: $id");
    }

    /**
     * {@inheritDoc}
     */
    public function getByType(string $type): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getFirst(string $type)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getNames(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?Container
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(string $name): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function has($id): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function named(string $name, string $type)
    {
        throw new \UnexpectedValueException("A $type was requested, but null was found");
    }
}
