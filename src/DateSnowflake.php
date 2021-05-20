<?php
/**
 * Copyright (c) 2021.
 * Project: DateSnowflake
 * File:       DateSnowflake.php
 * Date:     2021/03/13 16:02:12
 * Author:  ColyLL
 * QQ :      857859975
 */

namespace Colyll;

class DateSnowflake {
    /**
     *  由于雪花算法加上日期有26位长，所以修改缩小到22位。
     * |---------------------------雪花算法 (64bits)---------------------------------|
     * |--补位(1bit)--|---时间戳毫秒(41bit)---|---机器ID(10bit)--|---序号(12bit)--|
     *
     *              |-------------------------修改后(48bits)-----------------------------|
     *    日期 + |----每日当前毫秒(28bit)---|---机器ID(9bit)---|----序号(11bit)----|
     *
     */
    private $machineId;  // 机器ID
    private $machineIdLength = 9; // 机器ID strlen(decbin(511))
    private $id; // 序号
    private $idLength = 11;  // 序号长度  strlen(decbin(2047))
    private $theMillisecond;  // 每日当前毫秒值
    private $nowTime;  // 当前时间戳 毫秒
    private $dateString;  // date('Ymd')  日期
    private $redis; // laravel Redis缓存
    private $idCacheKey; //  Redis缓存 id 值

    public function __construct($machineId = 0) {
        $machineId = empty($machineId) ? $this->config() :$machineId;
        $this->machineId = $machineId;
        $redisConfig = Config('database.redis.cache');
        if (!empty($redisConfig)) {
            $redis = new \Redis();
            $redis->connect($redisConfig['host'],$redisConfig['port']);

            if (isset($redisConfig['password']) && ! empty($redisConfig['password'])) {
                $redis->auth($redisConfig['password']);
            }

            if (empty($redisConfig['database'])) {
                $redisConfig['database'] = 1;
            }
            $redis->select($redisConfig['database']);

            if (!empty($redisConfig['prefix'])) {
                $this->idCacheKey = $redisConfig['prefix'].'Colyll:DateSnowflake:'. $this->machineId;
            }

            $this->redis = $redis;
        }
    }

    private function config() {
        $machineId = Config('snowflake.machineId');

        return $machineId ? $machineId : 0;
    }

    /**
     * 获取当前毫秒值
     * @return int
     */
    private function getTime() {
        return intval(microtime(true) * 1000);
    }

    /**
     * 更新设置
     * @param $time
     */
    private function setUp($time) {
        $this->id = 0;
        $this->nowTime = $time;
        $this->dateString = date('Ymd', $time / 1000);
        $this->theMillisecond = $time % 86400000;
    }

    /**
     * 1毫秒内生成序号超出最大容量时，等待获取可用毫秒值
     * @return int
     */
    private function getCanUseTime() {
        $time = $this->getTime();
        while ($time <= $this->nowTime) {
            $time = $this->getTime();
        }

        return $time;
    }

    /**
     * 生成新ID，例如：2021031336140492049070
     * @return string
     */
    public function id() {
        $time = $this->getTime();
        if ($time !== $this->nowTime) {
            $this->setUp($time);
        }
        $this->getIncrementId();
        $shortSnowflake = $this->theMillisecond << ($this->idLength + $this->machineIdLength) | $this->machineId << $this->idLength | $this->id;

        return $this->dateString . $shortSnowflake;
    }

    private function getIncrementId(){
        if ($this->redis){
            $this->id = $this->redis->incrBy($this->idCacheKey, 1)%512;
        }else{
            $this->id += 1;
            // 当前毫秒内序号超出范围，重新获取可用时间
            if ($this->id > (-1 ^ (-1 << $this->idLength))) {
                $this->setUp($this->getCanUseTime());
            }
        }
    }
}
