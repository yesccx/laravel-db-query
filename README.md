<h1 align="center">Laravel-DB-Query</h1>
<p align="center">在 <strong>Laravel</strong> 中便捷的进行原生 <strong>MYSQL</strong> 语句查询</p>

<p align="center"><a href="https://github.com/yesccx/laravel-db-query"><img alt="For Laravel 5" src="https://img.shields.io/badge/laravel-9.*-green.svg" style="max-width:100%;"></a>
<a href="https://packagist.org/packages/yesccx/laravel-db-query"><img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/yesccx/laravel-db-query.svg" style="max-width:100%;"></a>
<a href="https://packagist.org/packages/yesccx/laravel-db-query"><img alt="Latest Unstable Version" src="https://img.shields.io/packagist/vpre/yesccx/laravel-db-query.svg" style="max-width:100%;"></a>
<a href="https://packagist.org/packages/yesccx/laravel-db-query"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/yesccx/laravel-db-query.svg?maxAge=2592000" style="max-width:100%;"></a>
<a href="https://packagist.org/packages/yesccx/laravel-db-query"><img alt="License" src="https://img.shields.io/packagist/l/yesccx/laravel-db-query.svg?maxAge=2592000" style="max-width:100%;"></a></p>

# 目录
- [目录](#目录)
- [安装](#安装)
  - [运行环境](#运行环境)
  - [通过Composer引入依赖包](#通过composer引入依赖包)
  - [发布配置文件](#发布配置文件)
- [开始使用](#开始使用)
  - [`DBQuery` 查询](#dbquery-查询)
    - [简单查询](#简单查询)
    - [模板查询](#模板查询)
    - [查询缓存](#查询缓存)
    - [更多查询方法](#更多查询方法)
  - [查询服务类](#查询服务类)
- [API](#api)
  - [Queries](#queries)
    - [get(): \\Illuminate\\Support\\Collection](#get-illuminatesupportcollection)
    - [find(mixed $default = null): mixed](#findmixed-default--null-mixed)
    - [first(mixed $default = null): mixed](#firstmixed-default--null-mixed)
    - [value(string $column, mixed $default = null): mixed](#valuestring-column-mixed-default--null-mixed)
    - [pluck(string $column): \\Illuminate\\Support\\Collection](#pluckstring-column-illuminatesupportcollection)
    - [exists(): bool](#exists-bool)
  - [ENV](#env)
    - [YDQ\_CACHE\_ENABLED](#ydq_cache_enabled)
    - [YDQ\_CACHE\_DRIVER](#ydq_cache_driver)
- [使用建议](#使用建议)
- [License](#license)

# 安装

## 运行环境

| 运行环境要求           |
| ---------------------- |
| PHP ^8.1.0             |
| Laravel Framework ^9.0 |

## 通过Composer引入依赖包

通过终端进入项目根目录，执行以下命令引入依赖包：

``` shell
> composer require yesccx/laravel-db-query:1.x
```

## 发布配置文件

如果需要额外配置，**可选择** 发布配置文件

``` shell
> php artisan vendor:publish --tag=db-query-config
```

# 开始使用

## `DBQuery` 查询

### 简单查询

将连接配置名传递给 `connection` 方法

> 连接配置通常来自 `database.php` 配置文件中的 `connections`

``` php
use Yesccx\DBQuery\DBQuery;

$data = DBQuery::connection('mysql')
    ->statement('select * from users where id = 1')
    ->get();
```

还可以直接传入连接配置（ *目前仅支持 `MYSQL` 驱动* ）

``` php
use Yesccx\DBQuery\DBQuery;

$data = DBQuery::connection([
        'driver' => 'mysql',
        'host' => '127.0.0.1'
        'username' => 'example',
        'password' => 'example',
        // ...
    ])
    ->statement('select * from users where id = 1')
    ->get();
```

### 模板查询

在语句中定义占位符后，可以使用链式方法如 `where`、`select`、`group by`、`order by` 等进行查询

``` php
use Yesccx\DBQuery\DBQuery;

$data = DBQuery::connection('mysql')
    ->statement('select * from users where @WHERE@')
    ->where('id', 1)
    ->get();


$data = DBQuery::connection('mysql')
    ->statement('select @COLUMNS@ from users where (@WHERE@) and deleted_at is null')
    ->select('id', 'name')
    ->where('id', 1)
    ->get();
```

目前支持的占位符：

| 类型        | 示例                                          |
| ----------- | --------------------------------------------- |
| `@WHERE@`   | `select * from a where id > 1 and  (@WHERE@)` |
| `@COLUMNS@` | `select @COLUMNS@ from a`                     |
| `@GROUPBY@` | `select * from a group by @GROUPBY@`          |
| `@HAVING@`  | `select * from a group by id having @HAVING@` |
| `@ORDERBY@` | `select * from a order by @ORDERBY@`          |
| `@LIMIT@`   | `select * from a limit @LIMIT@`               |
| `@OFFSET@`  | `select * from a offset @OFFSET@`             |


### 查询缓存

默认情况下关闭查询缓存，可以通过配置 `db-query.cache.enabled` 进行开启，开启后传递给 `cache` 方法一个有效时间（单位秒）对查询结果进行缓存

> 通常情况下可以直接配置 `env` 中的 `YDQ_CACHE_ENABLED` 进行开启

``` php
use Yesccx\DBQuery\DBQuery;

$data = DBQuery::connection('mysql')
    ->statement('select * from users where @WHERE@')
    ->where('id', 1)
    // 将查询结果缓存60秒
    ->cache(60)
    ->get();
```

> 如果需要指定缓存驱动、前缀等，可以通过配置 `db-query.cache.driver`

### 更多查询方法

查询允许链式的使用 `Laravel` `Illuminate\Database\Query\Builder` 类中支持的方法，此外可以通过其它方法获取查询结果，参考 [API章节](#api)

> 注意：不能使用 `Illuminate\Database\Query\Builder` 中的 `get`、`first`、`find`、`paginate`、`count` 等方法

## 查询服务类

继承 `DBQueryService`，实现 `connection` 方法后在类中使用 `statement` 方法进行查询。这种方式能方便的组织和管理原生的查询语句

``` php
<?php

namespace App\Services;

use Yesccx\DBQuery\Supports\DBQueryService;

class PatientQueryService extends DBQueryService
{
    /**
     * Define connection config
     *
     * @return string|array connection name or connection config
     */
    protected function connection(): string|array
    {
        return 'mysql';
    }

    public function getList(): array
    {
        return $this->statement('select * from users where type = 1')
            ->get()
            ->toArray();
    }

    public function getUserInfo(int $id): mixed
    {
        return $this->statement('select * from users where @WHERE@')
            ->where('id', $id)
            ->cache(60)
            ->first();
    }

    public function getUserCount(): int
    {
        return $this->statement('select count(*) as count from users')
            ->cache(60)
            ->value('count', 0);
    }
}
```

# API

## Queries
### get(): \Illuminate\Support\Collection

获取列表，结果是一个二维数组集合类

### find(mixed $default = null): mixed

获取单条数据，结果是一个一维数组，类似 `Illuminate\Database\Eloquent\Builder` 中的 `find` 方法

### first(mixed $default = null): mixed

`find` 方法的别名

### value(string $column, mixed $default = null): mixed

获取第一行某一列的值

### pluck(string $column): \Illuminate\Support\Collection

获取某一列

### exists(): bool

判断是否存在数据

## ENV

### YDQ_CACHE_ENABLED

是否开启查询缓存，默认关闭

### YDQ_CACHE_DRIVER

缓存驱动，默认与 `cache.default` 保持一致

# 使用建议

- 建议将定义 `查询服务类` 统一存放在 `app/QueryServices` 目录下

# License

MIT