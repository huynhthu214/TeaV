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
function exportData(button) {
  const type = button.getAttribute("data-type");
  if (!type) {
    alert("Không xác định loại dữ liệu cần export.");
    return;
  }

  // Chuyển hướng để tải file CSV về
  window.location.href = "export.php?type=" + type;
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
  const content = document.getElementById(
    "import-detail-" + importId
  ).innerHTML;
  document.getElementById("importDetailContent").innerHTML = content;
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
document.addEventListener("DOMContentLoaded", function () {
  const selectAll = document.getElementById("select-all");
  const checkboxes = document.querySelectorAll('input[name="select[]"]');

  if (selectAll) {
    selectAll.addEventListener("change", function () {
      checkboxes.forEach(function (checkbox) {
        checkbox.checked = selectAll.checked;
      });
    });
  }
});
function updateTotalAmount() {
  let total = 0;
  document.querySelectorAll("#product-list .product-item").forEach((item) => {
    const select = item.querySelector(".product-select");
    const quantity = parseInt(item.querySelector(".quantity-input").value) || 0;
    const price =
      parseFloat(select.selectedOptions[0]?.getAttribute("data-price")) || 0;
    total += price * quantity;
  });
  document.getElementById("total-amount").value =
    total.toLocaleString("vi-VN") + " VND";
}

document.getElementById("addProductRow").addEventListener("click", () => {
  const productList = document.getElementById("product-list");
  const firstRow = productList.querySelector(".product-item");
  const newRow = firstRow.cloneNode(true);
  newRow.querySelector(".product-select").value = "";
  newRow.querySelector(".quantity-input").value = 1;
  productList.appendChild(newRow);
  updateTotalAmount();
});

document.addEventListener("click", function (e) {
  if (e.target.closest(".remove-product")) {
    const row = e.target.closest(".product-item");
    const productList = document.getElementById("product-list");
    if (productList.querySelectorAll(".product-item").length > 1) {
      row.remove();
      updateTotalAmount();
    }
  }
});

document.addEventListener("input", function (e) {
  if (
    e.target.matches(".quantity-input") ||
    e.target.matches(".product-select")
  ) {
    updateTotalAmount();
  }
});

document
  .getElementById("addOrderModal")
  .addEventListener("show.bs.modal", function () {
    updateTotalAmount();
  });

// function generateOrderId() {
//   fetch('generate-order-id.php')
//     .then(response => response.text())
//     .then(orderId => {
//       document.getElementById('orderId').value = orderId;
//     })
//     .catch(error => {
//       console.error("Lỗi khi tạo mã đơn hàng:", error);
//     });
// }

// // Gọi khi modal mở
// document.getElementById('addOrderModal').addEventListener('show.bs.modal', function ()) {
//   generateOrderId();
//   updateTotalAmount();
// }
