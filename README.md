# date-snowflake
Add date string before a Short twitter's snowflake ID, e.g. 2021031336140492049070.

    /**
     *  由于雪花算法加上日期有26位长，所以修改缩小到22位。
     * |---------------------------雪花算法 (64bits)---------------------------------|
     * |--补位(1bit)--|---时间戳毫秒(41bit)---|---机器ID(10bit)--|---序号(12bit)--|
     *
     *              |-------------------------修改后(48bits)-----------------------------|
     *    日期 + |----每日当前毫秒(28bit)---|---机器ID(9bit)---|----序号(11bit)----|
     *
     */

# use
## a. General usage
```php
require('../src/DateSnowflake.php');
use Colyll\DateSnowflake;
$dateSnowflake = new DateSnowflake(10);

$id = $dateSnowflake->id();
```
## b. Use it in Laravel project
```
composer require colyll/date-snowflake dev-laravel
```
copy `config/snowflake.php`  into  project_root/config/ directory, 
use `SNOWFLAKE_MACHINE_ID=1` to set the machine ID in `.env`file.

```php
use Colyll\DateSnowflake;
$dateSnowflake = new DateSnowflake();

$id = $dateSnowflake->id();
```
