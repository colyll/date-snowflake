# date-snowflake
Add date string before a Short twitter's snowflake ID

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