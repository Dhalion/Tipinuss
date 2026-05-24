import { initBalanceChart } from './charts';

function initChartIfPresent() {
    const el = document.querySelector('[data-balance-chart]');
    if (el) {
        initBalanceChart(el);
    }
}

document.addEventListener('livewire:init', initChartIfPresent);
document.addEventListener('livewire:navigated', initChartIfPresent);
