import './bootstrap';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

// ဒီနေရာမှာတင် Register လုပ်လိုက်ပါ
Chart.register(ChartDataLabels);

// Global အဖြစ် သတ်မှတ်ပေးပါ
window.Chart = Chart;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

