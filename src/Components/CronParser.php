<?php

namespace WebmanTech\CrontabTask\Components;

use Workerman\Crontab\Parser;

final class CronParser extends Parser
{
    /**
     * 获取下次执行时间
     * @param string $crontabString
     * @param int|null $now
     * @return int|null
     */
    public function getNextDueTime(string $crontabString, int $now = null): ?int
    {
        return $this->getNextDueTimes($crontabString, $now, 1)[0] ?? null;
    }

    /**
     * 获取下次执行的多个时间
     * @param string $crontabString
     * @param int|null $now
     * @param int $limit 限制返回数量
     * @return array|int[]
     */
    public function getNextDueTimes(string $crontabString, int $now = null, int $limit = 5): array
    {
        $startTime = \DateTime::createFromFormat('Y-m-d H:i:0', date('Y-m-d H:i:0', $now ?: time()));
        $nextTimes = [];
        $maxLoopCount = 60 * 24 * 365; // 1年
        while (true) {
            $maxLoopCount--;
            if ($maxLoopCount <= 0) {
                break;
            }
            $startTime = $startTime->add(new \DateInterval('PT1M'));
            $times = $this->parse($crontabString, $startTime->getTimestamp());
            foreach ($times as $time) {
                $nextTimes[] = $time;
                if (count($nextTimes) >= $limit) {
                    break 2;
                }
            }
        }

        return $nextTimes;
    }
}