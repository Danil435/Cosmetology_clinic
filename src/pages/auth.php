<?php
session_start();

if ($_POST) {
    // Авторизация пользователя
    if (isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['g-recaptcha-response'])) {
        $email = $_POST['email'];
        $password = $_POST['pass'];
        $recaptcha_response = $_POST['g-recaptcha-response'];
        // Функция для проверки reCAPTCHA через cURL
        function verifyRecaptcha($recaptcha_response) {
            $secret_key = '6LcPs_kpAAAAAH0g09WysybknpxhIZUeo7_9dJmJ';
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret' => $secret_key,
                'response' => $recaptcha_response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ];
            // Настройки cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем проверку SSL-сертификатов для отладки

            // Отправляем запрос и получаем ответ
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result === FALSE) {
                return false;
            }

            return json_decode($result, true);
        }

        // Проверяем reCAPTCHA
        $response_data = verifyRecaptcha($recaptcha_response);

        // Логируем результат проверки reCAPTCHA
        error_log("Ответ от reCAPTCHA: " . json_encode($response_data));

        if (isset($response_data['success']) && $response_data['success'] == true) {
            // Подключение к базе данных
            $db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');
            
            if (!$db) {
                // Логируем ошибку подключения к базе данных
                error_log("Ошибка подключения к базе данных: " . mysqli_connect_error());
                echo "<script>alert('Ошибка подключения к базе данных. Попробуйте позже.');</script>";
                exit;
            }

            // Подготовка запроса для проверки учетных данных
            $stmt = $db->prepare("SELECT name, phone, role FROM users WHERE email = ? AND password = ?");
            if ($stmt === false) {
                // Логируем ошибку подготовки запроса
                error_log("Ошибка подготовки запроса: " . mysqli_error($db));
                echo "<script>alert('Ошибка запроса. Попробуйте позже.');</script>";
                exit;
            }

            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            // Проверка наличия пользователя
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();

                // Сохранение данных пользователя в сессии
                $_SESSION['email'] = $email;
                $_SESSION['pass'] = $password;
                $_SESSION['name'] = $user_data['name'];
                $_SESSION['phone'] = $user_data['phone'];
                $_SESSION['role'] = $user_data['role'];
                if ($_SESSION['role'] == 1) {
                    // Переадресация для админа
                    header('Location: /src/pages/admin.html');
                }else{
                  header('Location: /src/pages/Profile.php');
                }

                echo "<script>alert('Вы успешно авторизовались!');
                </script>";
                
            } else {
                echo "<script>alert('Неверный логин или пароль. Попробуйте снова.');</script>";
            }
            $stmt->close();
        } else {
            // Логируем ошибки, если reCAPTCHA не прошла проверку
            if (isset($response_data['error-codes'])) {
                error_log("Ошибка reCAPTCHA: " . json_encode($response_data['error-codes']));
            }
            echo "<script>alert('Пройдите reCAPTCHA заново.');</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/src/scss/style.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Бархатные Щёчки</title>
</head>
<body>
    <section class="Auth Auth__container">
        <form class="Auth__container-search" action="" method="POST">
            <h2 class="Auth__container-subtitle">Войти в аккаунт</h2>
            <h3 class="Auth__container-search-subtext">Почта</h3>
            <input name="email" class="Auth__container-search-text" type="text" />
            <h3 class="Auth__container-search-subtext">Пароль</h3>
            <input name="pass" class="Auth__container-search-text" type="text" />
            <div class="g-recaptcha" data-sitekey="6LcPs_kpAAAAAEUzIJHh5v2Hr4PbUSgXWryGNToI"></div>
            <div class="Auth__container-search-entrance">
                <button class="Auth__container-search-entrance-btn">Войти</button>
                <a href="/src/pages/reg.php">
                    <p class="Auth__container-search-entrance-text">Не зарегистрированны?</p>
                </a> 
            </div>
        </form>
        <img class="Auth__container-img" src="/src/assets/images/Auth-img.svg">
    </section>
    <script>
        function onClick(e) {
            e.preventDefault(); 
            grecaptcha.enterprise.ready(async () => {
                const token = await grecaptcha.enterprise.execute(
                    '6LcPs_kpAAAAAEUzIJHh5v2Hr4PbUSgXWryGNToI', {
                        action: 'LOGIN'
                    }
                );
                document.querySelector('.enter-form').submit();
            });
        }
    </script>
    <script type="module" src="/src/js/main.js"></script>
</body>
</html>
