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

const searchInput = document.getElementById("searchInput");
const herbalFilter = document.getElementById("herbalFilter");
const priceFilter = document.getElementById("priceFilter");
const useFilter = document.getElementById("useFilter");
const productList = document.getElementById("productList");
const productCards = productList.getElementsByClassName("product-card");

function applyFilters() {
  const searchText = searchInput.value.toLowerCase();
  const herbalValue = herbalFilter.value;
  const priceValue = priceFilter.value;
  const useValue = useFilter.value;

  for (let i = 0; i < productCards.length; i++) {
    const card = productCards[i];
    const cardTitle = card
      .querySelector(".card-title")
      .textContent.toLowerCase();
    const herbalType = card.getAttribute("data-type") || "";
    const price = card.getAttribute("data-price") || "";
    const useType = card.getAttribute("data-use") || "";
    const matchesSearch = cardTitle.includes(searchText);
    const matchesHerbal = !herbalValue || herbalType === herbalValue;
    let matchesPrice = true;
    if (priceValue) {
      if (priceValue === "low" && price !== "low") matchesPrice = false;
      else if (priceValue === "mid" && price !== "mid") matchesPrice = false;
      else if (priceValue === "high" && price !== "high") matchesPrice = false;
    }

    const matchesUse = !useValue || useType === useValue;

    card.style.display =
      matchesSearch && matchesHerbal && matchesPrice && matchesUse
        ? ""
        : "none";
  }
}

// Add event listeners to all filter inputs
searchInput.addEventListener("keyup", applyFilters);
herbalFilter.addEventListener("change", applyFilters);
priceFilter.addEventListener("change", applyFilters);
useFilter.addEventListener("change", applyFilters);

// Initial filter application
applyFilters();
