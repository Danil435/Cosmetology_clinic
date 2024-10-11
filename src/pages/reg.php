<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/src/scss/style.css" />
    <title>Бархатные Щёчки</title>
</head>
<body>
    <section class="Auth Auth__container">
        <form class="Auth__container-search" action="/src/server/register.php" method="POST">
            <h2 class="Auth__container-subtitle">Регистрация</h2>
            <h3 class="Auth__container-search-subtext">Имя</h3>
            <input
                class="Auth__container-search-text"
                type="text"
                name="name"
                required
            />
            <h3 class="Auth__container-search-subtext">Почта</h3>
            <input
                class="Auth__container-search-text"
                type="email"
                name="email"
                required
            />
            <h3 class="Auth__container-search-subtext">Номер телефона</h3>
            <input
                class="Auth__container-search-text"
                type="tel"
                name="phone"
                required
            />
            <h3 class="Auth__container-search-subtext">Пароль</h3>
            <input
                class="Auth__container-search-text"
                type="password"
                name="password"
                required
            />
            <h3 class="Auth__container-search-subtext">Подтверждение пароля</h3>
            <input
                class="Auth__container-search-text"
                type="password"
                name="confirm_password"
                required
            />

            <div class="Auth__container-search-entrance">
                <button type="submit" class="Auth__container-search-entrance-btn">Зарегистрироваться</button>
                <a href="/src/pages/auth.php"><p class="Auth__container-search-entrance-text">Уже есть аккаунт?</p></a>  
            </div>
        </form>
        <img class="Auth__container-img" src="/src/assets/images/Auth-img.svg">
    </section>
    <script type="module" src=""></script>
    <script type="module" src="/src/js/main.js"></script>
</body>
</html>