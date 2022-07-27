<script>

    function drawMemoryGraph(memory){

        // Define element
        var gauge_memory_usage_element = $(".tab-pane.active #gauge_memory_usage")[0];

        // Initialize chart
        var gauge_memory_usage = echarts.init(gauge_memory_usage_element);

        var gauge_memory_usage_options = {

            // Global text styles
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },

            // Add series
            series: [
                {
                    name: 'Memory usage',
                    type: 'gauge',
                    center: ['50%', '57.5%'],
                    radius: '80%',
                    startAngle: 150,
                    endAngle: -150,
                    axisLine: {
                        lineStyle: {
                            color: [[0.14, 'lightblue'], [0.79, '#3CBA9F'], [0.89, 'orange'], [1, '#ff4500']],
                            width: 30
                        }
                    },
                    axisTick: {
                        splitNumber: 5,
                        length: 5,
                        lineStyle: {
                            color: '#fff'
                        }
                    },
                    axisLabel: {
                        formatter: function(v) {
                            switch (v+''){
                                case '10': return 'Idle';
                                case '50': return 'Normal';
                                case '80': return 'Warning';
                                case '100': return 'Critical';
                                default: return '';
                            }
                        },
                        fontSize: 14
                    },
                    splitLine: {
                        length: 35,
                        lineStyle: {
                            color: '#fff'
                        }
                    },
                    pointer: {
                        width: 5
                    },
                    title: {
                        offsetCenter: ['-75%', -20],
                        textStyle: {
                            fontSize: 16
                        }
                    },
                    detail: {
                        offsetCenter: ['-80%', 10],
                        formatter: '{value}%',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 500
                        }
                    },
                    data: [{value: memory, name: 'Memory usage'}]
                }
            ]
        };

        gauge_memory_usage.setOption(gauge_memory_usage_options);
    }


    function drawDiskGraph(disk){

        if (typeof echarts == 'undefined') {
            console.warn('Warning - echarts.min.js is not loaded.');
            return;
        }


        let titleOptions ="";

        if(disk === "SWAP not configured"){
            disk = Math.floor(Math.random() * 50) + 10;
            titleOptions = {
                offsetCenter: ['-81%', -20],
                textStyle: {
                    fontSize: 16
                }
            };
        }else{
            titleOptions = {
                offsetCenter: ['-81%', -20],
                textStyle: {
                    fontSize: 16
                }
            };
        }

        var gauge_disk_usage_element = $(".tab-pane.active #gauge_disk_usage")[0];

        var gauge_disk_usage = echarts.init(gauge_disk_usage_element);

        var gauge_disk_usage_options = {
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },
            tooltip: {
                trigger: 'item',
                backgroundColor: 'rgba(0,0,0,0.75)',
                padding: [10, 15],
                textStyle: {
                    fontSize: 8,
                    fontFamily: 'Roboto, sans-serif'
                },
                formatter: '{a} <br/>{b} : {c}%'
            },
            series: [
                {
                    name: 'SWAP Usage',
                    type: 'gauge',
                    center: ['50%', '57.5%'],
                    radius: '80%',
                    startAngle: 150,
                    endAngle: -150,
                    axisLine: {
                        lineStyle: {
                            color: [[0.2, 'lightblue'], [0.85, '#3CBA9F'], [0.95, 'orange'], [1, '#ff4500']],
                            width: 30
                        }
                    },
                    axisTick: {
                        splitNumber: 5,
                        length: 5,
                        lineStyle: {
                            color: '#fff'
                        }
                    },
                    axisLabel: {
                        formatter: function (v) {
                            switch (v + '') {
                                case '10':
                                    return 'Idle';
                                case '50':
                                    return 'Normal';
                                case '90':
                                    return 'Warning';
                                case '100':
                                    return 'Critical';
                                default:
                                    return '';
                            }
                        },
                        fontSize: 14
                    },
                    splitLine: {
                        length: 35,
                        lineStyle: {
                            color: '#fff'
                        }
                    },
                    pointer: {
                        width: 5
                    },
                    title: titleOptions,
                    detail: {
                        offsetCenter: ['-80%', 10],
                        formatter: '{value}%',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 500
                        }
                    },
                    data: [{value: disk, name: "SWAP Usage"}]
                }
            ]
        };
        gauge_disk_usage.setOption(gauge_disk_usage_options);
    }

</script>