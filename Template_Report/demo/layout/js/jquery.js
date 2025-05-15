let donutChartInstance = null;
let lineChartInstance = null;

document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.querySelector(".toggle-btn");
  const sidebar = document.getElementById("sidebar");
  const body = document.getElementById("mainBody");

  toggleBtn.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");
    body.classList.toggle("sidebar-collapsed");
  });

  const donutCtx = document.getElementById('donutChart');
  const lineCtx = document.getElementById('lineChart');

  // Donut Chart
  if (donutCtx && donutChartInstance) {
    donutChartInstance.destroy();
  }

  if (donutCtx && typeof donutChartData !== 'undefined') {
    const pieLabels = donutChartData.map(item => item.name);
    const pieValues = donutChartData.map(item => item.quantity);
    const pieColors = ['#4CAF50', '#FFC107', '#03A9F4', '#E91E63', '#9C27B0'];

    donutChartInstance = new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieValues,
          backgroundColor: pieColors
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }

  // Line Chart
  if (lineCtx && lineChartInstance) {
    lineChartInstance.destroy();
  }

  if (lineCtx && typeof lineChartData !== 'undefined') {
    const months = lineChartData.map(item => 'Tháng ' + item.month);
    const revenues = lineChartData.map(item => item.revenue);

    lineChartInstance = new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'Doanh thu',
          data: revenues,
          borderColor: '#28a745',
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { ticks: { autoSkip: false } },
          y: { beginAtZero: true }
        }
      }
    });
  }
});

// Các hàm bổ sung
function exportData() {
  window.location.href = 'export.php';
}

function togglePassword(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
