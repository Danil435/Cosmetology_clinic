<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_basket = $db->prepare("SELECT * FROM Basket");
$stmt_basket->execute();
$result_basket = $stmt_basket->get_result();

// Create arrays to store the data
$data_basket = array();

while ($row = $result_basket->fetch_assoc()) {
	$data_basket[] = $row;
}

// Handle edit form submission
if (isset($_POST['id']) || isset($_POST['id_product']) || isset($_POST['count'])) {
	$id = $_POST['id'];
	$id_product = mysqli_real_escape_string($db, $_POST['id_product']);
	$count = mysqli_real_escape_string($db, $_POST['count']);
	$update = mysqli_query($db, "UPDATE Basket SET id_product='$id_product', count='$count' WHERE id='$id'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_basket");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_basket' && isset($_GET['id'])) {
	$id = $_GET['id'];
	$delete = mysqli_query($db, "DELETE FROM Basket WHERE id='$id'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_basket");
		exit;
	}
}

// Add new basket form submission
if (isset($_POST['id_product1']) && isset($_POST['count1'])) {
	$id_product = mysqli_real_escape_string($db, $_POST['id_product1']);
	$count = mysqli_real_escape_string($db, $_POST['count1']);

	// Check if all fields are non-empty
	if (!empty($id_product) && !empty($count)) {
		$insert = mysqli_query($db, "INSERT INTO Basket (id_product, count) VALUES ('$id_product', '$count')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_basket");
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
	<table id="basketTable">
		<thead>
			<tr>
				<th>ID</th>
				<th>ID Продукта</th>
				<th>Количество</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_basket as $row) { ?>
				<tr>
					<td><?php echo $row['id']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
								<label for="id_product">ID Продукта:</label>
								<input type="text" name="id_product" value="<?php echo $row['id_product']; ?>">
								<label for="count">Количество:</label>
								<input type="text" name="count" value="<?php echo $row['count']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['id_product']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['count']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<a href='?act=view_basket'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_basket&edit=<?php echo $row['id']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_basket&id=<?php echo $row['id']; ?>'>Удалить</a>
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

			<label for="id_product1">ID Продукта:</label>
			<input type="text" name="id_product1">

			<label for="count1">Количество:</label>
			<input type="text" name="count1">

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