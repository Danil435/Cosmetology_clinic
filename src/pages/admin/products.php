<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_products = $db->prepare("SELECT * FROM products");
$stmt_products->execute();
$result_products = $stmt_products->get_result();

// Create arrays to store the data
$data_products = array();

while ($row = $result_products->fetch_assoc()) {
	$data_products[] = $row;
}

// Handle edit form submission
if (isset($_POST['id_product']) || isset($_POST['name']) || isset($_POST['cost']) || isset($_POST['img'])) {
	$id_product = $_POST['id_product'];
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$cost = mysqli_real_escape_string($db, $_POST['cost']);
	$img = mysqli_real_escape_string($db, $_POST['img']);
	$update = mysqli_query($db, "UPDATE products SET name='$name', cost='$cost', img='$img' WHERE id_product='$id_product'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_products");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_product' && isset($_GET['id_product'])) {
	$id_product = $_GET['id_product'];
	$delete = mysqli_query($db, "DELETE FROM products WHERE id_product='$id_product'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_products");
		exit;
	}
}

// Add new product form submission
if (isset($_POST['name1']) && isset($_POST['cost1']) && isset($_POST['img1'])) {
	$name = mysqli_real_escape_string($db, $_POST['name1']);
	$cost = mysqli_real_escape_string($db, $_POST['cost1']);
	$img = mysqli_real_escape_string($db, $_POST['img1']);

	// Check if all fields are non-empty
	if (!empty($name) && !empty($cost) && !empty($img)) {
		$max_id = mysqli_query($db, "SELECT MAX(id_product) FROM products");
		$max_id = mysqli_fetch_assoc($max_id);
		$max_id = $max_id['MAX(id_product)'] + 1;
		$insert = mysqli_query($db, "INSERT INTO products (id_product, name, cost, img) VALUES ('$max_id', '$name', '$cost', '$img')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_products");
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
	<table id="productsTable">
		<thead>
			<tr>
				<th>ID Product</th>
				<th>Name</th>
				<th>Cost</th>
				<th>Img</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_products as $row) { ?>
				<tr>
					<td><?php echo $row['id_product']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_product']) { ?>
							<form method="post">
								<input type="hidden" name="id_product" value="<?php echo $row['id_product']; ?>">
								<label for="name">Name:</label>
								<input type="text" name="name" value="<?php echo $row['name']; ?>">
								<label for="cost">Cost:</label>
								<input type="text" name="cost" value="<?php echo $row['cost']; ?>">
								<label for="img">Img:</label>
								<input type="text" name="img" value="<?php echo $row['img']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['name']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['cost']; ?></td>
					<td><?php echo $row['img']; ?></td >
					<td>
						 <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_product']) { ?>
							<a href='?act=view_products'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_products&edit=<?php echo $row['id_product']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_product&id_product=<?php echo $row['id_product']; ?>'>Удалить</a>
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

			<label for="cost1">Cost:</label>
			<input type="text" name="cost1">

			<label for="img1">Img:</label>
			<input type="text" name="img1">

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