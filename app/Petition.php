<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;
use App\Constituency;
use Carbon\Carbon;
use DB;

class Petition extends Model
{
    /**
     * Schedule frequency.
     */
    const SCHEDULE_NONE = 'none';
    const SCHEDULE_DAY = 'day';
    const SCHEDULE_HOUR = 'hour';
    const SCHEDULE_HALF_HOUR = 'half-hour';
    const SCHEDULE_QUARTER_HOUR = 'quarter-hour';
    const SCHEDULE_TEN_MINUTES = 'ten-minutes';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'json',
    ];

    protected $attributes = [
        'metadata' => '{}',
    ];

    public function fetchJobs()
    {
        return $this->hasMany(FetchJob::class);
    }

    public function latestFetchJobs()
    {
        return $this->hasMany(FetchJob::class)->latest();
    }

    /**
     * @returns PetitionData object from the stored metadata
     */
    public function getPetitionData()
    {
        return new PetitionData(
            $this->petition_number,
            $this->metadata
        );
    }

    /**
     * Get the min and max date range of fetch jobs with an
     * overall count.
     */
    public function getJobFetchMinTime()
    {
        $minTime = $this->fetchJobs()
            ->whereNotNull('count')
            ->min('count_time');

        return $minTime ? Carbon::parse($minTime) : null;
    }

    /**
     * Get the min and max date range of fetch jobs with an
     * overall count.
     */
    public function getJobFetchMaxTime()
    {
        $maxTime = $this->fetchJobs()
            ->whereNotNull('count')
            ->max('count_time');

        return $maxTime ? Carbon::parse($maxTime) : null;
    }

    /**
     * Get the count of fetch jobs between an optional date range.
     */
    public function getJobFetchCount(Carbon $fromTime = null, Carbon $toTime = null)
    {
        $query = $this->fetchJobs()
            ->whereNotNull('count');

        if ($fromTime) {
            $query->where('count_time', '>=', $fromTime);
        }

        if ($toTime) {
            $query->where('count_time', '<=', $toTime);
        }

        return $query->count('count_time');
    }

    public function getJobFetchRange(
        Carbon $fromTime = null,
        Carbon $toTime = null,
        int $maxPoints = 1000
    ) {
        if ($fromTime == null) {
            $fromTime = $this->getJobFetchMinTime();
        }

        if ($toTime == null) {
            $toTime = $this->getJobFetchMaxTime();
        }

        $count = $this->getJobFetchCount();

        if ($count === 0) {
            // No results have been captureed yet, or there are
            // none for the sepcified data range.

            return;
        }

        if ($count > $maxPoints) {
            // Too many points, so we need to group by date ranges
            // to summarise.

            // FIXME: format the date to an appropriate range to get the
            // count number into a sensible range. This just limits to hourly
            // at this time.

            // NOTE: this will only work for MySQL. Performing the grouping
            // *after* fetching from the database would work for other
            // database engines and allow for other groupings, e.g. into two
            // hour groups, but will need more memory.

            switch ($this->schedule) {
                case static::SCHEDULE_HOUR:
                    $samplesPerHour = 1;
                    break;
                case static::SCHEDULE_HALF_HOUR:
                    $samplesPerHour = 2;
                    break;
                case static::SCHEDULE_QUARTER_HOUR:
                    $samplesPerHour = 4;
                    break;
                case static::SCHEDULE_TEN_MINUTES:
                    $samplesPerHour = 6;
                    break;
                case static::SCHEDULE_NONE:
                case static::SCHEDULE_DAY:
                default:
                    $samplesPerHour = 1/24;
                    break;
            }

            if ($count / $samplesPerHour <= $maxPoints) {
                // Group by hour.
                $groupPattern = '%Y-%m-%d %H:00:00';
            } else {
                // Group by day.
                $groupPattern = '%Y-%m-%d 00:00:00';
            }

            return $this
                ->fetchJobs()
                ->whereNotNull('count')
                ->where('count_time', '>=', $fromTime)
                ->where('count_time', '<=', $toTime)
                ->select([
                    DB::raw('date_format(count_time, "'.$groupPattern.'") as count_time_group'),
                    DB::raw('max(count) as count'),
                ])
                ->groupBy('count_time_group')
                ->orderBy('count_time_group')
                ->get()
                ->each(function ($item) {
                    $item->count_time = $item->count_time_group;
                });
        }

        return $this
            ->fetchJobs()
            ->whereNotNull('count')
            ->where('count_time', '>=', $fromTime)
            ->where('count_time', '<=', $toTime)
            ->select(['count_time', 'count'])
            ->orderBy('count_time')
            ->get();
    }

    public function getScheduleName()
    {
        return ucwords(str_replace('-', ' ', $this->schedule));
    }
}
