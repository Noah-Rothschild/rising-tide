function showToast(message, type = "success") {
  const container = document.getElementById("toast-container");

  const toast = document.createElement("div");
  toast.classList.add("toast", type);
  toast.innerText = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 2500);
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("addToCartForm");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const quantity = document.getElementById("quantity").value;
      const productName = document.querySelector(".product-title").innerText;
      const formData = new FormData(form);

      fetch("add_to_cart.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast(`${productName} added to cart (x${quantity})`);
          } else {
            showToast(data.message || "Error adding to cart", "error");
          }
        })
        .catch((error) => {
          showToast("Error adding to cart", "error");
          console.error("Error:", error);
        });
    });
  }
});
