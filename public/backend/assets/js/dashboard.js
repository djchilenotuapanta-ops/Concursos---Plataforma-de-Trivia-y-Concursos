/*
|--------------------------------------------------------------------------
| Dashboard UI JS (Laravel 12)
| - Toggle sidebar (mobile)
| - Render charts if canvas exists
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('dashSidebar');
  const toggle = document.getElementById('sidebarToggle');

  if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });

    // Cerrar sidebar al tocar fuera
    document.addEventListener('click', (e) => {
      if (window.innerWidth > 992) return;
      const clickedInside = sidebar.contains(e.target) || toggle.contains(e.target);
      if (!clickedInside) sidebar.classList.remove('open');
    });
  }

  // Chart de barras (si existe)
  const canvas = document.getElementById('statsChart');
  if (canvas && window.Chart) {
    const raw = canvas.getAttribute('data-values');
    let values = [0, 0, 0];
    try { values = raw ? JSON.parse(raw) : values; } catch (e) {}

    const labels = ['Concursos', 'Usuarios', 'Premios'];

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Estadísticas',
          data: values,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }
});
