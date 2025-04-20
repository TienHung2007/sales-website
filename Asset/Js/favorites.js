

// Hàm cập nhật số đếm giỏ hàng (tổng số loại sản phẩm, không phải tổng quantity)
function updateCartCount() {
  const cartItems = JSON.parse(localStorage.getItem("cart")) || [];
  const headerCartCount = document.querySelector(".header-user-actions .action-btn ion-icon[name='cart-outline'] ~ .count");
  const mobileCartCount = document.querySelector(".mobile-bottom-navigation .action-btn[href*='Payment.php'] .count");

  if (headerCartCount) headerCartCount.textContent = cartItems.length;
  if (mobileCartCount) mobileCartCount.textContent = cartItems.length;
}

// Hàm hiển thị sản phẩm yêu thích
function displayFavorites() {
  const favoriteProducts = document.getElementById("favoriteProducts");
  const noFavorites = document.getElementById("noFavorites");
  const favorites = JSON.parse(localStorage.getItem("favorites")) || [];

  if (!favoriteProducts || !noFavorites) return;

  if (favorites.length === 0) {
    noFavorites.style.display = "block";
    favoriteProducts.innerHTML = "";
    return;
  }

  noFavorites.style.display = "none";
  favoriteProducts.innerHTML = "";

  favorites.forEach(product => {
    const showcase = document.createElement("div");
    showcase.classList.add("showcase");

    const imgDefault = product.imgDefault || product.img || "";
    const imgHover = product.imgHover || imgDefault;
    const category = product.category || "Không xác định";
    const price = product.price || "0k";
    const title = product.title || "Sản phẩm không tên";

    showcase.innerHTML = `
      <div class="showcase-banner">
        <img src="${imgDefault}" alt="${title}" width="300" class="product-img default">
        <img src="${imgHover}" alt="${title}" width="300" class="product-img hover">
        <div class="showcase-actions">
          <button class="btn-action" data-title="${title}">
            <ion-icon name="heart"></ion-icon>
          </button>
          <button class="btn-action">
            <ion-icon name="eye-outline"></ion-icon>
          </button>
          <button class="btn-action" data-title="${title}">
            <ion-icon name="bag-add-outline"></ion-icon>
          </button>
        </div>
      </div>
      <div class="showcase-content">
        <a href="#" class="showcase-category">${category}</a>
        <a href="#">
          <h3 class="showcase-title">${title}</h3>
        </a>
        <div class="price-box">
          <p class="price">${price}</p>
        </div>
      </div>
    `;
    favoriteProducts.appendChild(showcase);
  });
}

// Hàm hiển thị sản phẩm trong giỏ hàng trên trang thanh toán
function displayCartItems() {
  const orderSummary = document.querySelector(".order-summary");
  const cartItems = JSON.parse(localStorage.getItem("cart")) || [];

  if (!orderSummary) return;

  const productContainer = orderSummary.querySelectorAll(".product-item");
  productContainer.forEach(item => item.remove());

  if (cartItems.length === 0) {
    orderSummary.innerHTML += `<p>Chưa có sản phẩm trong giỏ hàng.</p>`;
    return;
  }

  let totalPrice = 0;
  cartItems.forEach(product => {
    const quantity = product.quantity || 1;
    const summaryItem = document.createElement("div");
    summaryItem.classList.add("summary-item", "product-item");
    summaryItem.innerHTML = `
      <img src="${product.imgDefault || product.img}" alt="${product.title}">
      <div class="product-info">
        <span>${product.title}</span>
        <span>${product.price}</span>
      </div>
      <div class="quantity-controls">
        <button class="decrease-qty" data-title="${product.title}">-</button>
        <span class="quantity">${quantity}</span>
        <button class="increase-qty" data-title="${product.title}">+</button>
      </div>
      <button class="remove-from-cart" data-title="${product.title}">
        <ion-icon name="trash-outline"></ion-icon>
      </button>
    `;
    orderSummary.insertBefore(summaryItem, orderSummary.querySelector(".total"));

    const price = parseFloat(product.price.replace(/[^\d.]/g, ""));
    totalPrice += price * quantity;
  });

  const totalElement = orderSummary.querySelector(".total .price");
  if (totalElement) totalElement.textContent = `${totalPrice.toLocaleString()}k`;

  const shippingElement = orderSummary.querySelector(".shipping span:last-child");
  if (shippingElement) {
    shippingElement.textContent = totalPrice >= 500 ? "Miễn phí (Đơn trên 500k)" : "30k";
  }
}

// Xử lý sự kiện khi trang tải
document.addEventListener("DOMContentLoaded", function () {
  updateFavoriteCount();
  updateCartCount();
  displayFavorites();
  displayCartItems();

  // Xử lý nút "tim" trên trang sản phẩm (nếu có #productGrid)
  const productGrid = document.getElementById("productGrid");
  if (productGrid) {
    const heartButtons = productGrid.querySelectorAll(
      ".btn-action ion-icon[name='heart-outline'], .btn-action ion-icon[name='heart']"
    );
    heartButtons.forEach(button => {
      const product = button.closest(".showcase");
      const productTitle = product.querySelector(".showcase-title").textContent.trim();
      let favorites = JSON.parse(localStorage.getItem("favorites")) || [];
      const isFavorite = favorites.some(fav => fav.title === productTitle);

      if (isFavorite) {
        button.setAttribute("name", "heart");
        button.closest(".btn-action").style.color = "var(--salmon-pink)";
      }

      button.addEventListener("click", function () {
        const parentButton = this.closest(".btn-action");
        const productImgDefault = product.querySelector(".product-img.default").src;
        const productImgHover = product.querySelector(".product-img.hover")?.src || productImgDefault;
        const productCategory = product.querySelector(".showcase-category").textContent.trim();
        const productPrice = product.querySelector(".price").textContent.trim();
        favorites = JSON.parse(localStorage.getItem("favorites")) || [];
        const isCurrentlyFavorite = favorites.some(fav => fav.title === productTitle);

        if (!isCurrentlyFavorite) {
          this.setAttribute("name", "heart");
          parentButton.style.color = "var(--salmon-pink)";
          favorites.push({
            imgDefault: productImgDefault,
            imgHover: productImgHover,
            title: productTitle,
            category: productCategory,
            price: productPrice
          });
        } else {
          this.setAttribute("name", "heart-outline");
          parentButton.style.color = "";
          favorites = favorites.filter(fav => fav.title !== productTitle);
        }

        localStorage.setItem("favorites", JSON.stringify(favorites));
        updateFavoriteCount();
      });
    });

    // Xử lý nút "thêm vào giỏ hàng" trên trang sản phẩm
    const addToCartButtons = productGrid.querySelectorAll(".btn-action ion-icon[name='bag-add-outline']");
    addToCartButtons.forEach(button => {
      button.addEventListener("click", function () {
        const product = button.closest(".showcase");
        const productImgDefault = product.querySelector(".product-img.default").src;
        const productImgHover = product.querySelector(".product-img.hover")?.src || productImgDefault;
        const productTitle = product.querySelector(".showcase-title").textContent.trim();
        const productCategory = product.querySelector(".showcase-category").textContent.trim();
        const productPrice = product.querySelector(".price").textContent.trim();

        let cartItems = JSON.parse(localStorage.getItem("cart")) || [];
        if (!cartItems.some(item => item.title === productTitle)) {
          cartItems.push({
            imgDefault: productImgDefault,
            imgHover: productImgHover,
            title: productTitle,
            category: productCategory,
            price: productPrice,
            quantity: 1 // Số lượng mặc định là 1
          });
          localStorage.setItem("cart", JSON.stringify(cartItems));
          updateCartCount();
          console.log("Added to cart:", { title: productTitle, price: productPrice, quantity: 1 });
        } else {
          console.log("Sản phẩm đã có trong giỏ hàng:", productTitle);
        }
      });
    });
  } else {
    console.log("Không tìm thấy #productGrid trong HTML.");
  }

  // Xử lý nút "thêm vào giỏ hàng" trên trang yêu thích
  const addToCartButtons = document.querySelectorAll(
    ".btn-action ion-icon[name='bag-add-outline']"
  );
  addToCartButtons.forEach(button => {
    button.addEventListener("click", function () {
      const product = button.closest(".showcase");
      const productImgDefault = product.querySelector(".product-img.default").src;
      const productImgHover = product.querySelector(".product-img.hover")?.src || productImgDefault;
      const productTitle = product.querySelector(".showcase-title").textContent.trim();
      const productCategory = product.querySelector(".showcase-category").textContent.trim();
      const productPrice = product.querySelector(".price").textContent.trim();

      let cartItems = JSON.parse(localStorage.getItem("cart")) || [];
      if (!cartItems.some(item => item.title === productTitle)) {
        cartItems.push({
          imgDefault: productImgDefault,
          imgHover: productImgHover,
          title: productTitle,
          category: productCategory,
          price: productPrice,
          quantity: 1 // Số lượng mặc định là 1
        });
        localStorage.setItem("cart", JSON.stringify(cartItems));
        updateCartCount();
        console.log("Added to cart:", { title: productTitle, price: productPrice, quantity: 1 });
      } else {
        console.log("Sản phẩm đã có trong giỏ hàng:", productTitle);
      }
    });
  });

  // Xử lý bỏ thích trên trang yêu thích
  const favoriteProducts = document.getElementById("favoriteProducts");
  if (favoriteProducts) {
    favoriteProducts.addEventListener("click", function (e) {
      const heartButton = e.target.closest(".btn-action");
      if (heartButton && heartButton.querySelector("ion-icon[name='heart']")) {
        const titleToRemove = heartButton.dataset.title;
        let favorites = JSON.parse(localStorage.getItem("favorites")) || [];

        console.log("Attempting to remove:", titleToRemove);
        console.log("Current favorites:", favorites);

        const initialLength = favorites.length;
        favorites = favorites.filter(fav => fav.title !== titleToRemove);

        if (favorites.length < initialLength) {
          localStorage.setItem("favorites", JSON.stringify(favorites));
          console.log("Favorites after removal:", favorites);

          const product = heartButton.closest(".showcase");
          if (product) product.remove();

          const noFavorites = document.getElementById("noFavorites");
          if (favorites.length === 0) {
            noFavorites.style.display = "block";
          }

          updateFavoriteCount();
        } else {
          console.error("Không tìm thấy sản phẩm với tiêu đề:", titleToRemove);
        }
      }
    });
  }

  // Xử lý nút "bỏ khỏi giỏ hàng" và thay đổi số lượng trên trang thanh toán
  const orderSummary = document.querySelector(".order-summary");
  if (orderSummary) {
    orderSummary.addEventListener("click", function (e) {
      const removeButton = e.target.closest(".remove-from-cart");
      const increaseButton = e.target.closest(".increase-qty");
      const decreaseButton = e.target.closest(".decrease-qty");

      let cartItems = JSON.parse(localStorage.getItem("cart")) || [];

      // Xử lý xóa khỏi giỏ hàng
      if (removeButton) {
        const titleToRemove = removeButton.dataset.title;
        console.log("Attempting to remove from cart:", titleToRemove);
        console.log("Current cart:", cartItems);

        const initialLength = cartItems.length;
        cartItems = cartItems.filter(item => item.title !== titleToRemove);

        if (cartItems.length < initialLength) {
          localStorage.setItem("cart", JSON.stringify(cartItems));
          console.log("Cart after removal:", cartItems);
          displayCartItems();
          updateCartCount();
        } else {
          console.error("Không tìm thấy sản phẩm trong giỏ hàng với tiêu đề:", titleToRemove);
        }
      }

      // Xử lý tăng số lượng
      if (increaseButton) {
        const titleToUpdate = increaseButton.dataset.title;
        cartItems = cartItems.map(item => {
          if (item.title === titleToUpdate) {
            item.quantity = (item.quantity || 1) + 1;
          }
          return item;
        });
        localStorage.setItem("cart", JSON.stringify(cartItems));
        console.log("Increased quantity for:", titleToUpdate, "New cart:", cartItems);
        displayCartItems();
      }

      // Xử lý giảm số lượng
      if (decreaseButton) {
        const titleToUpdate = decreaseButton.dataset.title;
        cartItems = cartItems.map(item => {
          if (item.title === titleToUpdate && (item.quantity || 1) > 1) {
            item.quantity = (item.quantity || 1) - 1;
          }
          return item;
        });
        localStorage.setItem("cart", JSON.stringify(cartItems));
        console.log("Decreased quantity for:", titleToUpdate, "New cart:", cartItems);
        displayCartItems();
      }
    });
  }


});




