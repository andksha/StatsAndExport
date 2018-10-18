<div class='container charts text-center'>
    @if (empty($tables['personal']) == false)
        <div class='chart'>
            <h4 class='caption'>
                {{ trans('stats.captions.personal') }}
            </h4>
            <canvas id="personal_pie"
                class="chartjs-render-monitor" style="display:block; width:517px; height:258px;">
            </canvas>
        </div>
    @endif
    <div class='chart'>
        <h4 class='caption'>
            {{ trans('stats.captions.public') }}
        </h4>
        <canvas id="public_pie"
            class="chartjs-render-monitor" style="display:block; width:517px; height:258px;">
        </canvas>
    </div>
</div>
<div class='container stats'>
    <hr class='horizontal_line grey_hr'/>
    <div class='stat_main'>
    <div class='row vertical_line'>
        @foreach ($tables as $key => $table)
            @if (empty($table) == false)
                @if (count($tables) == 2 || count($tables) == 3)
                    <div class='col-md-4 stats_table'>
                @elseif (count($tables) == 1)
                    <div class='col-md-4 col-md-offset-4 stats_table'>
                @endif
                    <div class='row  text-center'>
                        <h4 class='caption'>{{ trans('stats.captions.' . $key) }}</h4>
                    </div>
                    <div class='row'>
                        <div class='col-md-6 caption'>
                            {{ trans('stats.captions.status') }}
                        </div>
                        <div class='col-md-6 caption'>
                            {{ trans('stats.captions.amount') }}
                        </div>
                    </div>

                        @foreach ($table as $key => $stats)
                            <div class='row'>
                                <div class='col-md-6 status'>
                                    {{ link_to('/terms?' . $stats['link'], trans('stats.' . $key),
                                            ['title' => 'Наявні терміни по даному критерію']) }}
                                </div>
                                <div class='col-md-6 text-center'>
                                    @if ($stats['stat'] == null)
                                        0
                                    @else
                                        {{ $stats['stat'] }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                </div>
            @endif
        @endforeach
        </div>
    </div>
</div>
    
{{ Html::script('js/Chart.bundle.min.js') }}

<script>
    var personal_config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    {{ $tables['personal']->draft['stat'] }},
                    {{ $tables['personal']->archive['stat'] }},
                    {{ $tables['personal']->active['stat'] }},
                    {{ $tables['personal']->ready['stat'] }},
                    {{ $tables['personal']->duplicate['stat'] }},
                    {{ $tables['personal']->hot['stat'] }},
                    {{ $tables['personal']->published['stat'] }}
                ],
                backgroundColor: [
                    '#b3b3b8',
                    '#f08a0b',
                    '#eef00b',
                    '#41caff',
                    '#1930dd',
                    '#ff2121',
                    '#3eba3b'
                ]
            }],
            labels: [
                '{{ trans('stats.draft') . " - " . $tables['personal']->draft['stat'] }}',
                '{{ trans('stats.archive') . " - " . $tables['personal']->archive['stat'] }}',
                '{{ trans('stats.active') . " - " . $tables['personal']->active['stat'] }}',
                '{{ trans('stats.ready') . " - " . $tables['personal']->ready['stat'] }}',
                '{{ trans('stats.duplicate') . " - " . $tables['personal']->duplicate['stat'] }}',
                '{{ trans('stats.hot') . " - " . $tables['personal']->hot['stat'] }}',
                '{{ trans('stats.published') . " - " . $tables['personal']->published['stat'] }}'
            ]

        },
        options: {
            /*title: {
                display: true,
                text: '{{ trans('stats.captions.personal') }}'
            },*/
            legend:{
              position: 'left'
            },
            responsive: true
        }
    };

    var public_config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    {{ $tables['public']->active['stat'] }},
                    {{ $tables['public']->ready['stat'] }},
                    {{ $tables['public']->duplicate['stat'] }},
                    {{ $tables['public']->hot['stat'] }},
                    {{ $tables['public']->published['stat'] }}
                ],
                backgroundColor: [
                    '#eef00b',
                    '#41caff',
                    '#1930dd',
                    '#ff2121',
                    '#3eba3b'
                ]
            }],
            labels: [
                '{{ trans('stats.active') . " - " . $tables['public']->active['stat'] }}',
                '{{ trans('stats.ready') . " - " . $tables['public']->ready['stat'] }}',
                '{{ trans('stats.duplicate') . " - " . $tables['public']->duplicate['stat'] }}',
                '{{ trans('stats.hot') . " - " . $tables['public']->hot['stat'] }}',
                '{{ trans('stats.published') . " - " . $tables['public']->published['stat'] }}',
            ]

        },
        options: {
            legend:{
                position: 'left'
            },
            responsive: true
        }
    };

    window.onload = function() {
        if (document.getElementById('personal_pie')) {
            var ctx1 = document.getElementById('personal_pie').getContext('2d');
            window.personalPie = new Chart(ctx1, personal_config);
        }
        var ctx2 = document.getElementById('public_pie').getContext('2d');
        window.personalPie = new Chart(ctx2, public_config);
    };
</script>