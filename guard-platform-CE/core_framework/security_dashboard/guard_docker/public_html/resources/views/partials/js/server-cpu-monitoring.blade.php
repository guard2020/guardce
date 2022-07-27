<script type="text/javascript">

    function stackedCpuUsage(cpu){


        loadChart(function(){
            drawColumnStacked(cpu);
        });


        function loadChart(myCallback){
            google.charts.load('current', {packages: ['corechart', 'bar'], callback: myCallback});
        }

        // Chart settings
        function drawColumnStacked(cpu) {

            let total = cpu.reduce((a,b) => a +b,0);

            //user, nice, system, iowait, irq, softirq, steal, guest, guest_nice
            let user = cpu[0]*100/total;
            let nice = cpu[1]*100/total;
            let system = cpu[2]*100/total;
            let iowait = cpu[3]*100/total;
            let irq = cpu[4]*100/total;
            let softirq = cpu[5]*100/total;
            let steal = cpu[6]*100/total;
            let guest = cpu[7]*100/total;
            let guest_nice = cpu[8]*100/total;

            // Define charts element
            var column_stacked_element = $(".tab-pane.active #cpu_usage_graph")[0];

            // Data
            var data = google.visualization.arrayToDataTable([
                ['', 'User', 'Nice', 'System', 'Iowait', 'Irq', 'Softirq', 'Steal', 'Guest', 'Guest_nice'],
                ['CPU Components', user, nice, system, iowait, irq, softirq, steal, guest, guest_nice]
            ]);


            // Options
            var options_column_stacked = {
                fontName: 'Roboto',
                height: 400,
                fontSize: 12,
                isStacked: true,
                tooltip: {
                    textStyle: {
                        fontName: 'Roboto',
                        fontSize: 13
                    }
                },
                vAxis: {
                    viewWindowMode:'explicit',
                    viewWindow: {
                        max:100,
                        min:0
                    },
                    title: 'CPU Usage (%)',
                    titleTextStyle: {
                        fontSize: 13,
                        bold: true,
                        color: '#808080'
                    },
                    textStyle: {
                        color: '#808080'
                    },
                    baselineColor: '#ccc',
                    gridlines: {
                        color: '#eee',
                        count: 5
                    },
                },
                hAxis: {
                    textStyle: {
                        color: '#808080'
                    },
                    label: 'hide'
                },
                chartArea: {
                    top:50,
                    bottom:0,
                    right:0,
                    left:0,
                    'width': '100%' },
                series: {
                    0: {color: '#2ec7c9'},
                    1: {color: '#b6a2de'},
                    2: {color: '#5ab1ef'},
                    3: {color: '#ffb980'},
                    4: {color: '#d87a80'},
                    5: {color: '#8d98b3'},
                    6: {color: '#B3AF84'},
                    7: {color: '#B066B3'},
                    8: {color: '#39B32E'},
                }
            };

            // Draw chart
            var column_stacked = new google.charts.Bar(column_stacked_element);
            column_stacked.draw(data, google.charts.Bar.convertOptions(options_column_stacked));
        }

    }





    // Progress arc - multiple colors
    function progressArc (element, size, total){


        if (typeof d3 == 'undefined') {
            console.warn('Warning - d3.min.js is not loaded.');
            return;
        }

        // Initialize chart only if element exsists in the DOM
        if(element) {

            // Main variables
            var d3Container = d3.select(element),
                radius = size,
                thickness = 20,
                startColor = '#66BB6A',
                midColor = '#FFA726',
                endColor = '#EF5350';

            // Colors
            var color = d3.scale.linear()
                .domain([0, 70, 100])
                .range([startColor, midColor, endColor]);


            // Create chart
            // ------------------------------

            // Add svg element
            var container = d3Container.append("svg");

            // Add SVG group
            var svg = container
                .attr('width', radius * 2)
                .attr('height', radius + 20);


            // Construct chart layout
            // ------------------------------

            // Pie
            var arc = d3.svg.arc()
                .innerRadius(radius - thickness)
                .outerRadius(radius)
                .startAngle(-Math.PI / 2);


            // Append chart elements
            // ------------------------------

            //
            // Group arc elements
            //

            // Group
            var chart = svg.append('g')
                .attr('transform', 'translate(' + radius + ',' + radius + ')');

            // Background
            var background = chart.append('path')
                .datum({
                    endAngle: Math.PI / 2
                })
                .attr({
                    'd': arc,
                    'class': 'd3-state-empty'
                });

            // Foreground
            var foreground = chart.append('path')
                .datum({
                    endAngle: -Math.PI / 2
                })
                .style('fill', startColor)
                .attr('d', arc);

            // Counter value
            var value = svg.append('g')
                .attr('transform', 'translate(' + radius + ',' + (radius * 0.9) + ')')
                .append('text')
                .text(0 + '%')
                .attr({
                    'class': 'd3-text',
                    'text-anchor': 'middle'
                })
                .style({
                    'font-size': 19,
                    'font-weight': 400
                });


            //
            // Min and max text
            //

            // Group
            var scale = svg.append('g')
                .attr('transform', 'translate(' + radius + ',' + (radius + 15) + ')')
                .attr('class', 'd3-text opacity-75')
                .style({
                    'font-size': 12
                });

            // Max
            scale.append('text')
                .text(100)
                .attr({
                    'text-anchor': 'middle',
                    'x': (radius - thickness / 2)
                });

            // Min
            scale.append('text')
                .text(0)
                .attr({
                    'text-anchor': 'middle',
                    'x': -(radius - thickness / 2)
                });

            //
            // Animation
            //

            // Interval
            setInterval(function() {

                update(total);
            }, 1, total);

            // Update
            function update(v) {
                v =d3.format("")(v);
                foreground.transition()
                    .duration(0)
                    .style('fill', function() {
                        return color(v);
                    })
                    .call(arcTween, v);

                value.transition()
                    .duration(0)
                    .call(textTween, v);
            }

            // Arc
            function arcTween(transition, v) {
                var newAngle = v / 100 * Math.PI - Math.PI / 2;
                transition.attrTween('d', function(d) {
                    var interpolate = d3.interpolate(d.endAngle, newAngle);
                    return function(t) {
                        d.endAngle = interpolate(t);
                        return arc(d);
                    };
                });
            }

            // Text
            function textTween(transition, v) {
                transition.tween('text', function() {
                    var interpolate = d3.interpolate(this.innerHTML, v),
                        round = (v.length > 1) ? Math.pow(10, v[1].length) : 1;
                    return function(t) {
                        this.innerHTML = d3.format("")(Math.round(interpolate(t) * round) / round) + '<tspan>%</tspan>';
                    };
                });
            }
        }
    };

</script>