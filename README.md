# Group-Co	

#### * 异步协程调度，应对高并发
#### * SOA服务化调用，支持并行、串行调用。服务端采用AsyncTask进行异步处理后合并数据并返回。
#### * 支持异步日志(待更新),异步Mysql,异步Redis
#### * 支持Mysql,Redis连接池(待更新)
#### * Twig、Doctrine支持视图、服务数据层
#### * 单元测试覆盖

##### 环境依赖
- hiredis 
- redis
- mysql
- php5.6
- swoole >=1.9.14 (在编译swoole时加入--enable-async-redis，开启异步redis客户端)

##### 安装
- 克隆项目
- 执行 => composer install
- 修改配置nginx，见doc/nginx.md,配置hosts
- 配置config中的service,database等配置文件
- 执行脚本 => app/console sql:migrate 
- 启动http server => php server.php
- 启动async服务 => app/service user
- 访问配置的servername，groupco.com

##### 基础服务
- AsyncMysql
- AsyncRedis
- Container
- Event
- Route
- Service
- StaticCache

##### 串行调用

```php

    //串行
    $start = microtime(true);
    //设置2秒超时
    service("user")->setTimeout(2);
    $users = (yield service("user")->call("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]));
    dump($users);

```

##### 并行调用

```php

    //并行
    $start = microtime(true);
    $callId1 = service("user")->addCall("User\User::getUsersCache", ['ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]);
    $callId2 = service("user")->addCall("User\User::getUser", ['id' => 1]);
    $res = (yield service("user")->multiCall());
    dump($res[$callId1]);
    dump($res[$callId2]);
    dump(microtime(true) - $start);
    
```

##### 异步redis

```php
    
    use AsyncRedis;

    //异步redis
    yield AsyncRedis::set('foo', 'bar');
    dump(yield AsyncRedis::get('foo'));
    $user = json_encode(['foo' => 'bar']);
    yield AsyncRedis::hSet('user', 1, $user);
    dump(yield \Group\Cache\AsyncRedis::hGet('user', 1));
    
```

##### 异步mysql

```php
    
    use AsyncMysql;
    
    //异步mysql
    $res = (yield AsyncMysql::query("INSERT INTO `user` (`id`, `mobile`, `password`) VALUES (NULL, '18768122222', '11111')"));
    //失败返回false   
    if ($res) {
        $result = $res->getResult();
        $affectedRows = $res->getAffectedRows();
        $id = $res->getInsertId();
    }
    
```


##### 异常处理

```php

    try {
        throw new \Exception("Error Processing Request", 1); 
        //yield throwException(new \Exception("Error Processing Request", 1));
    } catch (\Exception $e) {
        echo  $e->getMessage();
    }

```
