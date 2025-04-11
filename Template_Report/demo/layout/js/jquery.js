document
  .getElementById("sendCodeForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value;

    if (email.trim() === "") {
      alert("Please enter your email.");
      return;
    }

    const modal = new bootstrap.Modal(
      document.getElementById("verifyCodeModal")
    );
    modal.show();
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

document.getElementById("searchButton").addEventListener("click", function () {
  const searchValue = document
    .getElementById("searchInput")
    .value.toLowerCase();
  const herbalFilter = document.getElementById("herbalFilter").value;
  const priceFilter = document.getElementById("priceFilter").value;
  const useFilter = document.getElementById("useFilter").value;

  const productCards = document.querySelectorAll(".product-card");

  productCards.forEach((card) => {
    const name = card.querySelector(".card-title").textContent.toLowerCase();
    const type = card.getAttribute("data-type");
    const price = card.getAttribute("data-price");
    const use = card.getAttribute("data-use");

    const matchesSearch = searchValue === "" || name.includes(searchValue);
    const matchesHerbal = herbalFilter === "" || type === herbalFilter;
    const matchesPrice = priceFilter === "" || price === priceFilter;
    const matchesUse = useFilter === "" || use === useFilter;

    if (matchesSearch && matchesHerbal && matchesPrice && matchesUse) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
});

document.getElementById("searchInput").addEventListener("input", function () {
  document.getElementById("searchButton").click();
});
document.getElementById("herbalFilter").addEventListener("change", function () {
  document.getElementById("searchButton").click();
});
document.getElementById("priceFilter").addEventListener("change", function () {
  document.getElementById("searchButton").click();
});
document.getElementById("useFilter").addEventListener("change", function () {
  document.getElementById("searchButton").click();
});
