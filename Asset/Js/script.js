'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Biến cho cửa sổ pop-up, toast, menu, v.v.
  const modal = document.querySelector('[data-modal]');
  const modalCloseBtn = document.querySelector('[data-modal-close]');
  const modalCloseOverlay = document.querySelector('[data-modal-overlay]');
  const newsletterForm = document.querySelector('.newsletter form');
  const autoToast = document.querySelector('[data-toast-auto]');
  const autoToastCloseBtn = document.querySelector('[data-toast-close-auto]');
  const followToast = document.querySelector('[data-toast-follow]');
  const followToastCloseBtn = document.querySelector('[data-toast-close-follow]');
  const mobileMenuOpenBtn = document.querySelectorAll('[data-mobile-menu-open-btn]');
  const mobileMenu = document.querySelectorAll('[data-mobile-menu]');
  const mobileMenuCloseBtn = document.querySelectorAll('[data-mobile-menu-close-btn]');
  const overlay = document.querySelector('[data-overlay]');
  const accordionBtn = document.querySelectorAll('[data-accordion-btn]');
  const accordion = document.querySelectorAll('[data-accordion]');

  // Chức năng hiển thị/ẩn danh sách ngân hàng với hiệu ứng
  const paymentMethods = document.querySelectorAll('input[name="payment-method"]');
  const bankList = document.querySelector('.bank-list');

  paymentMethods.forEach(method => {
    method.addEventListener('change', function () {
      if (this.value === 'bank-card') {
        bankList.classList.add('active');
      } else {
        bankList.classList.remove('active');
      }
    });
  });

  // Khởi tạo giỏ hàng và danh sách yêu thích từ LocalStorage
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

  // Hàm đóng cửa sổ pop-up
  const modalCloseFunc = function () {
    if (modal) modal.classList.add('closed');
  };

  // Hàm hiển thị toast
  const showToast = (message) => {
    const toast = document.createElement('div');
    toast.classList.add('notification-toast', 'active');
    toast.innerHTML = `
      <button class="toast-close-btn" onclick="this.parentElement.remove()">
        <ion-icon name="close-outline"></ion-icon>
      </button>
      <div class="toast-detail">
        <p class="toast-message">${message}</p>
      </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  };

  // Hàm cập nhật số lượng giỏ hàng
  const updateCartCount = () => {
    const cartCountElements = document.querySelectorAll(
      '.header-user-actions .action-btn .count, .mobile-bottom-navigation .action-btn .count'
    );
    cartCountElements.forEach(element => {
      if (element.closest('a[href*="Payment.php"]')) {
        element.textContent = cart.length;
      }
    });
  };

  // Hàm cập nhật số lượng yêu thích
  const updateFavoriteCount = () => {
    const favoriteCountElements = document.querySelectorAll(
      '.header-user-actions .action-btn .count, .mobile-bottom-navigation .action-btn .count'
    );
    favoriteCountElements.forEach(element => {
      if (element.closest('a[href*="like.php"]')) {
        element.textContent = favorites.length;
      }
    });
  };

  // Hàm hiển thị sản phẩm yêu thích
  const displayFavorites = () => {
    const favoriteProducts = document.getElementById('favoriteProducts');
    const noFavorites = document.getElementById('noFavorites');
    if (!favoriteProducts || !noFavorites) return;

    if (favorites.length === 0) {
      noFavorites.style.display = 'block';
      favoriteProducts.innerHTML = '';
      return;
    }

    noFavorites.style.display = 'none';
    favoriteProducts.innerHTML = '';

    favorites.forEach(product => {
      const showcase = document.createElement('div');
      showcase.classList.add('showcase');
      const imgDefault = product.imgDefault || product.img || '';
      const imgHover = product.imgHover || imgDefault;
      const category = product.category || 'Không xác định';
      const price = product.price || '0k';
      const title = product.title || 'Sản phẩm không tên';

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
          <a href="#"><h3 class="showcase-title">${title}</h3></a>
          <div class="price-box"><p class="price">${price}</p></div>
        </div>
      `;
      favoriteProducts.appendChild(showcase);
    });
  };

  // Sự kiện cho pop-up, menu, v.v.
  if (modalCloseOverlay) modalCloseOverlay.addEventListener('click', modalCloseFunc);
  if (modalCloseBtn) modalCloseBtn.addEventListener('click', modalCloseFunc);
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (event) {
      event.preventDefault();
      modalCloseFunc();
      if (followToast) {
        followToast.classList.add('active');
        setTimeout(() => followToast.classList.remove('active'), 3000);
      }
    });
  }
  if (followToastCloseBtn) followToastCloseBtn.addEventListener('click', () => followToast.classList.remove('active'));
  if (autoToastCloseBtn) autoToastCloseBtn.addEventListener('click', () => {
    autoToast.classList.add('closed');
    autoToast.classList.remove('auto-toast');
  });
  for (let i = 0; i < mobileMenuOpenBtn.length; i++) {
    const mobileMenuCloseFunc = function () {
      mobileMenu[i].classList.remove('active');
      overlay.classList.remove('active');
    };
    mobileMenuOpenBtn[i].addEventListener('click', function () {
      mobileMenu[i].classList.add('active');
      overlay.classList.add('active');
    });
    mobileMenuCloseBtn[i].addEventListener('click', mobileMenuCloseFunc);
    overlay.addEventListener('click', mobileMenuCloseFunc);
  }
  for (let i = 0; i < accordionBtn.length; i++) {
    accordionBtn[i].addEventListener('click', function () {
      const clickedBtn = this.nextElementSibling.classList.contains('active');
      for (let j = 0; j < accordion.length; j++) {
        if (clickedBtn) break;
        if (accordion[j].classList.contains('active')) {
          accordion[j].classList.remove('active');
          accordionBtn[j].classList.remove('active');
        }
      }
      this.nextElementSibling.classList.toggle('active');
      this.classList.toggle('active');
    });
  }

  // Chức năng "tim sản phẩm" cho tất cả .showcase
  const heartButtons = document.querySelectorAll(
    '.btn-action ion-icon[name="heart-outline"], .btn-action ion-icon[name="heart"]'
  );
  heartButtons.forEach(button => {
    const product = button.closest('.showcase');
    if (!product) return;

    const productTitle = product.querySelector('.showcase-title')?.textContent.trim() || '';
    const isFavorite = favorites.some(fav => fav.title === productTitle);

    if (isFavorite) {
      button.setAttribute('name', 'heart');
      button.closest('.btn-action').style.color = 'var(--salmon-pink)';
    } else {
      button.setAttribute('name', 'heart-outline');
      button.closest('.btn-action').style.color = '';
    }

    button.addEventListener('click', function (event) {
      event.preventDefault();
      const currentProduct = this.closest('.showcase');
      if (!currentProduct) return;

      const currentTitle = currentProduct.querySelector('.showcase-title')?.textContent.trim() || '';
      const productImgDefault = currentProduct.querySelector('.product-img.default')?.src || currentProduct.querySelector('.showcase-img')?.src || '';
      const productImgHover = currentProduct.querySelector('.product-img.hover')?.src || productImgDefault;
      const productPrice = currentProduct.querySelector('.price')?.textContent.trim() || '';
      const productCategory = currentProduct.querySelector('.showcase-category')?.textContent.trim() || 'Ưu đãi trong ngày';
      const isCurrentlyFavorite = favorites.some(fav => fav.title === currentTitle);

      if (!isCurrentlyFavorite) {
        this.setAttribute('name', 'heart');
        this.closest('.btn-action').style.color = 'var(--salmon-pink)';
        favorites.push({
          imgDefault: productImgDefault,
          imgHover: productImgHover,
          title: currentTitle,
          category: productCategory,
          price: productPrice
        });
        showToast(`${currentTitle} đã được thêm vào yêu thích!`);
      } else {
        this.setAttribute('name', 'heart-outline');
        this.closest('.btn-action').style.color = '';
        favorites = favorites.filter(fav => fav.title !== currentTitle);
        showToast(`${currentTitle} đã được xóa khỏi yêu thích!`);
      }

      localStorage.setItem('favorites', JSON.stringify(favorites));
      updateFavoriteCount();
    });
  });

  // Chức năng "thêm vào giỏ hàng" cho .btn-action trong .showcase
  const addToCartButtons = document.querySelectorAll(
    '.btn-action ion-icon[name="bag-add-outline"]'
  );
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      const product = button.closest('.showcase');
      if (!product) return;

      const productTitle = product.querySelector('.showcase-title')?.textContent.trim() || '';
      const productImgDefault = product.querySelector('.product-img.default')?.src || product.querySelector('.showcase-img')?.src || '';
      const productImgHover = product.querySelector('.product-img.hover')?.src || productImgDefault;
      const productPrice = product.querySelector('.price')?.textContent.trim() || '';
      const productCategory = product.querySelector('.showcase-category')?.textContent.trim() || 'Ưu đãi trong ngày';

      if (!cart.some(item => item.title === productTitle)) {
        cart.push({
          imgDefault: productImgDefault,
          imgHover: productImgHover,
          title: productTitle,
          category: productCategory,
          price: productPrice,
          quantity: 1
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        showToast(`${productTitle} đã được thêm vào giỏ hàng!`);
      } else {
        showToast(`${productTitle} đã có trong giỏ hàng!`);
      }
    });
  });

  // Chức năng "Thêm vào giỏ hàng" cho .add-cart-btn trong .product-featured và các trang khác
  document.querySelectorAll('.add-cart-btn').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      const product = this.closest('.showcase');
      if (!product) return;

      const productTitle = button.dataset.title || product.querySelector('.showcase-title')?.textContent.trim() || '';
      const productImg = product.querySelector('.showcase-img')?.src || '';
      const productPrice = product.querySelector('.price')?.textContent.trim() || '';
      const productCategory = 'Ưu đãi trong ngày'; // Hoặc lấy từ dữ liệu khác nếu có

      if (!cart.some(item => item.title === productTitle)) {
        cart.push({
          imgDefault: productImg,
          imgHover: productImg,
          title: productTitle,
          category: productCategory,
          price: productPrice,
          quantity: 1
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        showToast(`${productTitle} đã được thêm vào giỏ hàng!`);
      } else {
        showToast(`${productTitle} đã có trong giỏ hàng!`);
      }
    });
  });

  // Chức năng "Thêm vào giỏ hàng" cho .add-to-cart-btn trong product-detail.php
  document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      const productInfo = this.closest('.product-info');
      if (!productInfo) return;

      const productTitle = productInfo.querySelector('.product-title')?.textContent.trim() || '';
      const productImg = document.querySelector('.main-image img')?.src || '';
      const productPrice = productInfo.querySelector('.price')?.textContent.trim() || '';
      const productCategory = productInfo.querySelector('.product-subtitle')?.textContent.trim() || 'Good Smile Company';

      if (!cart.some(item => item.title === productTitle)) {
        cart.push({
          imgDefault: productImg,
          imgHover: productImg,
          title: productTitle,
          category: productCategory,
          price: productPrice,
          quantity: parseInt(document.getElementById('quantity')?.value) || 1
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        showToast(`${productTitle} đã được thêm vào giỏ hàng!`);
      } else {
        showToast(`${productTitle} đã có trong giỏ hàng!`);
      }
    });
  });

  // Chức năng "Thích" cho .favorite-btn trong product-detail.php
  document.querySelectorAll('.favorite-btn').forEach(button => {
    const productInfo = button.closest('.product-info');
    if (productInfo) {
      const productTitle = productInfo.querySelector('.product-title')?.textContent.trim() || '';
      const isFavorite = favorites.some(fav => fav.title === productTitle);
      const heartIcon = button.querySelector('ion-icon');
      if (isFavorite) {
        heartIcon.setAttribute('name', 'heart');
        button.style.color = 'var(--salmon-pink)';
      } else {
        heartIcon.setAttribute('name', 'heart-outline');
        button.style.color = '';
      }
    }

    button.addEventListener('click', function(event) {
      event.preventDefault();
      const productInfo = this.closest('.product-info');
      if (!productInfo) return;

      const productTitle = productInfo.querySelector('.product-title')?.textContent.trim() || '';
      const productImg = document.querySelector('.main-image img')?.src || '';
      const productPrice = productInfo.querySelector('.price')?.textContent.trim() || '';
      const productCategory = productInfo.querySelector('.product-subtitle')?.textContent.trim() || 'Good Smile Company';

      const heartIcon = this.querySelector('ion-icon');
      const isCurrentlyFavorite = favorites.some(fav => fav.title === productTitle);

      if (!isCurrentlyFavorite) {
        heartIcon.setAttribute('name', 'heart');
        this.style.color = 'var(--salmon-pink)';
        favorites.push({
          imgDefault: productImg,
          imgHover: productImg,
          title: productTitle,
          category: productCategory,
          price: productPrice
        });
        showToast(`${productTitle} đã được thêm vào yêu thích!`);
      } else {
        heartIcon.setAttribute('name', 'heart-outline');
        this.style.color = '';
        favorites = favorites.filter(fav => fav.title !== productTitle);
        showToast(`${productTitle} đã được xóa khỏi yêu thích!`);
      }

      localStorage.setItem('favorites', JSON.stringify(favorites));
      updateFavoriteCount();
    });
  });

  // Chức năng "tim sản phẩm" trong #favoriteProducts
  const favoriteProducts = document.getElementById('favoriteProducts');
  if (favoriteProducts) {
    displayFavorites();
    favoriteProducts.addEventListener('click', function (e) {
      const heartButton = e.target.closest('.btn-action');
      if (heartButton && heartButton.querySelector('ion-icon[name="heart"]')) {
        const titleToRemove = heartButton.dataset.title;
        favorites = favorites.filter(fav => fav.title !== titleToRemove);
        localStorage.setItem('favorites', JSON.stringify(favorites));
        displayFavorites();
        updateFavoriteCount();
        showToast(`${titleToRemove} đã được xóa khỏi yêu thích!`);
      }
    });
  }



  // Cập nhật số lượng ban đầu
  updateCartCount();
  updateFavoriteCount();
});

function updateCountdown() {
  const countdownElements = document.querySelectorAll('.countdown');
  countdownElements.forEach(element => {
    const endTime = new Date(element.dataset.endTime).getTime();
    const now = new Date().getTime();
    const distance = endTime - now;

    if (distance < 0) {
      element.innerHTML = '<p class="countdown-expired">Ưu đãi đã kết thúc</p>';
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    element.querySelector('.days').textContent = days;
    element.querySelector('.hours').textContent = hours;
    element.querySelector('.minutes').textContent = minutes;
    element.querySelector('.seconds').textContent = seconds;
  });
}

document.addEventListener('DOMContentLoaded', function () {
  updateCountdown();
  setInterval(updateCountdown, 1000);
});

// Chức năng "Xem thêm" với tải động từ server
const loadMoreBtn = document.getElementById('loadMoreBtn');
if (loadMoreBtn) {
  loadMoreBtn.addEventListener('click', function () {
    const offset = parseInt(this.getAttribute('data-offset'));
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('offset', offset);

    fetch('product.php?' + urlParams.toString())
      .then(response => response.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newProducts = doc.querySelectorAll('.product-grid .showcase');
        const productGrid = document.getElementById('productGrid');

        newProducts.forEach(product => productGrid.appendChild(product.cloneNode(true)));

        const newOffset = doc.querySelector('#loadMoreBtn')?.getAttribute('data-offset');
        if (newOffset) {
          this.setAttribute('data-offset', newOffset);
        } else {
          this.remove();
        }

        // Cập nhật lại sự kiện cho các nút "Thích" và "Thêm vào giỏ hàng" trong sản phẩm mới
        updateProductActions();
      })
      .catch(error => console.error('Lỗi khi tải thêm sản phẩm:', error));
  });
}

// Hàm cập nhật sự kiện cho các nút trong sản phẩm mới
function updateProductActions() {
  // Cập nhật nút "Thích"
  const heartButtons = document.querySelectorAll(
    '.btn-action ion-icon[name="heart-outline"], .btn-action ion-icon[name="heart"]'
  );
  heartButtons.forEach(button => {
    const product = button.closest('.showcase');
    if (!product) return;

    const productTitle = product.querySelector('.showcase-title')?.textContent.trim() || '';
    const isFavorite = favorites.some(fav => fav.title === productTitle);

    if (isFavorite) {
      button.setAttribute('name', 'heart');
      button.closest('.btn-action').style.color = 'var(--salmon-pink)';
    } else {
      button.setAttribute('name', 'heart-outline');
      button.closest('.btn-action').style.color = '';
    }

    button.removeEventListener('click', handleHeartClick); // Xóa sự kiện cũ để tránh trùng lặp
    button.addEventListener('click', handleHeartClick);
  });

  // Cập nhật nút "Thêm vào giỏ hàng"
  const addToCartButtons = document.querySelectorAll('.btn-action ion-icon[name="bag-add-outline"]');
  addToCartButtons.forEach(button => {
    button.removeEventListener('click', handleAddToCartClick); // Xóa sự kiện cũ
    button.addEventListener('click', handleAddToCartClick);
  });
}

// Hàm xử lý sự kiện "Thích"
function handleHeartClick(event) {
  event.preventDefault();
  const currentProduct = this.closest('.showcase');
  if (!currentProduct) return;

  const currentTitle = currentProduct.querySelector('.showcase-title')?.textContent.trim() || '';
  const productImgDefault = currentProduct.querySelector('.product-img.default')?.src || '';
  const productImgHover = currentProduct.querySelector('.product-img.hover')?.src || productImgDefault;
  const productPrice = currentProduct.querySelector('.price')?.textContent.trim() || '';
  const productCategory = currentProduct.querySelector('.showcase-category')?.textContent.trim() || 'Ưu đãi trong ngày';
  const isCurrentlyFavorite = favorites.some(fav => fav.title === currentTitle);

  if (!isCurrentlyFavorite) {
    this.setAttribute('name', 'heart');
    this.closest('.btn-action').style.color = 'var(--salmon-pink)';
    favorites.push({
      imgDefault: productImgDefault,
      imgHover: productImgHover,
      title: currentTitle,
      category: productCategory,
      price: productPrice
    });
    showToast(`${currentTitle} đã được thêm vào yêu thích!`);
  } else {
    this.setAttribute('name', 'heart-outline');
    this.closest('.btn-action').style.color = '';
    favorites = favorites.filter(fav => fav.title !== currentTitle);
    showToast(`${currentTitle} đã được xóa khỏi yêu thích!`);
  }

  localStorage.setItem('favorites', JSON.stringify(favorites));
  updateFavoriteCount();
}

// Hàm xử lý sự kiện "Thêm vào giỏ hàng"
function handleAddToCartClick(event) {
  event.preventDefault();
  const product = this.closest('.showcase');
  if (!product) return;

  const productTitle = product.querySelector('.showcase-title')?.textContent.trim() || '';
  const productImgDefault = product.querySelector('.product-img.default')?.src || '';
  const productImgHover = product.querySelector('.product-img.hover')?.src || productImgDefault;
  const productPrice = product.querySelector('.price')?.textContent.trim() || '';
  const productCategory = product.querySelector('.showcase-category')?.textContent.trim() || 'Ưu đãi trong ngày';

  if (!cart.some(item => item.title === productTitle)) {
    cart.push({
      imgDefault: productImgDefault,
      imgHover: productImgHover,
      title: productTitle,
      category: productCategory,
      price: productPrice,
      quantity: 1
    });
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showToast(`${productTitle} đã được thêm vào giỏ hàng!`);
  } else {
    showToast(`${productTitle} đã có trong giỏ hàng!`);
  }
}

// Gọi hàm khởi tạo sự kiện cho các sản phẩm ban đầu
updateProductActions();
