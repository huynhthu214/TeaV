
document.addEventListener("DOMContentLoaded", function () {
  const sendCodeForm = document.getElementById("sendCodeForm");
  const verifyCodeForm = document.getElementById("verifyCodeForm");

  sendCodeForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const emailInput = document.getElementById("email");
    const email = emailInput.value.trim();

    if (email === "") {
      alert("Please enter your email.");
      return;
    }

    console.log("Sending code to: " + email);

    const modal = new bootstrap.Modal(document.getElementById("verifyCodeModal"));
    modal.show();
  });

  verifyCodeForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const code = document.getElementById("verificationCode").value.trim();

    if (code === "123456") {
      window.location.href = "reset_password.php";
    } else {
      alert("Invalid code. Please try again.");
    }
  });
});

// Xử lý khi người dùng nhập tay
document.getElementById("quantity").addEventListener("input", function () {
  const input = this;
  let value = parseInt(input.value) || 1;
  const minValue = parseInt(input.getAttribute("min")) || 1;
  const maxValue = parseInt(input.getAttribute("max")) || Infinity;

  if (value < minValue) {
    input.value = minValue;
  } else if (value > maxValue) {
    input.value = maxValue;
  } else {
    input.value = value;
  }
});
