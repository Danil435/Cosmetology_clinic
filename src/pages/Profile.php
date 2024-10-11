<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

// Проверка на ошибки подключения к базе данных
if ($db === false) {
    echo "<script>alert('Ошибка подключения к базе данных');</script>";
    exit(); // Прекращаем выполнение скрипта, так как база недоступна
}

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['email'])) {
    header('Location: /src/pages/auth.php');
    exit();
}

// Получение данных пользователя
$email = $_SESSION['email'];
$query = "SELECT name, phone FROM users WHERE email = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    echo "<script>alert('Пользователь не найден');</script>";
    exit();
}

// Получение истории заказов
$order_query = "SELECT order_id, total_price, status FROM orders WHERE user_id = ?";
$order_stmt = $db->prepare($order_query);
$order_stmt->bind_param("i", $_SESSION['user_id']); // Предполагается, что user_id хранится в сессии
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$_SESSION['is_authenticated'] = isset($_SESSION['email']) && isset($_SESSION['pass']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/src/scss/style.css" />
    <title>Бархатные Щёчки</title>
</head>
<body>
    <section class="header">
        <div class="container header__container">
            <nav class="header__nav">
                <div class="header__nav-logo">
                    <a class="logo nav-logo" href="/index.html">
                        <img src="/src/assets/icon/logo.svg" alt="Логотип">
                    </a>
                    <h3 class="header__nav-logo-subtext">Бархатные Щёчки</h3>
                </div>
                <ul class="header__nav-menu">
                    <li class="header__nav-item"><a href="/src/pages/services.html" class="header__nav-item-link">Услуги</a></li>
                    <li class="header__nav-item"><a href="/src/pages/Product.php" class="header__nav-item-link">Товар</a></li>
                    <li class="header__nav-item"><a href="/src/pages/AboutUs.html" class="header__nav-item-link">О нас</a></li>
                </ul>
                <div class="header__nav-prof">
                    <a href="/src/pages/Profile.php"><img class="header__nav-prof-item" src="/src/assets/icon/profil.svg" alt="Профиль"></a> 
                    <a href="/src/pages/Corzina.html"><img class="header__nav-prof-item" src="/src/assets/icon/corzina.svg" alt="Корзина"></a>   
                </div>
            </nav>
        </div>
    </section>

    <section class="Profile Profile__container">
        <h1 class="Profile__container-title">Профиль</h1>
        <div class="Profile__container-info">
            <div class="Profile__container-person">
                <h2 class="Profile__container-subtitle"><?php echo htmlspecialchars($user_data['name']); ?></h2>
                <h3 class="Profile__container-subtext">Номер телефона: <?php echo htmlspecialchars($user_data['phone']); ?></h3>
            </div>

            <div class="promo__container-gmail">
                <h2 class="Profile__container-subtitle">Почта:</h2>
                <h3 class="Profile__container-subtext"><?php echo htmlspecialchars($email); ?></h3>
                <?php if ($_SESSION['is_authenticated']) {
							echo '<li> <a class="  navLinkExit" href="#" onclick="logout()">Выход</a> </li>';
                } ?>

            </div>
        </div>

    </section>

    <section class="Order Orders__container">
        <h2 class="Orders__container-subtitle">История заказов:</h2>

        <?php while ($order = $order_result->fetch_assoc()): ?>
            <div class="Orders__container-block">
                <img class="Orders__container-block-img" src="/src/assets/images/order-img.svg" alt="Заказ">
                <h3 class="Orders__container-block-subtext"><?php echo htmlspecialchars($order['total_price']); ?>р.</h3>
                <h3 class="Orders__container-block-subtext">Номер заказа: <?php echo htmlspecialchars($order['order_id']); ?></h3>
                <h3 class="Orders__container-block-subtext">Статус заказа: <?php echo htmlspecialchars($order['status']); ?></h3>
            </div>
        <?php endwhile; ?>
        
        <?php if ($order_result->num_rows === 0): ?>
            <p>У вас нет истории заказов.</p>
        <?php endif; ?>
    </section>

    <footer class="footer footer__container">
        <nav class="footer__nav">
            <div class="footer__nav-logo">
                <a class="logo nav-logo" href="/index.html">
                    <img src="/src/assets/icon/logo.svg" alt="Логотип">
                </a>
                <h3 class="footer-subtext">Бархатные Щёчки</h3>
            </div>
            <ul class="footer__nav-menu">
                <li class="footer__nav-item"><a href="/src/pages/services.html" class="footer__nav-item-link">Услуги</a></li>
                <li class="footer__nav-item"><a href="/src/pages/Product.php" class="footer__nav-item-link">Товар</a></li>
                <li class="footer__nav-item"><a href="/src/pages/AboutUs.html" class="footer__nav-item-link">О нас</a></li>
            </ul>
            <div class="footer__nav-prof">
                <img class="footer__nav-prof-item" src="/src/assets/icon/VK.png" alt="VK">
                <img class="footer__nav-prof-item" src="/src/assets/icon/youtube.png" alt="YouTube">
                <img class="footer__nav-prof-item" src="/src/assets/icon/tg.png" alt="Telegram">
            </div>
        </nav>
    </footer>
    <script>
		function logout() {
			// удалить куки сессии
			document.cookie = 'PHPSESSID=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

			// обновить страницу после завершения сессии
			window.location.href = 'http://localhost:8000/src/pages/auth.php';
		}
	</script>
    <script type="module" src="/src/js/main.js"></script>
</body>
</html>

<?php
$stmt->close();
$order_stmt->close();
mysqli_close($db);
?>