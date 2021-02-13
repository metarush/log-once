<?php

declare(strict_types=1);

namespace MetaRush\LogOnce\FileSystem;

use MetaRush\LogOnce\AdapterInterface;
use Zkwbbr\Utils\AdjustedDateTimeByTimeZone;
use Zkwbbr\Utils\FilesFromDirectory;

class Adapter implements AdapterInterface
{
    private string $logDir;

    public function __construct(string $logDir)
    {
        $this->logDir = $logDir;
    }

    public function log(string $hash, string $message, string $timeZone): void
    {
        $filename = AdjustedDateTimeByTimeZone::x('now', $timeZone, 'Y-m-d_H-i-s_O') . '__' . $hash . '.log';

        \file_put_contents($this->logDir . $filename, $message);
    }

    public function logExistAndNotYetRead(string $hash): bool
    {
        $logs = FilesFromDirectory::x($this->logDir, '~.*__' . $hash . '\.log$~');

        return (bool) $logs;
    }

}