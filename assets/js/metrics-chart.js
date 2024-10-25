import Chart from 'chart.js/auto'

// get visitor metrics from window
const visitorMetrics = window.visitorMetrics

// visitors count chart
const visitorsCountCtx = document.getElementById('visitorsCountChart').getContext('2d')
const visitorsCountChart = new Chart(visitorsCountCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(visitorMetrics.visitorsCount),
        datasets: [{
            label: 'Visitors count',
            data: Object.values(visitorMetrics.visitorsCount),
            backgroundColor: 'rgba(16, 151, 241, 0.5)',
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
        }
    }
})

// visitors browsers chart
const visitorsBrowsersCtx = document.getElementById('visitorsBrowsersChart').getContext('2d')
const visitorsBrowsersChart = new Chart(visitorsBrowsersCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(visitorMetrics.visitorsBrowsers),
        datasets: [{
            label: 'Browsers',
            data: Object.values(visitorMetrics.visitorsBrowsers),
        }]
    },
    options: {
        plugins: {
        legend: {
            labels: {
                color: "white",
                    font: {
                        size: 16
                    }
                },
            },
            title: {
                display: true,
                text: 'Browsers',
                color: 'white',
                font: {
                    size: 20
                }
            }
        }
    }
})

// visitors country chart
const visitorsCountryCtx = document.getElementById('visitorsCountryChart').getContext('2d')
const visitorsCountryChart = new Chart(visitorsCountryCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(visitorMetrics.visitorsCountry),
        datasets: [{
            label: 'Country',
            data: Object.values(visitorMetrics.visitorsCountry),
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: "white",
                    font: {
                        size: 16
                    }
                },
            },
            title: {
                display: true,
                text: 'Country',
                color: 'white',
                font: {
                    size: 20
                }
            }
        }
    }
})

// visitors city chart
const visitorsCityCtx = document.getElementById('visitorsCityChart').getContext('2d')
const visitorsCityChart = new Chart(visitorsCityCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(visitorMetrics.visitorsCity),
        datasets: [{
            label: 'City',
            data: Object.values(visitorMetrics.visitorsCity),
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: "white",
                    font: {
                        size: 16
                    }
                },
            },
            title: {
                display: true,
                text: 'City',
                color: 'white',
                font: {
                    size: 20
                }
            }
        }
    }
})
