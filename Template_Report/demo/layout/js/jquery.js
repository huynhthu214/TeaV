document.getElementById("sendCodeForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value;

  if (email.trim() === "") {
      alert("Please enter your email.");
      return;
  }

  const modal = new bootstrap.Modal(document.getElementById("verifyCodeModal"));
  modal.show();
});