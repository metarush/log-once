<?php

declare(strict_types=1);

namespace MetaRush\LogOnce\Pdo;

use MetaRush\LogOnce\AdapterInterface;
use MetaRush\DataAccess\DataAccess;
use Zkwbbr\Utils\AdjustedDateTimeByTimeZone;

class Adapter implements AdapterInterface
{
    private DataAccess $dal;
    private string $table;

    public function __construct(DataAccess $dataMapper, string $table)
    {
        $this->dal = $dataMapper;
        $this->table = $table;
    }

    public function log(string $hash, string $message, string $timeZone): void
    {
        $data = [
            'createdOn'   => AdjustedDateTimeByTimeZone::x('now', $timeZone, 'Y-m-d H:i:s'),
            'hash'        => $hash,
            'message'     => $message,
            'alreadyRead' => 0,
        ];

        $this->dal->create($this->table, $data);
    }

    public function logExistAndNotYetRead(string $hash): bool
    {
        $where = [
            'hash'        => $hash,
            'alreadyRead' => 0,
        ];

        $row = $this->dal->findOne($this->table, $where);

        return (bool) $row;
    }

}