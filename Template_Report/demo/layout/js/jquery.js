document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.querySelector(".toggle-btn");
  const sidebar = document.querySelector(".sidebar");

  toggleBtn.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");
  });

  // Biểu đồ line
  const ctxLine = document.getElementById('lineChart').getContext('2d');
  const lineChart = new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Earnings',
        data: [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 40000],
        borderColor: '#4e73df',
        backgroundColor: 'rgba(78, 115, 223, 0.05)',
        tension: 0.3,
        fill: true,
        pointRadius: 3,
        pointBackgroundColor: '#4e73df',
        pointBorderColor: '#4e73df'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '$' + value.toLocaleString();
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // Biểu đồ donut
  const ctxDonut = document.getElementById('donutChart').getContext('2d');
  const donutChart = new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
      labels: ['Direct', 'Social', 'Referral'],
      datasets: [{
        data: [55, 30, 15],
        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
        hoverOffset: 10
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      cutout: '70%' // Donut effect
    }
  });
});

  function exportData() {
    // Ví dụ: chuyển hướng đến file export
    window.location.href = 'export.php'; // Thay bằng đường dẫn thực tế của bạn
  }