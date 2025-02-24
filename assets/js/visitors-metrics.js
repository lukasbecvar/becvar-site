/** visitors metrics charts function */
// import apexcharts library
import ApexCharts from 'apexcharts'
document.addEventListener("DOMContentLoaded", function () {
    // get visitor metrics data from global scope
    const visitorMetrics = window.visitorMetrics

    // compute min and max values from the data for integer tick configuration
    const dataValues = Object.values(visitorMetrics)

    // configuration for chart with integer-only y-axis ticks and background grid
    const options = {
        series: [{
            data: dataValues
        }],
        chart: {
            height: 275,
            type: 'area',
            zoom: {
                enabled: false
            },
            background: 'transparent',
            toolbar: {
                show: false // hide toolbar
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 3,
            curve: 'smooth',
            colors: ['#009900']
        },
        markers: {
            size: 5,
            colors: ['#009900'],
            strokeColors: '#ffffff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.3,
                gradientToColors: ['#009900'],
                inverseColors: false,
                opacityFrom: 0.6,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        title: {
            align: 'left',
            text: 'Visitors count',
            style: {
                color: '#ffffff',
                fontSize: '22px',
                fontWeight: 'bold',
                fontFamily: 'Helvetica, Arial, sans-serif'
            }
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '12px'
            },
            x: {
                show: false
            },
            marker: {
                show: true
            },
            y: {
                formatter: function(value) {
                    return Math.round(value)
                }
            }
        },
        grid: {
            show: true,
            borderColor: 'rgba(255, 255, 255, 0.2)',
            strokeDashArray: 0,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            row: {
                colors: ['#1d1d1d', 'transparent'],
                opacity: 0.3
            }
        },
        xaxis: {
            categories: Object.keys(visitorMetrics),
            labels: {
                style: {
                    colors: '#ffffff',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                },
                rotate: 0,
                maxHeight: 50,
                hideOverlappingLabels: true,
                formatter: (value) => value
            },
            tickAmount: 'dataPoints'
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#ffffff'
                }
            }
        }
    }

    // render the chart element
    const chart = new ApexCharts(document.querySelector("#chart"), options)
    chart.render()
})