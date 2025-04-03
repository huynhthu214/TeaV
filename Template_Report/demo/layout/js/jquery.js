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