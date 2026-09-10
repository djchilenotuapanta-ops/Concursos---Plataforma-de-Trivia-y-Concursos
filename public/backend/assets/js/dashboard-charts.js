/*
|-------------------------------------------------------------
| Dashboard Charts (Empresa / Usuario)
|-------------------------------------------------------------
| - Usa Chart.js (ya cargado en layouts/dashboard.blade.php)
| - No rompe la lógica del negocio
| - Si no existe el canvas o no hay datos, no hace nada
*/

(function () {
  function isValidPayload(p) {
    return !!(
      p &&
      Array.isArray(p.labels) &&
      Array.isArray(p.values)
    );
  }

  function safeNumber(n) {
    var x = Number(n);
    return Number.isFinite(x) ? x : 0;
  }

  function renderBar(canvasId, payload) {
    var el = document.getElementById(canvasId);
    if (!el) return;
    if (!window.Chart) return;
    if (!isValidPayload(payload) || payload.labels.length === 0) return;

    var ctx = el.getContext('2d');
    if (!ctx) return;

    // Si se vuelve a cargar la vista por SPA/turbolinks, evitamos duplicados
    try {
      if (el.__chartInstance) {
        el.__chartInstance.destroy();
      }
    } catch (e) {
      // No hacemos nada
    }

    var values = payload.values.map(safeNumber);

    el.__chartInstance = new window.Chart(ctx, {
      type: 'bar',
      data: {
        labels: payload.labels,
        datasets: [
          {
            label: 'Total',
            data: values,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 },
          },
        },
      },
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.__companyBarChart) {
      renderBar('companyStatsBar', window.__companyBarChart);
    }

    if (window.__userBarChart) {
      renderBar('userStatsBar', window.__userBarChart);
    }
  });
})();
