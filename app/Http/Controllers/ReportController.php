<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\SimpleOverview;
use App\Petition;
use Carbon\Carbon;
use Cache;

class ReportController extends Controller
{
    public function simpleOverview(int $petitionNumber = null)
    {
        if ($petitionNumber !== null) {
            $petition = Petition::where('petition_number', '=', $petitionNumber)
                ->firstOrFail();

            $petitionData = $petition->getPetitionData();

            $allOverviewCounts = $petition->getJobFetchRange();

            $chartData = $petition->getChartData();

            if ($allOverviewCounts !== null) {
                // Construct the chart.
                // Couldn't be easier. Package here:
                // https://github.com/ConsoleTVs/Charts

                $chart1 = new SimpleOverview;
                $chart1data = $chartData->get('chart1');

                // The time is formatted without seconds and rounded to
                // the nearest five minutes.

                $chart1->labels($chart1data->get('labels'));

                $chart1->dataset(
                    $chart1data->get('action'),
                    $chart1data->get('type'),
                    $chart1data->get('dataset')
                );

                $chart1->options([
                    'scales' => [
                        'yAxes' => [
                            'ticks' => ['beginAtZero' => false],
                        ]
                    ],
                    'color' => ['#66aa66'],
                    'backgroundColor' => ['#bbffbb'],
                ]);

                $chart2 = new SimpleOverview;
                $chart2data = $chartData->get('chart2');

                $chart2->labels($chart2data->get('labels'));

                $chart2->dataset(
                    $chart2data->get('action'),
                    $chart2data->get('type'),
                    $chart2data->get('dataset')
                )->options([
                    'color' => ['#aa6666'],
                    'backgroundColor' => ['#ffbbbb'],
                ]);

            }
        }

        $petitionList = Petition::get();

        return view('charts.simple-overview', [
            'chart1' => $chart1 ?? null,
            'chart2' => $chart2 ?? null,
            'petitionList' => $petitionList,
            'petition' => $petition ?? null,
        ]);
    }
}
