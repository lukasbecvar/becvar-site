/** visitors metrics charts script **/
import ApexCharts from 'apexcharts'

// get visitor metrics from window
const visitorMetrics = window.visitorMetrics

// visitors count config
var options = {
    series: [{
        data: Object.values(visitorMetrics)
    }],
    chart: {
        height: 350,
        type: 'area',
        zoom: {
            enabled: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'straight',
        width: 3,
        colors: ['#009900']
    },
    fill: {
        type: 'solid',
        colors: ['#009900'],
        opacity: 0.5
    },
    title: {
        text: 'Visitors count',
        align: 'left',
        style: {
            color: '#ffffff',
            fontSize: '20px',
            fontWeight: 'bold'
        }
    },
    tooltip: {
        theme: 'dark',
        style: {
            fontSize: '12px'
        }
    },
    grid: {
        row: {
            colors: ['#1d1d1d', 'transparent'],
            opacity: 0.5
        },
        borderColor: 'rgba(255, 255, 255, 0.24)'
    },
    xaxis: {
        categories: Object.keys(visitorMetrics),
        labels: {
            style: {
                colors: '#ffffff'
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#ffffff'
            }
        }
    }
}

// render visitors count chart
var chart = new ApexCharts(document.querySelector("#chart"), options)
chart.render()
