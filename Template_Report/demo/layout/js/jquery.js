window.addEventListener('DOMContentLoaded', function () {
  const alertBox = document.getElementById('addedAlert');
  if (alertBox) {
    setTimeout(() => {
      alertBox.style.display = 'none';
    }, 3000); // 3 giây
  }
});