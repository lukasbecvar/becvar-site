/** visitors metrics charts function */
import ApexCharts from 'apexcharts'

// get visitor metrics data from view
const visitorMetrics = window.visitorMetrics

// visitors count config
var options = {
    series: [{
        data: Object.values(visitorMetrics)
    }],
    chart: {
        height: 275,
        type: 'area',
        zoom: {
            enabled: false
        },
        padding: {
            right: 0
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 3,
        curve: 'straight',
        colors: ['#009900']
    },
    fill: {
        opacity: 0.5,
        type: 'solid',
        colors: ['#009900']
    },
    title: {
        align: 'left',
        text: 'Visitors count',
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
            opacity: 0.5,
            colors: ['#1d1d1d', 'transparent']
        },
        borderColor: 'rgba(255, 255, 255, 0.24)'
    },
    xaxis: {
        categories: Object.keys(visitorMetrics),
        labels: {
            style: {
                colors: '#ffffff'
            },
            rotate: 0,
            maxHeight: 50,
            hideOverlappingLabels: true,
            formatter: (value) => {
                return value;
            }
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

// render visitors count chart
var chart = new ApexCharts(document.querySelector("#chart"), options)
chart.render()
