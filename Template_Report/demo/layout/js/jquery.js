document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("openRegisterModal")
    .addEventListener("click", function () {
      var loginModal = new bootstrap.Modal(
        document.getElementById("loginModal")
      );
      var registerModal = new bootstrap.Modal(
        document.getElementById("registerModal")
      );

      loginModal.hide();
      setTimeout(() => registerModal.show(), 500);
    });

  document
    .getElementById("openLoginModal")
    .addEventListener("click", function () {
      var loginModal = new bootstrap.Modal(
        document.getElementById("loginModal")
      );
      var registerModal = new bootstrap.Modal(
        document.getElementById("registerModal")
      );

      registerModal.hide();
      setTimeout(() => loginModal.show(), 500);
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
