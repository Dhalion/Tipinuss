import { initBalanceChart } from './charts';

document.addEventListener('livewire:navigated', () => {
    const el = document.querySelector('[data-balance-chart]');
    if (el) {
        initBalanceChart(el);
    }
});

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    const el = document.querySelector('[data-balance-chart]');
    if (el) {
        initBalanceChart(el);
    }
} else {
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.querySelector('[data-balance-chart]');
        if (el) {
            initBalanceChart(el);
        }
    });
}
