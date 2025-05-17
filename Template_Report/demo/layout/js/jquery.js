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
    input.removeAttribute('readonly');
    input.focus();
}

function submitField(fieldName) {
    const input = document.getElementById(fieldName);
    if (!input.hasAttribute('readonly')) {
        document.getElementById('fieldInput').value = fieldName;
        document.getElementById('mainForm').submit();
    }
}