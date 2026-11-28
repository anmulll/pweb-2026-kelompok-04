import './bootstrap';

import Chart from 'chart.js/auto';

window.Chart = Chart;

// Auto-Refresh Polling for Order & Payment Status (ADR-04 & Architecture §5)
document.addEventListener('DOMContentLoaded', () => {
    const metaEl = document.getElementById('order-meta');
    if (!metaEl) return;

    const initialOrderStatus = metaEl.dataset.orderStatus;
    const initialPaymentStatus = metaEl.dataset.paymentStatus;
    const statusUrl = metaEl.dataset.statusUrl;

    if (initialOrderStatus !== 'pending_payment' || initialPaymentStatus !== 'pending') {
        return;
    }

    let isPolling = true;

    const checkStatus = async () => {
        if (!isPolling || document.hidden) return;

        try {
            const response = await fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.order_status !== initialOrderStatus || data.payment_status !== initialPaymentStatus) {
                    isPolling = false;
                    window.location.reload();
                }
            }
        } catch (error) {
            console.warn('Auto-refresh status check encountered an error:', error);
        }
    };

    // Poll every 5 seconds
    const intervalId = setInterval(checkStatus, 5000);

    // Immediate check when returning to visible tab
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && isPolling) {
            checkStatus();
        }
    });
});
