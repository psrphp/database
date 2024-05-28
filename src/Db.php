<?php

declare(strict_types=1);

namespace PsrPHP\Database;

use Medoo\Medoo;

/**
 * @method false|\PDOStatement query($query, $map = [])
 * @method false|\PDOStatement exec($query, $map = [])
 * @method string|false quote($string)
 * @method false|\PDOStatement create($table, $columns, $options = null)
 * @method false|\PDOStatement drop($table)
 * @method array|false select($table, $join, $columns = null, $where = null)
 * @method false|\PDOStatement insert($table, $datas)
 * @method false|\PDOStatement update($table, $data, $where = null)
 * @method false|\PDOStatement delete($table, $where)
 * @method false|\PDOStatement replace($table, $columns, $where = null)
 * @method false|string|int|array get($table, $join = null, $columns = null, $where = null)
 * @method bool has($table, $join, $where = null)
 * @method array|false rand($table, $join = null, $columns = null, $where = null)
 * @method false|int count($table, $join = null, $column = null, $where = null)
 * @method false|int avg($table, $join, $column = null, $where = null)
 * @method false|int max($table, $join, $column = null, $where = null)
 * @method false|int min($table, $join, $column = null, $where = null)
 * @method false|int sum($table, $join, $column = null, $where = null)
 * @method mixed action($actions)
 * @method null|string|\PDOStatement|false id()
 * @method \Medoo\Medoo debug()
 * @method mixed error()
 * @method string|string[]|null last()
 * @method mixed log()
 * @method array info()
 */
class Db
{
    private $master_config = [];
    private $slaves_config = [];

    public function __construct(array $master_config = [], array ...$slaves_config)
    {
        $this->master_config = $master_config;
        $this->slaves_config = $slaves_config;
    }

    public function master(): Medoo
    {
        static $db;
        if (!$db) {
            $db = $this->getInstance($this->master_config);
        }
        return $db;
    }

    public function slave(): Medoo
    {
        static $db;
        if (!$db) {
            $db = $this->slaves_config ? $this->getInstance(array_rand($this->slaves_config)) : $this->master();
        }
        return $db;
    }

    public function getInstance(array $config = []): Medoo
    {
        return new Medoo(array_merge([
            'database_type' => 'mysql',
            'database_name' => 'test',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8mb4',
            'prefix' => 'prefix_',
            'option' => [
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
            'command' => ['SET SQL_MODE=ANSI_QUOTES'],
        ], $config));
    }

    public function __call($name, $arguments)
    {
        if (in_array($name, ['exec', 'create', 'drop', 'insert', 'update', 'delete', 'replace', 'action'])) {
            return $this->master()->$name(...$arguments);
        } else {
            return $this->slave()->$name(...$arguments);
        }
    }
}
