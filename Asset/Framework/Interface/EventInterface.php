<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Framework\Interface;

/**
 * Interface EventInterface
 *
 * Defines the base contract for all system event handlers.
 *
 * @package Asset\Framework\Interface
 */
interface EventInterface
{

    /**
     * @return self|null
     */
    public function listenerEvent(): ?self;

    /**
     * @param MainInterface $main
     * @return self
     */
    public function setMain(MainInterface $main): self;

    /**
     * @param string $target
     * @param string $data
     * @param bool $type
     * @return void
     */
    public function setResponse(string $target, string $data, bool $type): void;

    /**
     * @param array $fields
     * @param array $message
     * @return array
     */
    public function getResponseData(array $fields, array $message): array;

}