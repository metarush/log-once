<?php

declare(strict_types=1);

namespace MetaRush\LogOnce;

interface AdapterInterface
{
    /**
     * Logs the message
     *
     * @param string $hash
     * @param string $message
     * @param string $timeZone
     * @return void
     */
    public function log(string $hash, string $message, string $timeZone): void;

    /**
     * Check if a log hash exists and not yet marked as read
     *
     * @param string $hash
     * @return bool
     */
    public function logExistAndNotYetRead(string $hash): bool;
}