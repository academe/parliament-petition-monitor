<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\SimpleOverview;
use App\Petition;
use Carbon\Carbon;
use Cache;

class ReportController extends Controller
{
    public function simpleOverview($petitionNumber = null)
    {
        if ($petitionNumber !== null) {
            $petition = Petition::where('petition_number', '=', $petitionNumber)
                ->firstOrFail();

            $petitionData = $petition->getPetitionData();

            $chartData = $petition->getChartData();

            if ($chartData !== null) {
                // Construct the chart.
                // Couldn't be easier. Package here:
                // https://github.com/ConsoleTVs/Charts

                $chart1 = new SimpleOverview;
                $chart1data = $chartData->get('chart1');

                // The time is formatted without seconds and rounded to
                // the nearest five minutes, just to make it easier to read.

                $chart1->labels($chart1data->get('labels'));

                // Total counts dataset.

                $chart1->dataset(
                    $chart1data->get('action'),
                    $chart1data->get('type'),
                    $chart1data->get('dataset')
                )->options([
                    'color' => ['#66aa33'],
                    'backgroundColor' => 'rgba(255, 180, 20, 0.2)',
                ]);

                // Constituency counts dataset.

                if ($chart1data->get('dataset2')) {
                    $chart1->dataset(
                        'UK Constituency Signature Count',
                        'line',
                        $chart1data->get('dataset2')
                    )->options([
                        'color' => ['#6633ff'],
                        'backgroundColor' => 'rgba(255, 20, 132, 0.2)',
                    ]);
                }

                $chart1->options([
                    'scales' => [
                        'yAxes' => [
                            'ticks' => ['beginAtZero' => false],
                        ]
                    ],
                    //'color' => ['#66aa66'],
                    //'backgroundColor' => ['#bbffbb'],
                ]);

                $chart2 = new SimpleOverview;
                $chart2data = $chartData->get('chart2');

                $chart2->labels($chart2data->get('labels'));

                $chart2->dataset(
                    $chart2data->get('action'),
                    $chart2data->get('type'),
                    $chart2data->get('dataset')
                )->options([
                    'color' => '#aa6666',
                    'backgroundColor' => '#ffbbbb',
                ]);
            }
        } else {
            $petition = null;
        }

        if (!empty($petition) && $petition->fetchJobs()->count()) {
            $totalCount = $petition->fetchJobs()->latest()->first()->count;
            $constituencyCount = $petition
                ->fetchJobs()
                ->latest()
                ->first()
                ->constituencySignatures()
                ->sum('count');
        } else {
            $totalCount = 0;
            $constituencyCount = 0;
        }

        $petitionList = Petition::get();

        return view('charts.simple-overview', [
            'chart1' => $chart1 ?? null,
            'chart2' => $chart2 ?? null,
            'petitionList' => $petitionList,
            'petition' => $petition,
            'petitionData' => $petition ? $petition->getPetitionData() : null,
            'totalCount' => $totalCount,
            'constituencyCount' => $constituencyCount,
        ]);
    }
}
