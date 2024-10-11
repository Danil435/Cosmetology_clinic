<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');
// Проверка на ошибки подключения к базе данных
if ($db == false) {
  echo "<script>alert('Ошибка подключения к базе данных');</script>";
  exit(); // Прекращаем выполнение скрипта, так как база недоступна
}
$products = mysqli_query($db, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/src/scss/style.css" />
    <title>Бархатные Щёчки</title>
  </head>
  <body>
    <div class="body-wraper">
      <header class="header__container">
  <nav class="header__nav" id="menu">
      <div class="header__nav-logo">
    <a class="logo nav-logo" href="/index.html">
      <img src="/src/assets/icon/logo.svg">
    </a>
    <a href="/index.html" class="header__nav-logo-subtext">Бархатные Щёчки</a>
      </div>
      
      <button class="header__burger-btn" id="burger">
        <span></span><span></span><span></span>
      </button>

      <ul id="menu" class="header__nav-menu">
        
        <li class="header__nav-item">
          <div class="header__nav-prof">
           <a href="/src/pages/Profile.php"><img class="header__nav-prof-item" src="/src/assets/icon/profil.svg"></a> 
           <a href="/src/pages/Corzina.html"><img class="header__nav-prof-item" src="/src/assets/icon/corzina.svg"></a> 
          </div>
        </li>

        <li class="header__nav-item"><a href="/src/pages/services.html" class="header__nav-item-link">Услуги</a></li>
        <li class="header__nav-item"><a href="/src/pages/Product.php" class="header__nav-item-link">Товар</a></li>
        <li class="header__nav-item"><a href="/src/pages/AboutUs.html" class="header__nav-item-link">О нас</a></li>
        
      </ul>

  </nav>

    </header>
    <section class="Product Product__container">
      <h1 class="Product__container-title">Товары</h1>
        <div class="Product__container-cards">
        <?php
        while ($card = mysqli_fetch_assoc($products)) {
          
            // выводим все карточки, если кнопки не нажаты
        ?>
        <div class="Product__container-card">
            <img class="Product__container-card-img" src="/src/assets/images/<?php echo $card['img'] ?>">
            <h3 class="Product__container-card-subtext">
              <?php
               echo $card['name'] 
            ?></h3>
            <div class="Product__container-card-cena">
            <p class="Product__container-card-text">
            <?php
               echo $card['cost'] . '₽';
            ?>
            </p>
            <img class="" src="/src/assets/icon/corzina.svg" >
            </div>
        </div>
        
        <?php
        }
        ?>
        
    </div>
    </section>

    <footer class="footer footer__container">
        <nav class="footer__nav">
          <div class="footer__nav-logo">
        <a class="logo nav-logo" href="/index.html">
          <img src="/src/assets/icon/logo.svg">
        </a>
        <h3 class="footer-subtext">Бархатные Щёчки</h3>
          </div>
          <ul class="footer__nav-menu">
            <li class="footer__nav-item"><a href="/src/pages/services.html" class="footer__nav-item-link">Услуги</a></li>
            <li class="footer__nav-item"><a href="/src/pages/Product.php" class="footer__nav-item-link">Товар</a></li>
            <li class="footer__nav-item"><a href="/src/pages/AboutUs.html" class="footer__nav-item-link">О нас</a></li>
          </ul>
          <div class="footer__nav-prof">
            <img class="footer__nav-prof-item" src="/src/assets/icon/VK.png">
            <img class="footer__nav-prof-item" src="/src/assets/icon/youtube.png">
            <img class="footer__nav-prof-item" src="/src/assets/icon/tg.png">
          </div>
      </nav>
      </footer>
    <script type="module" src="/src/js/main.js"></script>
    <script type="module" defer src="/src/js/burger.js"></script>
  </body>
</html>
