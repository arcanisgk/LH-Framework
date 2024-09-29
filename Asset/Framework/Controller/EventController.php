<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Framework\Controller;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Controller;
 */
class EventController
{

    /**
     * @var EventController|null Singleton instance of the class: EventController.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class EventController.
     *
     * @return EventController The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * EventController constructor.
     */
    public function __construct()
    {
        $this->setResponseError(false);
    }

    /**
     * @var array
     */
    public array $response = [];

    /**
     * @var bool
     */
    private bool $response_error;

    /**
     * @return bool
     */
    public function isResponseError(): bool
    {
        return $this->response_error;
    }

    /**
     * @param bool $response_error
     */
    public function setResponseError(bool $response_error): void
    {
        $this->response_error = $response_error;
    }

    /**
     * @param string $target
     * @param string $data
     * @param bool $type
     * @return void
     */
    public function setResponse(string $target, string $data, bool $type = false): void
    {
        $this->setResponseError($type);
        $this->response = ['target' => $target, 'content' => $data];
    }

    /**
     * @param $fields
     * @param $message
     * @return array
     */
    public function getResponseData($fields, $message): array
    {
        $response_data = [];
        $error         = $this->isResponseError() ? 'danger' : 'success';
        foreach (array_merge($fields, $message) as $key => $data) {
            if ($this->response['target'] == $key) {
                $response_data[$key] = '<label class="text-'.$error.' fw-bold">'.$this->response['content'].'</label>';
            } else {
                $response_data[$key] = $data;
            }
        }

        return $response_data;
    }
}