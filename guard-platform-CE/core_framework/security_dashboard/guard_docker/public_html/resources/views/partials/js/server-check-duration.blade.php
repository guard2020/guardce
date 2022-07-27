<script>
    function serverDuration(duration, events){
        let ctx = $(".tab-pane.active #duration_graph")[0];
        ctx.font = "semibold 20px 'Helvetica Neue'";

        let eventsData = JSON.parse(events);
        let durationData = JSON.parse(duration);

        var myLineChart = new Chart.Bar(ctx, {
            data: {
                 //labels: ['SWAP', 'DISK', 'HTTPD', 'keepalive', 'MYSQL', 'CPU', 'MEMORY'],
                labels: eventsData,
                datasets: [
                    {
                        fill: false,
                        data: durationData,
                        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850", "lightblue", 'lightgreen', 'yellow', 'grey'],
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
                        scaleLabel: {
                            display: false,
                            labelString: "Event",
                            fontColor: "#808080",
                            fontStyle: 'bold',
                            fontSize: 12,
                            fontFamily: 'Roboto'
                        },
                        ticks: {
                            fontSize: 8
                        }
                    }],
                    yAxes: [{
                        ticks: {
                        },
                        scaleLabel: {
                            display: true,
                            labelString: "Duration in Seconds",
                            fontColor: "#808080",
                            fontSize: 13,
                            fontStyle: 'bold',
                            fontFamily: 'Roboto'
                        }
                    }]
                }
            }
        });
    }


</script>