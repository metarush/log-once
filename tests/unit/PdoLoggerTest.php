<?php

declare(strict_types=1);

namespace Tests;

use MetaRush\LogOnce\LogOnce;
use MetaRush\LogOnce\Pdo\Adapter;
use MetaRush\DataMapper\Builder;
use MetaRush\DataMapper\DataMapper;

class PdoLoggerTest extends Common
{
    private string $testDir = __DIR__ . '/testDir/';
    private string $dbFile;
    private string $table = 'test';
    private \PDO $pdo;
    private Adapter $adapter;
    private DataMapper $dataMapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->dbFile = $this->testDir . 'test.db';

        $dsn = 'sqlite:' . $this->dbFile;

        // create test db if doesn't exist yet
        if (!\file_exists($this->dbFile)) {

            $this->pdo = new \PDO($dsn);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->query('
                CREATE TABLE test (
                `id`            INTEGER PRIMARY KEY AUTOINCREMENT,
                `createdOn`     TEXT,
                `hash`          TEXT,
                `message`       TEXT,
                `alreadyRead`   INTEGER
            )');
        }

        // ------------------------------------------------

        $this->dataMapper = (new Builder)
            ->setDsn($dsn)
            ->build();

        $this->adapter = new Adapter($this->dataMapper, $this->table);

        $this->logger = (new LogOnce($this->adapter))
            ->setTimeZone('UTC');
    }

    public function tearDown(): void
    {
        // close the DB connections so unlink will work
        unset($this->dataMapper);
        unset($this->pdo);
        unset($this->adapter);
        unset($this->logger);

        if (\file_exists($this->dbFile))
            \unlink($this->dbFile);
    }

    public function test_log_logMessageThatDoesNotExist_pass()
    {
        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->log();

        // ------------------------------------------------

        $rows = $this->dataMapper->findAll($this->table);

        $this->assertCount(1, $rows);
    }

    public function test_log_logMessageThatAlreadyExist_nothingHappensPass()
    {
        // seed
        $data = [
            'createdOn'     => \date('Y-m-d H:i:s'),
            'hash'          => '12345',
            'message'       => 'test',
            'alreadyRead'   => 0
        ];
        $this->dataMapper->create($this->table, $data);

        // ------------------------------------------------

        $this->logger
            ->setHash('12345')
            ->setLogMessage('test')
            ->log();

        // ------------------------------------------------

        $rows = $this->dataMapper->findAll($this->table);

        $this->assertCount(1, $rows);
    }

}