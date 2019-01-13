<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ config('app.name') }}</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    </head>
    <body>
        <div class="container-fluid">
            <h1>{{ config('app.name') }}</h1>

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

                <script src="https://unpkg.com/vue"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

                <h2>Total Signatures</h2>

                <div id="app">
                    {!! $chart->container() !!}
                </div>
                <script>
                    var app = new Vue({
                        el: '#app',
                    });
                </script>
                {!! $chart->script() !!}

                <h2>Signatures per Hour</h2>

                <div id="app2">
                    {!! $chart2->container() !!}
                </div>
                <script>
                    var app2 = new Vue({
                        el: '#app2',
                    });
                </script>
                {!! $chart2->script() !!}

            @elseif(!empty($petition))
                <p class="alert alert-info">First sample will be gathered shortly.</p>
            @endif

            @if(!empty($petition))
                <table class="table">
                    <tr>
                        <th scope="row">Action</td>
                        <td>{{ $petition->getPetitionData()->getAction() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Total Votes</td>
                        <td>
                            @if($petition->fetchJobs()->count())
                                {{ $petition->fetchJobs()->latest()->first()->count }}
                            @else
                                No results yet
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">State</td>
                        <td>{{ $petition->getPetitionData()->getState() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Background</td>
                        <td>
                            {!! Markdown::convertToHtml($petition->getPetitionData()->getBackground()) !!}
                        </td>
                    </tr>
                    @if($petition->getPetitionData()->getAdditionalDetails())
                    <tr>
                        <th scope="row">Additional Details</td>
                        <td>
                            {!! Markdown::convertToHtml($petition->getPetitionData()->getAdditionalDetails()) !!}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th scope="row">Petition Home Page</td>
                        <td><a href="{{ $petition->getPetitionData()->getHtmlUrl() }}" rel="external">
                            {{ $petition->getPetitionData()->getHtmlUrl() }}
                        </a></td>
                    </tr>
                    <tr>
                        <th scope="row">Source JSON Feed</td>
                        <td><a href="{{ $petition->getPetitionData()->getJsonUrl() }}" rel="external">
                            {{ $petition->getPetitionData()->getJsonUrl() }}
                        </a></td>
                    </tr>
                    <tr>
                        <th scope="row">Monitor Period</td>
                        <td>{{ $petition->getScheduleName() }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Sample Count</td>
                        <td>
                            @if($petition->fetchJobs()->count())
                                {{ $petition->fetchJobs()->count() }}
                                since
                                {{ $petition->fetchJobs()->oldest()->first()->created_at->format('Y-m-d') }}
                            @else
                                No results yet
                            @endif
                        </td>
                    </tr>
                </table>
            @endif

            <a href="https://github.com/academe/parliament-petition-monitor"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png" alt="Fork me on GitHub"></a>
        </div>

        {{--
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>--}}
    </body>
</html>
