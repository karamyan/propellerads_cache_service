<?php

declare(strict_types=1);

namespace App\Modules;


use DateTime;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use RedisException;
use Exception;


class StatByDepartmentModule
{
    /**
     * @param array $body
     * @param $redis
     * @return array
     * @throws Exception
     */
    public static function getData(array $body, $redis): array
    {
        $dateFrom      = new DateTime($body['date_time_from']);
        $dateTo        = new DateTime($body['date_time_to']);
        $epochDateFrom = $dateFrom->getTimestamp();
        $epochDateTo   = $dateTo->getTimestamp();

        $key = sprintf('%s|%s_%s', implode('_', $body['datamarts']), $epochDateFrom, $epochDateTo);

        $item = $redis->getItem($key);

        $item->expiresAfter(86400);

        if (!$item->isHit()) {
            // Fetch data from data  access service.
            $data = [
                'result' => [],
                'time' => time()
            ];
            $item->set($data);
            $redis->save($item);

            $response = [
                'source' => 'DWH',
                "timestamp" => time()
            ];
        } else {
            // Retrieve data from cache
            $data = $item->get();

            $response = [
                'source' => 'CACHE',
                "timestamp" => $data['time']
            ];
        }

        return $response;
    }

    /**
     * @param array $body
     * @return void
     * @throws RedisException
     */
    public static function calculate(array $body): void
    {
        $datetimeEpoch = (new DateTime($body['date_time']))->getTimestamp();

        // TODO Create service to inject predis or another cache db as memcache.
        // TODO This is not the best practices in the symfony but default CacheInterface in symfony does not have search by redis key.
        $redis = RedisAdapter::createConnection($_ENV['REDIS_HOST']);

        $keys = $redis->keys("*{$body['datamarts']}*");
        foreach ($keys as $key) {
            $params = explode('|', $key);
            $timestampRange = $params[1];

            $times = explode('_', $timestampRange);

            if ($datetimeEpoch >= $times[0] && $datetimeEpoch <= $times[1]) {
                $redis->del($key);
            }
        }
    }
}
