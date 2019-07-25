📃 开源协议
Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html

使用说明
--------------------------------------------------------------------------

```php

<?php
        \v10086\Qredis::conn('192.168.139.128',6379);
        $resp = \v10086\Qredis::exec('ping');
        echo $resp //将输出PONG
        $resp = \v10086\Qredis::exec('set abc "666"');
        echo $resp //将输出OK
        $resp = \v10086\Qredis::exec('get abc');
        echo $resp //将输出"666"


```


