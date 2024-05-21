'use strict';



/**
 * navbar toggle
 */

const navbar = document.querySelector("[data-navbar]");
const navbarLinks = document.querySelectorAll("[data-nav-link]");
const menuToggleBtn = document.querySelector("[data-menu-toggle-btn]");

menuToggleBtn.addEventListener("click", function () {
  navbar.classList.toggle("active");
  this.classList.toggle("active");
});

for (let i = 0; i < navbarLinks.length; i++) {
  navbarLinks[i].addEventListener("click", function () {
    navbar.classList.toggle("active");
    menuToggleBtn.classList.toggle("active");
  });
}



/**
 * header sticky & back to top
 */

const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");

window.addEventListener("scroll", function () {
  if (window.scrollY >= 100) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
});

const filterButtons = document.querySelectorAll('.filter-btn');

function handleButtonClick(event) {
  console.log('Button clicked:', event.target.textContent);
  filterButtons.forEach(button => button.classList.remove('active'));
  event.target.classList.add('active');
}

filterButtons.forEach(button => {
  button.addEventListener('click', handleButtonClick);
});

function addToCart(itemName, itemPrice, event) {
  // Update cart item count in localStorage
  let cartItemCount = localStorage.getItem('cartItemCount') ? parseInt(localStorage.getItem('cartItemCount')) : 0;
  cartItemCount++;
  localStorage.setItem('cartItemCount', cartItemCount);
  document.getElementById('cart-button').innerText = `Cart Items: ${cartItemCount}`;

  // Update cart items in localStorage
  let cartItems = localStorage.getItem('cartItems') ? JSON.parse(localStorage.getItem('cartItems')) : [];
  cartItems.push({ name: itemName, price: itemPrice });
  localStorage.setItem('cartItems', JSON.stringify(cartItems));

  // Send item to the server
  fetch('add_to_cart.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({ name: itemName, price: itemPrice })
  }).then(response => response.text())
    .then(data => {
      console.log('Item added to cart server-side');
    });
  }



