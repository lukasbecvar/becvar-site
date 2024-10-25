import Chart from 'chart.js/auto'

// get visitor metrics from window
const visitorMetrics = window.visitorMetrics

// visitors count chart
const visitorsCountCtx = document.getElementById('visitorsCountChart').getContext('2d');
const visitorsCountChart = new Chart(visitorsCountCtx, {
    type: 'line',
    data: {
        labels: Object.keys(visitorMetrics.visitorsCount),
        datasets: [{
            label: 'Visitors count',
            data: Object.values(visitorMetrics.visitorsCount),
            borderColor: 'rgba(16, 151, 241, 1)',
            backgroundColor: 'rgba(16, 151, 241, 0.5)',
            fill: true,
            tension: 0,
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#ffffff'
                }
            },
            x: {
                ticks: {
                    color: '#ffffff'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: "white"
                }
            },
            title: {
                display: true,
                text: 'Visitors Count',
                color: 'white',
                font: {
                    size: 20
                }
            }
        }
    }
})
