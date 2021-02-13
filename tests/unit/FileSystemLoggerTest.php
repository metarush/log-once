<?php

declare(strict_types=1);

namespace Tests;

use MetaRush\LogOnce\LogOnce;
use MetaRush\LogOnce\FileSystem\Adapter;
use MetaRush\Notifier\Pushover\Builder as PushoverNotifier;
use Zkwbbr\Utils\FilesFromDirectory;

class FileSystemLoggerTest extends Common
{
    private string $testDir = __DIR__ . '/testDir/';

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new Adapter($this->testDir);

        $this->logger = (new LogOnce($this->adapter))
            ->setTimeZone('UTC');
    }

    public function tearDown(): void
    {
        $logs = FilesFromDirectory::x($this->testDir);

        foreach ($logs as $v)
            \unlink($this->testDir . $v);
    }

    public function test_log_logMessageThatDoesNotExist_pass()
    {
        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->log();

        // ------------------------------------------------

        $files = FilesFromDirectory::x($this->testDir);

        $this->assertCount(1, $files);
    }

    public function test_log_logMessageThatAlreadyExist_nothingHappensPass()
    {
        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->log();

        // ------------------------------------------------

        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->log();

        // ------------------------------------------------

        $files = FilesFromDirectory::x($this->testDir);

        $this->assertCount(1, $files);
    }

    public function test_log_logMessageWithPushoverNotification_pass()
    {
        $notifiers = [
                (new PushoverNotifier)
                ->addAccount($_ENV['MRL_PUSHOVER_APP_KEY'], $_ENV['MRL_PUSHOVER_USER_KEY'])
                ->setSubject('test subject')
                ->setBody('test body')
                ->build()
        ];

        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->setNotifiers($notifiers)
            ->log();

        // ------------------------------------------------

        $files = FilesFromDirectory::x($this->testDir);

        $this->assertCount(1, $files);
    }

}