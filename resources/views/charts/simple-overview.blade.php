<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>UK Parliment Petitions Monitor</title>
    </head>
    <body>
        <div>
            <ul>
            @foreach($petitionList as $petitionItem)
                <li><a href="{{ route('overview', ['petitionNumber' => $petitionItem->petition_number]) }}">
                    {{ $petitionItem->getPetitionData()->getAction() }}
                </a></li>
            @endforeach
            </ul>
        </div>

        <hr />

        @if(!empty($chart))
            <div id="app">
                {!! $chart->container() !!}
            </div>
            <script src="https://unpkg.com/vue"></script>
            <script>
                var app = new Vue({
                    el: '#app',
                });
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
            {!! $chart->script() !!}
        @endif

        @if(!empty($petition))
            <table>
                <tr>
                    <th scope="row">Action</td>
                    <td>{{ $petition->getPetitionData()->getAction() }}</td>
                </tr>
                <tr>
                    <th scope="row">Total Votes</td>
                    <td>{{ $petition->getPetitionData()->getCount() }}</td>
                </tr>
                <tr>
                    <th scope="row">State</td>
                    <td>{{ $petition->getPetitionData()->getState() }}</td>
                </tr>
                <tr>
                    <th scope="row">Background</td>
                    <td>{{ $petition->getPetitionData()->getBackground() }}</td>
                </tr>
                <tr>
                    <th scope="row">Additional Details</td>
                    <td>{{ $petition->getPetitionData()->getAdditionalDetails() }}</td>
                </tr>
                <tr>
                    <th scope="row">Monitor Period</td>
                    <td>{{ $petition->getScheduleName() }}</td>
                </tr>
            </table>
            <?php //dump($petition->toArray()); ?>
        @endif
    </body>
</html>
