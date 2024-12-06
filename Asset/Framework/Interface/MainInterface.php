<?php

namespace Asset\Framework\Interface;

use Asset\Framework\Http\Response;

/**
 * Interface MainInterface
 *
 * Defines the base contract for all system main handlers.
 *
 * @package Asset\Framework\Interface
 */
interface MainInterface
{
    /**
     * @return Response
     */
    public function process(): Response;

    /**
     * @return EventInterface|null
     */
    public function getEvent(): ?EventInterface;
}