import ApexCharts from 'apexcharts';

export function initBalanceChart(el) {
    const data = JSON.parse(el.dataset.chartData || '[]');

    if (data.length < 2) {
        el.innerHTML = `<div class="flex items-center justify-center h-64 text-zinc-400 text-sm">${el.dataset.emptyText || ''}</div>`;
        return;
    }

    if (el._chartInstance) {
        el._chartInstance.destroy();
    }

    const style = getComputedStyle(document.documentElement);
    const primaryColor = style.getPropertyValue('--color-primary-500').trim() || '#7b1fa2';
    const locale = document.documentElement.lang || 'de';

    const options = {
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false },
            animations: { enabled: true, easing: 'ease-out', speed: 500 },
            fontFamily: 'Inter, sans-serif',
            foreColor: '#a1a1aa',
        },
        series: [{
            name: el.dataset.seriesName || '',
            data: data,
        }],
        stroke: {
            curve: 'smooth',
            width: 2.5,
            colors: [primaryColor],
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0,
                stops: [0, 100],
                colorStops: [
                    { offset: 0, color: primaryColor, opacity: 0.3 },
                    { offset: 100, color: primaryColor, opacity: 0 },
                ],
            },
        },
        markers: {
            size: 0,
            hover: { size: 5 },
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'dd.MM',
                style: { colors: '#a1a1aa', fontSize: '12px' },
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: (val) => val.toLocaleString(locale),
                style: { colors: '#a1a1aa', fontSize: '12px' },
            },
        },
        grid: {
            borderColor: 'rgba(113, 113, 122, 0.15)',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
        },
        tooltip: {
            theme: 'dark',
            x: { format: 'dd.MM.yyyy HH:mm' },
            y: {
                formatter: (val) => `${val.toLocaleString(locale)} 🌰`,
            },
        },
        colors: [primaryColor],
    };

    const chart = new ApexCharts(el, options);
    el._chartInstance = chart;
    chart.render();
}
