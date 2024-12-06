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

namespace Asset\Framework\Template\Form;

use Asset\Framework\Trait\SingletonTrait;
use InvalidArgumentException;

/**
 * Class that handles:
 *
 * @package Asset\Framework\View;
 */
class FormSMG
{

    use SingletonTrait;

    private const array TYPES
        = [
            'system'  => 'alert-primary',
            'error'   => 'alert-danger',
            'mail'    => 'alert-info',
            'form'    => 'alert-secondary',
            'default' => 'alert-dark',
            'success' => 'alert-success',
            'info'    => 'alert-info',
            'valid'   => 'valid-feedback',
            'invalid' => 'invalid-feedback',
            'warning' => 'alert-warning',
        ];

    /**
     * @var array
     */
    private array $smgField = [];

    /**
     * @var array
     */
    private array $currentMessage = [];

    /**
     * @return array|string[]
     */
    public static function getTypes(): array
    {
        return self::TYPES;
    }

    /**
     * @param array $array
     * @return FormSMG
     */
    public function setSMG(array $array): self
    {
        $this->validateMessage($array);
        $this->currentMessage = $array;
        $this->buildMessage();

        return $this;
    }

    /**
     * @param array $message
     * @return void
     */
    private function validateMessage(array $message): void
    {
        if (!isset($message['type']) || !isset($message['content'])) {
            throw new InvalidArgumentException('Type and content are required');
        }
    }

    /**
     * @return void
     */
    private function buildMessage(): void
    {
        $html = $this->currentMessage['in'] === 'modal'
            ? $this->buildModalMessage()
            : $this->buildInlineMessage();

        $this->smgField[] = $html;
    }

    /**
     * @return string
     */
    private function buildModalMessage(): string
    {
        $type       = strtolower($this->currentMessage['type']);
        $alertClass = self::TYPES[$type] ?? self::TYPES['default'];

        return sprintf(
            '<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header %s">
                            <h5 class="modal-title">%s</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">%s</div>
                        <div class="modal-footer">
                            %s
                        </div>
                    </div>
                </div>
            </div>',
            $alertClass,
            $this->currentMessage['title'] ?? 'Message',
            $this->currentMessage['content'],
            $this->buildModalButtons()
        );
    }

    /**
     * @return string
     */
    private function buildModalButtons(): string
    {
        if (!isset($this->currentMessage['buttons'])) {
            return '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        }

        $buttonsHtml = '';
        foreach ($this->currentMessage['buttons'] as $button) {
            $class   = $button['class'] ?? 'btn-secondary';
            $dismiss = isset($button['dismiss']) && $button['dismiss'] ? 'data-bs-dismiss="modal"' : '';
            $event   = isset($button['event']) ? 'name="event" value="'.$button['event'].'"' : '';

            $buttonsHtml .= sprintf(
                '<button type="button" class="btn %s" %s %s>%s</button>',
                $class,
                $dismiss,
                $event,
                $button['text']
            );
        }

        return $buttonsHtml;
    }

    /**
     * @return string
     */
    private function buildInlineMessage(): string
    {

        $type = strtolower($this->currentMessage['type']);

        if ($type === 'valid' || $type === 'invalid') {
            return sprintf(
                '<div class="%s">%s</div>',
                self::TYPES[$type],
                $this->currentMessage['content']
            );
        }

        $cardClass = match ($type) {
            'error' => 'border-danger text-danger',
            'system' => 'border-primary text-primary',
            'mail', 'info' => 'border-info text-info',
            'success' => 'border-success text-success',
            'warning' => 'border-warning text-warning',
            default => 'border-dark text-dark'
        };

        return sprintf(
            '<div class="card %s mb-10px">
                        <div class="card-body">
                            <blockquote class="blockquote">
                                <p class="mb-2">%s</p>
                            </blockquote>
                            %s
                        </div>
                    </div>',
            $cardClass,
            $this->currentMessage['content'],
            isset($this->currentMessage['footer']) ?
                sprintf(
                    '<figcaption class="blockquote-footer mt-n2 mb-1">%s %s</figcaption>',
                    $this->currentMessage['footer'],
                    isset($this->currentMessage['cite']) ?
                        sprintf('<cite title="%1$s">%1$s</cite>', $this->currentMessage['cite']) : ''
                ) : ''
        );
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->smgField;
    }

    /**
     * @return string
     */
    public function getLastMessage(): string
    {
        return end($this->smgField);
    }

}