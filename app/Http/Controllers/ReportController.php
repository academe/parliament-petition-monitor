<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\SimpleOverview;
use App\Petition;
use Carbon\Carbon;

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

                // The time is formatted without seconds and rounded to
                // the nearest five minutes.

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

                // Now calculate the derivative.

                $previous = null;
                $derivative = [];

                $allOverviewCounts->each(function ($item) use (& $previous, & $derivative) {
                    $fiveMinutes = Carbon::parse($item->count_time)->roundMinute(5);

                    if ($previous === null) {
                        $previous = $item;
                        $previous->time = $fiveMinutes;
                        return;
                    }

                    $hours = ($fiveMinutes->diffInMinutes($previous->time)) / 60;
                    if ($hours == 0) {
                        // Sometimes two times will be the same.
                        // Skip that iteration to avoid a divide by zero.
                        return;
                    }
                    $signatures = $item->count - $previous->count;
                    $signaturesPerHour = round($signatures / $hours, 2);

                    $derivative[$fiveMinutes->format('Y-m-d H:i')] = $signaturesPerHour;

                    $previous = $item;
                    $previous->time = $fiveMinutes;
                });

                $chart2 = new SimpleOverview;

                $chart2->labels(array_keys($derivative));

                $chart2->dataset(
                    $petitionData->getAction(),
                    'line',
                    array_values($derivative)
                );
            }
        }

        $petitionList = Petition::get();

        return view('charts.simple-overview', [
            'chart' => $chart ?? null,
            'chart2' => $chart2 ?? null,
            'petitionList' => $petitionList,
            'petition' => $petition ?? null,
        ]);
    }
}
