# database

读写分离的数据库操作类

## 安装

``` bash
composer require psrphp/database
```

## 用例

``` php
$config = [
    'master'=>[...],
    'slave'=>[[...],[...],...]
];

$db = new \PsrPHP\Database\Db($config);
$db->master()->insert delete ...;
$db->slave()->get select ...;
$db->get(...);
$db->select(...);
$db->delete(...);
// 更多方法请参考 (Medoo)[https://github.com/catfan/Medoo]
```
