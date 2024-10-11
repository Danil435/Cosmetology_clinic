<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_users = $db->prepare("SELECT * FROM users");
$stmt_users->execute();
$result_users = $stmt_users->get_result();

// Create arrays to store the data
$data_users = array();

while ($row = $result_users->fetch_assoc()) {
	$data_users[] = $row;
}

// Handle edit form submission
if (isset($_POST['id']) || isset($_POST['name']) || isset($_POST['phone']) || isset($_POST['email']) || isset($_POST['password']) || isset($_POST['role'])) {
	$id = $_POST['id'];
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$phone = mysqli_real_escape_string($db, $_POST['phone']);
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password = mysqli_real_escape_string($db, $_POST['password']);
	$role = mysqli_real_escape_string($db, $_POST['role']);
	$update = mysqli_query($db, "UPDATE users SET name='$name', phone='$phone', email='$email', password='$password', role='$role' WHERE id='$id'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_users");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_user' && isset($_GET['id'])) {
	$id = $_GET['id'];
	$delete = mysqli_query($db, "DELETE FROM users WHERE id='$id'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_users");
		exit;
	}
}

// Add new user form submission
if (isset($_POST['name1']) && isset($_POST['phone1']) && isset($_POST['email1']) && isset($_POST['password1']) && isset($_POST['role1'])) {
	$name = mysqli_real_escape_string($db, $_POST['name1']);
	$phone = mysqli_real_escape_string($db, $_POST['phone1']);
	$email = mysqli_real_escape_string($db, $_POST['email1']);
	$password = mysqli_real_escape_string($db, $_POST['password1']);
	$role = mysqli_real_escape_string($db, $_POST['role1']);

	// Check if all fields are non-empty
	if (!empty($name) && !empty($phone) && !empty($email) && !empty($password) && (!empty($role) || ($role == 0))) {
		$insert = mysqli_query($db, "INSERT INTO users (name, phone, email, password, role) VALUES ('$name', '$phone', '$email', '$password', '$role')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_users");
			exit;
		}
	} else {
		echo "<script> alert('заполните все поля') </script>";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>

<body>
	<table id="usersTable">
		<thead>
			<tr>
				<th>ID</th>
				<th>Имя пользователя</th>
				<th>Номер</th>
				<th>Email</th>
				<th>Пароль</th>
				<th>Роль</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_users as $row) { ?>
				<tr>
					<td><?php echo $row['id']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
								<label for="name">Имя пользователя:</label>
								<input type="text" name="name" value="<?php echo $row['name']; ?>">
								<label for="phone">Номер:</label>
								<input type="text" name="phone" value="<?php echo $row['phone']; ?>">
								<label for="email">Email:</label>
								<input type="text" name="email" value="<?php echo $row['email']; ?>">
								<label for="password">Пароль:</label>
								<input type="text" name="password" value="<?php echo $row['password']; ?>">
								<label for="role">Роль:</label>
								<input type="text" name="role" value="<?php echo $row['role']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['name']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['phone']; ?></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php echo $row['password']; ?></td>
					<td><?php echo $row['role']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<a href='?act=view_users'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_users&edit=<?php echo $row['id']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_user&id=<?php echo $row['id']; ?>'>Удалить</a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<!-- Add button -->
	<button id="addButton">Добавить</button>
	<a href="../admin.html">Назад</a>

	<!-- Add form -->

	<div id="addForm" style="display: none;">
		<form method="post">

			<label for="name1">Имя пользователя:</label>
			<input type="text" name="name1">

			<label for="phone1">Номер:</label>
			<input type="text" name="phone1">

			<label for="email1">Email:</label>
			<input type="text" name="email1">

			<label for="password1">Пароль:</label>
			<input type="text" name="password1">

			<label for="role1">Роль:</label>
			<input type="text" name="role1">

			<input type="submit" value="Сохранить">
		</form>
	</div>

	<script>
		document.getElementById('addButton').addEventListener('click', function() {
			document.getElementById('addForm').style.display = 'block';
		});
	</script>

</body>

</html>