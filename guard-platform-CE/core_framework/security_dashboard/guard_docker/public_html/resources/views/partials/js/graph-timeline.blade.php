<script type="text/javascript">
    $(function() {

        $.get('{!! route('anomaly.graph.dt')!!}', function( data ) {

            let ctx = document.getElementById('chartTimeline');
            ctx.font = "semibold 20px 'Helvetica Neue'";


            var myLineChart = new Chart.Line(ctx, {
                data: {
                    datasets: [
                        {
                            label: 'Anomalies count',
                            fill: false,
                            data: data,
                            lineTension: 0,
                            borderColor: "#12439B",
                            pointRadius: 1,
                            pointBackgroundColor: "#12439B",
                            pointHoverRadius: 10,
                            pointHitRadius: 10,
                        }
                    ]
                },
                options: {
                    showLines: true,
                    maintainAspectRatio: false,
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false,
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                format: 'HH:mm',
                                unit: 'minute',
                                stepSize: 5,
                                displayFormats: {
                                    'minute': 'HH:mm',
                                    'hour': 'HH:mm'
                                },
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "Time and Date",
                                fontColor: "#808080",
                                fontStyle: 'bold',
                                fontSize: 12,
                                fontFamily: 'Helvetica'
                            },
                            ticks: {
                                callback: function(value, index, values){
                                    return value  + ' (04/03/2020)';
                                }
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                suggestedMax: 850,
                                suggestedMin: 0,
                                stepSize: 100,
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "Anomalies' Count",
                                fontColor: "#808080",
                                fontStyle: 'bold',
                                fontSize: 13,
                                fontFamily: 'Helvetica'
                            }
                        }]
                    }
                }
            });
        });
    });
</script>

