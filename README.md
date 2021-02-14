# metarush/log-once

Log messages, usually error messages, only once and optionally get notified.
No more flood of error logs with the same message.

## Install

Install via composer as `metarush/log-once`

## Sample usage

```php

use MetaRush\LogOnce\LogOnce;

$message = 'This is my log';

$hash = \crc32($message); // you can use any hash function of your choice e.g., \md5(), \sha1(), etc

(new LogOnce($adapter)) // see adapter examples below
    ->setTimeZone('UTC')
    ->setHash($hash)
    ->setLogMessage($message)
    ->setNotifiers($notifiers) // optional, see below
    ->log();
```

## Log adapters

### File system

```php

use MetaRush\LogOnce\FileSystem\Adapter as FileSytemLogger;

$logDir = '/path/to/logs';

$adapter = new FileSytemLogger($logDir);
```

Note: To mark a log file as read, rename it with a `__ALREADYREAD` suffix
e.g., `2021-01-01_00-00-00_+0000__12345__ALREADYREAD.log` or you can simply delete the file.

### PDO database (e.g., MySQL, PostgreSQL, SQLite)

Create a table with ff. fields:

- `id`        INTEGER PRIMARY KEY AUTOINCREMENT,
- `createdOn`    DATETIME, // must use YYYY-MM-DD HH:MM:SS
- `hash`        TEXT, // make length as long as your hash function's output e.g., if \md5(), must be 32
- `message`   TEXT, // make length as long your log messages
- `alreadyRead`      INTEGER // will have 1 or 0 value, can be ENUM or UNSIGNED TINY INT if you want

```php

use MetaRush\DataMapper\Builder as DataMapperBuilder;
use MetaRush\LogOnce\Pdo\Adapter as PdoLogger;

$dataMapper = (new DataMapperBuilder)
    ->setDsn('mysql:host=localhost;dbname=yourLogDb')
    ->setDbUser('user')
    ->setDbPass('pass');
    ->build();

$adapter = new PdoLogger($dataMapper, 'yourLogTable');

```

Note: To mark a log row as read, set the `alreadyRead` column to `1` or you can simply delete the row.

## Notifiers

We use the package `metarush/notifier` as notifier

```php

use MetaRush\Notifier\Pushover\Builder as PushoverNotifier;

// define a Pushover notifier

$pushoverNotifier = (new PushoverNotifier)
                        ->addAccount('pushover_app_key', 'pushover_user_key')
                        ->setSubject('test subject')
                        ->setBody('test body')
                        ->build();

// $emailNotifier = (new EmailNotifier)...; // optionally add other notifiers

$notifiers = [$pushoverNotifier, $emailNotifier];
```

Inject `$notifiers` in `->setNotifiers($notifiers)` to the sample usage above.

For more info on how to use other available notifiers such as email visit [metarush/notifier](https://github.com/metarush/notifier)

## Viewing logs

A UI to view the logs is not included in this package.
You can simply use whatever database admin tool you're using if you're using PDO logger,
or manually view the file system, if you're using the File system logger.