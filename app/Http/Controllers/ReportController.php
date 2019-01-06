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

            $allOverviewCounts = $petition->getJobFetchRange();

            if ($allOverviewCounts !== null) {
                // Construct the chart.
                // Couldn't be easier. Package here:
                // https://github.com/ConsoleTVs/Charts

                $chart = new SimpleOverview;

                // TODO: format the time without seconds.

                $chart->labels(
                    $allOverviewCounts->pluck(['count_time_five_minute'])
                );

                $chart->dataset(
                    $petitionData->getAction(),
                    'line',
                    $allOverviewCounts->pluck(['count'])
                );

                $chart->options([
                    'scales' => [
                        'yAxes' => [
                            'ticks' => ['beginAtZero' => false],
                        ]
                    ]
                ]);
            }
        }

        $petitionList = Petition::get();

        return view('charts.simple-overview', [
            'chart' => $chart ?? null,
            'petitionList' => $petitionList,
            'petition' => $petition ?? null,
        ]);
    }
}
