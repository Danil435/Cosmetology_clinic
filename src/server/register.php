<?php
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
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    // Проверка пароля
    if ($password !== $confirm_password) {
        echo "Пароли не совпадают!";
        exit();
    }

    // Хэширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Проверка, существует ли уже такой пользователь по email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Пользователь с такой почтой уже существует!";
        exit();
    }

    // Вставляем данные в базу данных
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, 0)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "Регистрация прошла успешно!";
        // После успешной регистрации можно перенаправить пользователя
        header("Location: /src/pages/auth.php");
        exit();
    } else {
        echo "Ошибка при регистрации. Попробуйте еще раз.";
    }
}
?>
