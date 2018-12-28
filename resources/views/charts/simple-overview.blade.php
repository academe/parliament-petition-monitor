<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Chart with VueJS</title>
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
    </body>
</html>
