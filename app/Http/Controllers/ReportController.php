<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\SimpleOverview;
use App\Petition;

class ReportController extends Controller
{
    public function simpleOverview(int $petitionNumber = null)
    {
        if ($petitionNumber !== null) {
            $petition = Petition::where('petition_number', '=', $petitionNumber)
                ->firstOrFail();

            $petitionData = $petition->getPetitionData();

            // All records for all fetch jobs, not summarised in any way.
            // Based on the total number of records and the date range,
            // we could group by various time ranges to limit the number of
            // records, such as by hour, half day, day, week etc. We would
            // want to average the counts in each of those ranges, or perhaps
            // take the maximum in that range.

            /*$allOverviewCounts = $petition
                ->fetchJobs()
                ->whereNotNull('count')
                ->select(['count_time', 'count'])
                ->orderBy('count_time')
                ->get();*/

            $allOverviewCounts = $petition->getJobFetchRange();

            $chart = new SimpleOverview;

            $chart->labels($allOverviewCounts->pluck(['count_time']));
            $chart->dataset(
                $petitionData->getAction(),
                'line',
                $allOverviewCounts->pluck(['count'])
            );
            $chart->options(['scales' => ['yAxes' => ['ticks' => ['beginAtZero' => false]]]]);
        }

        $petitionList = Petition::get();

        return view('charts.simple-overview', [
            'chart' => $chart ?? null,
            'petitionList' => $petitionList,
            'petition' => $petition ?? null,
        ]);

    }
}
