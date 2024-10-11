<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_services = $db->prepare("SELECT * FROM Services");
$stmt_services->execute();
$result_services = $stmt_services->get_result();

// Create arrays to store the data
$data_services = array();

while ($row = $result_services->fetch_assoc()) {
	$data_services[] = $row;
}

// Handle edit form submission
if (isset($_POST['id_services']) || isset($_POST['name']) || isset($_POST['category'])) {
	$id_services = $_POST['id_services'];
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$category = mysqli_real_escape_string($db, $_POST['category']);
	$update = mysqli_query($db, "UPDATE Services SET name='$name', category='$category' WHERE id_services='$id_services'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_services");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_service' && isset($_GET['id_services'])) {
	$id_services = $_GET['id_services'];
	$delete = mysqli_query($db, "DELETE FROM Services WHERE id_services='$id_services'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_services");
		exit;
	}
}

// Add new service form submission
if (isset($_POST['name1']) && isset($_POST['category1'])) {
	$name = mysqli_real_escape_string($db, $_POST['name1']);
	$category = mysqli_real_escape_string($db, $_POST['category1']);

	// Check if all fields are non-empty
	if (!empty($name) && !empty($category)) {
		$max_id = mysqli_query($db, "SELECT MAX(id_services) FROM Services");
		$max_id = mysqli_fetch_assoc($max_id);
		$max_id = $max_id['MAX(id_services)'] + 1;
		$insert = mysqli_query($db, "INSERT INTO Services (id_services, name, category) VALUES ('$max_id', '$name', '$category')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_services");
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
	<table id="servicesTable">
		<thead>
			<tr>
				<th>ID Service</th>
				<th>Name</th>
				<th>Category</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_services as $row) { ?>
				<tr>
					<td><?php echo $row['id_services']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_services']) { ?>
							<form method="post">
								<input type="hidden" name="id_services" value="<?php echo $row['id_services']; ?>">
								<label for="name">Name:</label>
								<input type="text" name="name" value="<?php echo $row['name']; ?>">
								<label for="category">Category:</label>
								<input type="text" name="category" value="<?php echo $row['category']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['name']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['category']; ?></td>
					<td>
						 <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_services']) { ?>
							<a href='?act=view_services'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_services&edit=<?php echo $row['id_services']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
    <a href='?act=delete_service&id_services=<?php echo $row['id_services']; ?>'>Удалить</a>
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

			<label for="name1">Name:</label>
			<input type="text" name="name1">

			<label for="category1">Category:</label>
			<input type="text" name="category1">

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