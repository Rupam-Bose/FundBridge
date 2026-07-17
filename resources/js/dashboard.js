/**
 * FundBridge — Dashboard JavaScript
 * Handles: Charts (Chart.js), Sidebar collapse, Notifications
 */

document.addEventListener('DOMContentLoaded', () => {

    /* Sidebar Collapse */

    const sidebar = document.querySelector('.sidebar');
    const dashMain = document.querySelector('.dashboard-main');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    const sidebarCollapsed = localStorage.getItem('fb-sidebar-collapsed') === 'true';

    if (sidebarCollapsed && sidebar) {
        sidebar.classList.add('collapsed');
        if (dashMain) dashMain.style.marginLeft = '72px';
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const isNowCollapsed = sidebar.classList.contains('collapsed');
            if (dashMain) {
                dashMain.style.marginLeft = isNowCollapsed ? '72px' : '260px';
            }
            localStorage.setItem('fb-sidebar-collapsed', isNowCollapsed);
            toggleBtn.querySelector('i').className = isNowCollapsed
                ? 'fa-solid fa-chevron-right'
                : 'fa-solid fa-chevron-left';
        });
    }

    /* Chart: Fundraising Growth (Line) */

    const lineCtx = document.getElementById('fundraisingChart');
    if (lineCtx && typeof Chart !== 'undefined') {
        const labels = JSON.parse(lineCtx.dataset.labels || '[]');
        const values = JSON.parse(lineCtx.dataset.values || '[]');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Amount Raised ($)',
                    data: values,
                    fill: true,
                    borderColor: '#00d99c',
                    backgroundColor: 'rgba(0, 217, 156, 0.12)',
                    tension: 0.45,
                    pointBackgroundColor: '#00d99c',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1c34',
                        borderColor: 'rgba(0,217,156,0.3)',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: '#9eacc2',
                        padding: 12,
                        callbacks: {
                            label: ctx => ` $${ctx.parsed.y.toLocaleString()}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9eacc2', font: { size: 12 } },
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            color: '#9eacc2',
                            font: { size: 12 },
                            callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
                        },
                    },
                },
            },
        });
    }

    /* Chart: Interest Distribution (Donut) */

    const donutCtx = document.getElementById('interestChart');
    if (donutCtx && typeof Chart !== 'undefined') {
        const labels = JSON.parse(donutCtx.dataset.labels || '[]');
        const values = JSON.parse(donutCtx.dataset.values || '[]');

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#ef4444', '#fb923c', '#6b7280'],
                    borderColor: 'transparent',
                    hoverBorderColor: '#fff',
                    hoverBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9eacc2',
                            padding: 16,
                            usePointStyle: true,
                            font: { size: 12 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#0b1c34',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: '#9eacc2',
                    },
                },
            },
        });
    }

    /* Chart: Admin User Growth (Bar) */

    const barCtx = document.getElementById('userGrowthChart');
    if (barCtx && typeof Chart !== 'undefined') {
        const labels = JSON.parse(barCtx.dataset.labels || '[]');
        const values = JSON.parse(barCtx.dataset.values || '[]');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'New Users',
                    data: values,
                    backgroundColor: 'rgba(0, 217, 156, 0.25)',
                    borderColor: '#00d99c',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0b1c34',
                        borderColor: 'rgba(0,217,156,0.3)',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: '#9eacc2',
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9eacc2', font: { size: 12 } },
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9eacc2', font: { size: 12 }, stepSize: 1 },
                    },
                },
            },
        });
    }

    /* Chart: Admin Sector Distribution (Pie) */

    const sectorCtx = document.getElementById('sectorChart');
    if (sectorCtx && typeof Chart !== 'undefined') {
        const labels = JSON.parse(sectorCtx.dataset.labels || '[]');
        const values = JSON.parse(sectorCtx.dataset.values || '[]');

        const colors = [
            '#00d99c','#2563eb','#8b5cf6','#f59e0b',
            '#ef4444','#10b981','#f97316','#06b6d4',
        ];

        new Chart(sectorCtx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderColor: 'transparent',
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9eacc2',
                            padding: 12,
                            usePointStyle: true,
                            font: { size: 12 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#0b1c34',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: '#9eacc2',
                    },
                },
            },
        });
    }

    /* Animate Stats on Load */

    const animateCounters = () => {
        document.querySelectorAll('.counter').forEach(el => {
            const target = parseFloat(el.dataset.target || el.textContent);
            const isFloat = el.dataset.float === 'true';
            const prefix  = el.dataset.prefix || '';
            const suffix  = el.dataset.suffix || '';
            let start = 0;
            const step = target / 60;
            const timer = setInterval(() => {
                start += step;
                if (start >= target) {
                    start = target;
                    clearInterval(timer);
                }
                el.textContent = prefix + (isFloat ? start.toFixed(1) : Math.floor(start).toLocaleString()) + suffix;
            }, 16);
        });
    };

    animateCounters();

});
