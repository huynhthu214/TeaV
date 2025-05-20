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

  const donutCtx = document.getElementById("donutChart");
  const lineCtx = document.getElementById("lineChart");

  // Donut Chart
  if (donutCtx && donutChartInstance) {
    donutChartInstance.destroy();
  }

  if (donutCtx && typeof donutChartData !== "undefined") {
    const pieLabels = donutChartData.map((item) => item.name);
    const pieValues = donutChartData.map((item) => item.quantity);
    const pieColors = ["#4CAF50", "#FFC107", "#03A9F4", "#E91E63", "#9C27B0"];

    donutChartInstance = new Chart(donutCtx, {
      type: "doughnut",
      data: {
        labels: pieLabels,
        datasets: [
          {
            data: pieValues,
            backgroundColor: pieColors,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });
  }

  // Line Chart
  if (lineCtx && lineChartInstance) {
    lineChartInstance.destroy();
  }

  if (lineCtx && typeof lineChartData !== "undefined") {
    const months = lineChartData.map((item) => "Tháng " + item.month);
    const revenues = lineChartData.map((item) => item.revenue);

    lineChartInstance = new Chart(lineCtx, {
      type: "line",
      data: {
        labels: months,
        datasets: [
          {
            label: "Doanh thu",
            data: revenues,
            borderColor: "#28a745",
            fill: false,
            tension: 0.3,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { ticks: { autoSkip: false } },
          y: { beginAtZero: true },
        },
      },
    });
  }
});

// Các hàm bổ sung
function exportData() {
  window.location.href = "export.php";
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

function enableEdit(fieldId) {
  const input = document.getElementById(fieldId);
  input.removeAttribute("readonly");
  input.focus();
}

function deleteSelected() {
  const checkboxes = document.querySelectorAll(
    'input[name="select[]"]:checked'
  );

  if (checkboxes.length === 0) {
    alert("Vui lòng chọn ít nhất một đơn hàng để xóa.");
    return;
  }

  const orderIds = Array.from(checkboxes).map((cb) => cb.value);
  const confirmDelete = confirm(
    "Bạn có chắc chắn muốn hủy đơn hàng sau:\n" + orderIds.join(", ")
  );

  if (confirmDelete) {
    window.location.href =
      "cancel-order.php?ids=" + encodeURIComponent(orderIds.join(","));
  }
}

function showImportDetail(importId) {
  const content = document.getElementById('import-detail-' + importId).innerHTML;
  document.getElementById('importDetailContent').innerHTML = content;
}

function editImport(importId) {
  const detailBox = document.getElementById("import-detail-" + importId);
  if (!detailBox) return;

  const note = detailBox
    .querySelector("p:nth-child(3)")
    .innerText.replace("Ghi chú: ", "");
  const productName = detailBox
    .querySelector("table tbody tr td:first-child")
    .innerText.split(" (")[0];
  const quantity = detailBox.querySelector(
    "table tbody tr td:nth-child(2)"
  ).innerText;
  const unitPrice = detailBox
    .querySelector("table tbody tr td:nth-child(3)")
    .innerText.replace(/\D/g, "");

  document.getElementById("editImportId").value = importId;
  document.getElementById("editNote").value = note;
  document.getElementById("editProductName").value = productName;
  document.getElementById("editQuantity").value = quantity;
  document.getElementById("editPrice").value = unitPrice;

  const modal = new bootstrap.Modal(document.getElementById("editImportModal"));
  modal.show();
}
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('input[name="select[]"]');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });
        }
    });