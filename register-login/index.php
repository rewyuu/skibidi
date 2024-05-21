<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}
include('database.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jerry's Pares and Bulalohan</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Rubik:wght@400;500;600;700&family=Shadows+Into+Light&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="./images/hero-banner.png" media="min-width(768px)">
    <link rel="preload" as="image" href="./images/hero-banner-bg.png" media="min-width(768px)">
    <link rel="preload" as="image" href="./images/hero-bg.jpg">
    <link rel="icon" href="/images/jerrys-logos.png">
</head>
<body id="top">
    
<style>
    .cart-message {
        visibility: hidden;
        font-weight: 700;
        color: white;
        background-color: var(--dark-orange);
        border-radius: 10px;
        margin: 10px;
        padding: 5px;
    }

    .selectButtons {
      background-color: var(--dark-orange);
      width: auto;
      height: 30px;
      border-radius: 5px;
      margin: 10px;
      padding: 10px;
      padding-top: 3px;
    }

    .selectButtons:hover {
      background-color: orange;
    }

    .food-menu-card {
      display:none; 
    }
    .food-menu-card.active {
      display: block;
    }
</style>

<header class="header active" data-header>
  <div class="container">

    <h1>
      <a href="#" class="logo">Jerry's Pares and Bulalohan<span class="span">.</span></a>
    </h1>

    <nav class="navbar" data-navbar>
      <ul class="navbar-list">

        <li class="nav-item">
          <a href="#home" class="navbar-link" data-nav-link>Home</a>
        </li>

        <li class="nav-item">
          <a href="#food-menu" class="navbar-link" data-nav-link>Menu</a>
        </li>

        <li class="nav-item">
          <a href="#aboutus" class="navbar-link" data-nav-link>About us</a>
        </li>

        <li class="nav-item">
          <a href="#contactus" class="navbar-link" data-nav-link>Contact Us</a>
        </li>

        <li class="nav-item">
          <a href="order.php" class="navbar-link" data-nav-link>Orders</a>
        </li>

      </ul>
    </nav>

    <div class="header-btn-group">
    <button id="cart-button" class="btn btn-hover" onclick="redirectToCart()">Cart Items: 0</button>

    <button class="btn btn-hover" onclick="redirectToLogout()">Logout</button>

      <script>

    function redirectToCart() {
        window.location.href = "cart.php";
    }

    function redirectToLogout() {
        localStorage.removeItem('cartItemCount');
        localStorage.removeItem('cartItems');
        window.location.href = "logout.php"; 
    }

    function addToCart(itemName, itemPrice) {
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    let newItem = { id: new Date().getTime(), name: itemName, price: itemPrice };

    cartItems.push(newItem);
    localStorage.setItem('cartItems', JSON.stringify(cartItems));

    fetch('addToCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ itemName: itemName, itemPrice: itemPrice }),
    })
    .then(response => response.json())
    .then(data => {
        console.log('Item added to database:', data);
        updateCartCount();
    })
    .catch(error => {
        console.error('Error adding item to database:', error);
    });

    let cartMessage = event.target.closest('.food-menu-card').querySelector('.cart-message');
    cartMessage.innerText = "Item added to cart!";
    cartMessage.style.visibility = 'visible';
    setTimeout(function() {
        cartMessage.style.visibility = 'hidden';
    }, 2000);
}

function updateCartCount() {
    fetch('getCartCount.php')
    .then(response => response.json())
    .then(data => {
        let cartButton = document.getElementById('cart-button');
        if (cartButton) {
            cartButton.textContent = `Cart Items: ${data.cart_count}`;
        } else {
            console.error('Cart button element not found.');
        }
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    updateCartCount();
});

// for the color for every button u click it change
 const filterButtons = document.querySelectorAll('.filter-btn');

function handleButtonClick(event) {
  console.log('Button clicked:', event.target.textContent);
  filterButtons.forEach(button => button.classList.remove('active'));
  event.target.classList.add('active');
}

filterButtons.forEach(button => {
  button.addEventListener('click', handleButtonClick);
});

// for the food menu
document.addEventListener('DOMContentLoaded', function () {
  const filterButtons = document.querySelectorAll('.filter-btn');
  const foodMenuCards = document.querySelectorAll('.food-menu-card');

foodMenuCards.forEach(card => {
  card.classList.add('active');
});

filterButtons.forEach(button => {
  button.addEventListener('click', function () {
  const category = button.getAttribute('data-category');

  filterButtons.forEach(btn => btn.classList.remove('active'));
  button.classList.add('active');

  foodMenuCards.forEach(card => {
    if (category === 'all' || card.getAttribute('data-category') === category) {
      card.classList.add('active'); 
    } else {
      card.classList.remove('active'); 
    }
  });
});
});
});
</script>

      <button class="nav-toggle-btn" aria-label="Toggle Menu" data-menu-toggle-btn>
        <span class="line top"></span>
        <span class="line middle"></span>
        <span class="line bottom"></span>
      </button>
    </div>
  </div>
</header>
    
    <main> 
        <article>
            <!--   First Section   -->
            <section class="hero" id="home" style="background-image: url('images/hero-bg.jpg')">
                <div class="container">
        
                  <div class="hero-content">
        
                    <p class="hero-subtitle">Eat Sleep And Repeat</p>
        
                    <h2 class="h1 hero-title">Jerry's "The Original Pares"</h2>
        
                    <p class="hero-text">Beef pares is a Filipino raised beef stew with garlic fried rice, and a bowl of clear soup. It is a popular food particularly associated with specialty roadside diner-style establishments known as paresan</p>
        
                    <button class="btn" onclick="scrollToFoodMenu()">Add to Cart</button>
        
                  </div>
        
                  <figure class="hero-banner">
                    <img src="./images/hero-banner-bg.png" width="820" height="716" alt="" aria-hidden="true"
                      class="w-100 hero-img-bg">
        
                    <img src="./images/hero-banner.png  " width="700" height="637" loading="lazy" alt="pares"
                      class="w-100 hero-img">
                  </figure>
        
                </div>
              </section>

            <!--   Second Section  -->
            <section class="section section-divider white promo">
                <div class="container">

                    <!--  first picture   -->

                    <ul class="promo-list has-scrollbar">
          
                      <li class="promo-item">
                        <div class="promo-card">

                 <h3 class="h3 card-title">Bulalo</h3>            
                <p class="card-text">
                    Savor the succulent goodness of our Bulalo, tender beef shanks, simmered to perfection in a rich, savory broth. A hearty delight that promises to satisfy your cravings and keep you coming back for more!
                </p>

                <img src="./images/promo-1.png" width="300" height="300" loading="lazy" alt="bulalo"
                  class="w-100 card-banner">
              </div>
              </li>

              <!--    second picture    -->

              <li class="promo-item">
                <div class="promo-card">
  
                  <h3 class="h3 card-title">Menudo</h3>
                  <p class="card-text">
                    Discover the irresistible flavors of our Menudo, A hearty blend of tender pork, potatoes, carrots, and bell peppers, simmered in a savory tomato sauce
                  </p>
  
                  <img src="./images/promo-2.png" width="300" height="300" loading="lazy" alt="Soft Drinks"
                    class="w-100 card-banner">
                </div>
              </li>

              <!--    third picture    -->

              <li class="promo-item">
                <div class="promo-card">
  
                  <h3 class="h3 card-title">Chicken Curry</h3>
                  <p class="card-text">
                    Experience the exotic allure of our Chicken Curry, Tender chicken pieces cooked in a fragrant blend of spices and creamy coconut milk. A flavorful journey that promises to spice up your dining experience.
                  </p>
  
                  <img src="./images/promo-3.png" width="300" height="300" loading="lazy" alt="French Fry"
                    class="w-100 card-banner">
                </div>
              </li>
                
              <!--    fourth picture    -->

                    <li class="promo-item">
                        <div class="promo-card">
          
                          <h3 class="h3 card-title">Dinakdakan</h3>
          
                          <p class="card-text">
                            Indulge in the bold flavors of our Dinakdakan, Grilled pork cheeks and liver, mixed with onions, chili peppers, and calamansi juice. A mouthwatering Filipino delicacy that packs a punch and will keep you coming back for another bite.
                          </p>

                          <img src="./images/promo-4.png" width="300" height="300" loading="lazy" alt="Chicken Masala"
                            class="w-100 card-banner">
                        </div>
                      </li>
                </div>
              </li>
            </ul>
            </section>

            <!--   Third Section   -->
            <section class="section food-menu" id="food-menu">
                <br> 
                <div class="container">
                    <p class="section-subtitle">Jerry's Dishes</p>

          <h2 class="h2 section-title">
            Our Delicious <span class="span">Meals</span>
          </h2>

          <p class="section-text">
            Crafted with care and passion in our local kitchen, using only the freshest ingredients.
          </p>

          <ul class="fiter-list">
            <li>
              <button class="filter-btn  active" data-category="all">All</button>
            </li>
            <li>
              <button class="filter-btn" data-category="dishes" >Dishes</button>
            </li>
            <li>
              <button class="filter-btn" data-category="add-ons">Add-ons</button>
            </li>
            <li>
              <button class="filter-btn" data-category="drinks">Drinks</button>
            </li>
          </ul>

          <ul class="food-menu-list">
            <li>
            <div class="food-menu-card" data-category="dishes">

                <div class="card-banner">
                  <img src="./images/food-menu-1.jpg" width="300" height="300" loading="lazy"
                    alt="bulalo" class="w-100">

                    <button class="btn food-menu-btn" onclick="addToCart('Bulalo', 130.00)">Add to Cart</button>=
                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Bulalo</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="130.00">P130.00</data>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">

                <div class="card-banner">
                  <img src="./images/food-menu-2.jpg" width="300" height="300" loading="lazy"
                    alt="pares" class="w-100">
                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Pares W/ Rice</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="60.00">P60.00</data>
                  <button class="selectButtons" onclick="addToCart('Pares W/ Rice', 60)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                <h3 class="h3 card-title">Pares W/o Rice</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="50.00">P50.00</data>
                  <button class="selectButtons" onclick="addToCart('Pares W/o Rice', 50)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                <h3 class="h3 card-title">Pares Tendon W/ Rice</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="80.00">P80.00</data>
                  <button class="selectButtons" onclick="addToCart('Pares Tendon W/ Rice', 80)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                <h3 class="h3 card-title">Pares Mami</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="40.00">P40.00</data>
                  <button class="selectButtons" onclick="addToCart('Pares Mami', 40)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-3.jpg" width="300" height="300" loading="lazy"
                      alt="lapazbatchoy" class="w-100">
  
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Lapaz Batchoy</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="50.00">P50.00</data>
                    <button class="selectButtons" onclick="addToCart('Lapaz Batchoy', 50)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                  <h3 class="h3 card-title">Lapaz Batchoy Special </h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <button class="selectButtons" onclick="addToCart('Lapaz Batchoy Special', 60)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                </div>
              </li>

            <li>
            <div class="food-menu-card" data-category="dishes">

                <div class="card-banner">
                  <img src="./images/food-menu-4.jpg" width="300" height="300" loading="lazy"
                    alt="lugaw" class="w-100">

                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Lugaw W/ Laman</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="35.00 ">P35.00</data>
                  <button class="selectButtons" onclick="addToCart('Lugaw W/ Laman', 35)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                <h3 class="h3 card-title">Lugaw W/ Egg</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="30.00 ">P30.00</data>
                  <button class="selectButtons" onclick="addToCart('Lugaw W/ Egg', 30)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                <h3 class="h3 card-title">Lugaw Plain</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="20.00 ">P20.00</data>
                  <button class="selectButtons" onclick="addToCart('Lugaw Plain', 20)">Select</button>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">

                <div class="card-banner">
                  <img src="./images/food-menu-5.jpg" width="300" height="300" loading="lazy"
                    alt="caldereta" class="w-100">

                    <button class="btn food-menu-btn" onclick="addToCart('Caldereta', 70.00)">Add to Cart</button>
                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Caldereta</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="70.00">P70.00</data>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">

                <div class="card-banner">
                  <img src="./images/food-menu-6.jpg" width="300" height="300" loading="lazy"
                    alt="igado" class="w-100">

                  <button class="btn food-menu-btn" onclick="addToCart('Igado', 70.00)">Add to Cart</button>
                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Igado</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="70.00">P70.00</data>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-7.jpg" width="300" height="300" loading="lazy"
                      alt="adobo" class="w-100">
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Adobong Baboy</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="70.00">P70.00</data>
                    <button class="selectButtons" onclick="addToCart('Adobong Baboy', 70)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">Adobong Manok</h3>

                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <button class="selectButtons" onclick="addToCart('Adobong Manok', 60)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-8.jpg" width="300" height="300" loading="lazy"
                      alt="bopis" class="w-100">
  
                    <button class="btn food-menu-btn" onclick="addToCart('Bopis', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Bopis</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-10.jpg" width="300" height="300" loading="lazy"
                      alt="menudo" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Menudo', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Menudo</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-11.jpg" width="300" height="300" loading="lazy"
                      alt="dinuguan" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Dinuguan', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Dinuguan</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>
            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-17.jpeg" width="300" height="300" loading="lazy"
                      alt="silogs" class="w-100">
  
                  </div>
  
                  <div class="wrapper">
                    <p class="category">Silogs</p>
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Tapsilog</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00 ">P80.00</data>
                    <button class="selectButtons" onclick="addToCart('Tapsilog', 80)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">PorkSilog</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00 ">P80.00</data>
                    <button class="selectButtons" onclick="addToCart('PorkSilog', 80)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">Chicksilog</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00 ">P80.00</data>
                    <button class="selectButtons" onclick="addToCart('ChickSilog', 80)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

                  <h3 class="h3 card-title">Cornsilog</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00 ">P80.00</data>
                    <button class="selectButtons" onclick="addToCart('CornSilog', 80)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">Hotsilog</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00 ">P60.00</data>
                    <button class="selectButtons" onclick="addToCart('HotSilog', 80)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-18.jpeg" width="300" height="300" loading="lazy"
                      alt="dinakdakan" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Dinakdakan', 80.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Dinakdakan</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00">P80.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-19.JPG" width="300" height="300" loading="lazy"
                      alt="papaitan" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Papaitan', 80.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Papaitan</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="80.00">P80.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="dishes">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-20.jpeg" width="300" height="300" loading="lazy"
                      alt="balbacua" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Balbacua', '120.00')">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Balbacua</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="120.00">P120.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-9.jpg" width="300" height="300" loading="lazy"
                      alt="isaw" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Isaw', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Isaw</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-12.jpg" width="300" height="300" loading="lazy"
                      alt="chickenskin" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Chicken Skin', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Chicken Skin</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-13.jpg" width="300" height="300" loading="lazy"
                      alt="chicharon" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Chicharon', 60.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Chicharon</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="60.00">P60.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-14.jpg" width="300" height="300" loading="lazy"
                      alt="tokwa" class="w-100">
  
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Tokwa</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="20.00">P20.00</data>
                    <button class="selectButtons" onclick="addToCart('Tokwa', 20)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">Tokwa't Baboy</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="30.00">P30.00</data>
                    <button class="selectButtons" onclick="addToCart('Tokwat Baboy', 30)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-15.jpeg" width="300" height="300" loading="lazy"
                      alt="rice" class="w-100">
  
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Plain Rice</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="12.00">P12.00</data>
                    <button class="selectButtons" onclick="addToCart('Plain Rice', 12)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>

                  <h3 class="h3 card-title">Fried Rice</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="15.00">P15.00</data>
                    <button class="selectButtons" onclick="addToCart('Fried Rice', 15)">Select</button>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-16.jpeg" width="300" height="300" loading="lazy"
                      alt="egg" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Egg', 12.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Egg</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="12.00">P12.00</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            <li>
            <div class="food-menu-card" data-category="add-ons">
  
                  <div class="card-banner">
                    <img src="./images/food-menu-sabaw.jpg" width="300" height="300" loading="lazy"
                      alt="egg" class="w-100">
  
                      <button class="btn food-menu-btn" onclick="addToCart('Egg', 12.00)">Add to Cart</button>
                  </div>
  
                  <div class="wrapper">
  
                    <div class="rating-wrapper">
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                      <ion-icon name="star"></ion-icon>
                    </div>
                  </div>
  
                  <h3 class="h3 card-title">Sabaw</h3>
  
                  <div class="price-wrapper">
  
                    <p class="price-text">Price:</p>
  
                    <data class="price" value="0">Free</data>
                    <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                  </div>
  
                </div>
            </li>

            

            <li>
            <div class="food-menu-card" data-category="drinks">

                <div class="card-banner">
                  <img src="./images/food-menu-21.jpg" width="300" height="300" loading="lazy"
                    alt="water" class="w-100">

                    <button class="btn food-menu-btn" onclick="addToCart('Water', 15.00)">Add to Cart</button>
                </div>

                <div class="wrapper">

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>

                <h3 class="h3 card-title">Water</h3>

                <div class="price-wrapper">

                  <p class="price-text">Price:</p>

                  <data class="price" value="15.00">P15.00</data>
                  <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
                </div>

              </div>
          </li>

          <li>
          <div class="food-menu-card" data-category="drinks">

              <div class="card-banner">
                <img src="./images/food-menu-22.png" width="300" height="300" loading="lazy"
                  alt="coke" class="w-100">

                  <button class="btn food-menu-btn" onclick="addToCart('Coke', 25.00)">Add to Cart</button>
              </div>

              <div class="wrapper">

                <div class="rating-wrapper">
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                  <ion-icon name="star"></ion-icon>
                </div>
              </div>

              <h3 class="h3 card-title">Coke</h3>

              <div class="price-wrapper">

                <p class="price-text">Price:</p>

                <data class="price" value="25.00">P25.00</data>
                <div class="cart-message">
                    <p>Item added to Cart</p>
                  </div>
              </div>

            </div>
        </li>

        <li>
        <div class="food-menu-card" data-category="drinks">

            <div class="card-banner">
              <img src="./images/food-menu-23.jpeg" width="300" height="300" loading="lazy"
                alt="mtndew" class="w-100">

                <button class="btn food-menu-btn" onclick="addToCart('Mountain Dew', 25.00)">Add to Cart</button>
            </div>

            <div class="wrapper">

              <div class="rating-wrapper">
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
                <ion-icon name="star"></ion-icon>
              </div>
            </div>

            <h3 class="h3 card-title">Mountain Dew</h3>

            <div class="price-wrapper">

              <p class="price-text">Price:</p>

              <data class="price" value="25.00">P25.00</data>
              <div class="cart-message">
                  <p>Item added to Cart</p>
                 </div>
            </div>

          </div>
      </li>

      <li>
      <div class="food-menu-card" data-category="drinks">

          <div class="card-banner">
            <img src="./images/food-menu-24.png" width="300" height="300" loading="lazy"
              alt="pepsi" class="w-100">

              <button class="btn food-menu-btn" onclick="addToCart('Pepsi', 25.00)">Add to Cart</button>
          </div>

          <div class="wrapper">

            <div class="rating-wrapper">
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
              <ion-icon name="star"></ion-icon>
            </div>
          </div>

          <h3 class="h3 card-title">Pepsi</h3>

          <div class="price-wrapper">

            <p class="price-text">Price:</p>

            <data class="price" value="25.00">P25.00</data>
            <div class="cart-message">
              <p>Item added to Cart</p>
              </div>
          </div>

        </div>
    </li>

    <li>
    <div class="food-menu-card" data-category="drinks">

        <div class="card-banner">
          <img src="./images/food-menu-25.png" width="300" height="300" loading="lazy"
            alt="royal" class="w-100">

            <button class="btn food-menu-btn" onclick="addToCart('Royal', 25.00)">Add to Cart</button>
        </div>

        <div class="wrapper">

          <div class="rating-wrapper">
            <ion-icon name="star"></ion-icon>
            <ion-icon name="star"></ion-icon>
            <ion-icon name="star"></ion-icon>
            <ion-icon name="star"></ion-icon>
            <ion-icon name="star"></ion-icon>
          </div>
        </div>

        <h3 class="h3 card-title">Royal</h3>

        <div class="price-wrapper">

          <p class="price-text">Price:</p>

          <data class="price" value="25.00">P25.00</data>
          <div class="cart-message">
            <p>Item added to Cart</p>
            </div>
        </div>

      </div>
  </li>
          </ul>

        </div>
      </section>

      <section class="hero" id="aboutus" style="background-image: url('images/hero-bg.jpg')">
        
        <div class="container">

          <div class="hero-content">

            <p class="hero-subtitle">ABOUT US</p>

            <h2 class="h1 hero-title">Jerry's Pares and Bulalohan</h2>

            <p class="hero-text">Jerry's Pares is a small, no-frills eatery in Makati. We have a wide range of dishes to offer. From its bestseller foods: Pares, Bulalo and Dinakdakan to the usual silogs, fried chicken and chicharon can be found here.</p>     

          </div>

          <figure class="hero-banner">
            <img src="./images/hero-banner-bg.png" width="820" height="716" alt="" aria-hidden="true"
              class="w-100 hero-img-bg">
          </figure>

        </div>

      </section>

    <section id="contactus" class="contact-section">
        <h2 class="h3 card-title">Contact Us</h2>
        <address class="h3 card-title">
          <p >Visit Us: 131A 13th Avenue, East Rembo, Makati, Philippines</p>
          <p>Call Us: 0967 337 6890</p>
          <p>Email Us: jfagotoandmamihaus@gmail.com</p>
          <ul class="social-icons">
            <li><p>Our Facebook Page <a href="https://www.facebook.com/ILoveJerrysPares" target="_blank"><i class="fab fa-facebook"></i></a></p></li>
          </ul>
        </address>
      </section>      
      
        </article>

    </main>

    <script >

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
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>