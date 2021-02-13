<?php

declare(strict_types=1);

namespace MetaRush\LogOnce;

use MetaRush\LogOnce\AdapterInterface;

class LogOnce
{
    private AdapterInterface $logger;

    /**
     *
     * @var array<\MetaRush\Notifier\Notifier>
     */
    private array $notifiers = [];
    private string $hash;
    private string $logMessage;
    private string $timeZone = 'UTC';

    public function __construct(AdapterInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log(): void
    {
        if ($this->logger->logExistAndNotYetRead($this->hash))
            return;

        // ------------------------------------------------

        $this->logger->log($this->hash, $this->logMessage, $this->timeZone);

        // ------------------------------------------------

        if ($this->notifiers)
            foreach ($this->notifiers as $v) {
                /** @var \MetaRush\Notifier\Notifier $v */
                $v->send();
            }
    }

    /**
     *
     * @param array<\MetaRush\Notifier\Notifier> $notifiers
     * @return self
     */
    public function setNotifiers(array $notifiers): self
    {
        $this->notifiers = $notifiers;
        return $this;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function setLogMessage(string $logMessage): self
    {
        $this->logMessage = $logMessage;
        return $this;
    }

    public function setTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;
        return $this;
    }

}