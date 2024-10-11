<?php
session_start();

// Подключение к базе данных
$host = 'localhost';
$dbname = 'cosmetology_clinic';
$username = 'root';
$password = '';

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

// Функция для очистки данных
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $captcha_input = sanitize_input($_POST['captcha']);

    // Проверка CAPTCHA
    if ($captcha_input !== $_SESSION['captcha']) {
        echo "Неверный код CAPTCHA!";
        exit();
    }

    // Проверка данных пользователя
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    
    if ($user && password_verify($password, $user['password'])) {
        // Авторизация успешна
        echo "Вы успешно вошли в аккаунт!";
        // Перенаправление на главную страницу или в профиль
        header("Location: /profile.php");
        exit();
    } else {
        echo "Неверная почта или пароль!";
    }
}
?>
