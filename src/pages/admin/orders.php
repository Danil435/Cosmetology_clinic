<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_orders = $db->prepare("SELECT * FROM orders");
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

// Create arrays to store the data
$data_orders = array();

while ($row = $result_orders->fetch_assoc()) {
	$data_orders[] = $row;
}

// Handle edit form submission
if (isset($_POST['order_id']) || isset($_POST['user_id']) || isset($_POST['order_date']) || isset($_POST['total_price']) || isset($_POST['status']) || isset($_POST['service_id'])) {
	$order_id = $_POST['order_id'];
	$user_id = mysqli_real_escape_string($db, $_POST['user_id']);
	$order_date = mysqli_real_escape_string($db, $_POST['order_date']);
	$total_price = mysqli_real_escape_string($db, $_POST['total_price']);
	$status = mysqli_real_escape_string($db, $_POST['status']);
	$service_id = mysqli_real_escape_string($db, $_POST['service_id']);
	$update = mysqli_query($db, "UPDATE orders SET user_id='$user_id', order_date='$order_date', total_price='$total_price', status='$status', service_id='$service_id' WHERE order_id='$order_id'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_orders");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_order' && isset($_GET['order_id'])) {
	$order_id = $_GET['order_id'];
	$delete = mysqli_query($db, "DELETE FROM orders WHERE order_id='$order_id'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_orders");
		exit;
	}
}

// Add new order form submission
if (isset($_POST['user_id1']) && isset($_POST['order_date1']) && isset($_POST['total_price1']) && isset($_POST['status1']) && isset($_POST['service_id1'])) {
	$user_id = mysqli_real_escape_string($db, $_POST['user_id1']);
	$order_date = mysqli_real_escape_string($db, $_POST['order_date1']);
	$total_price = mysqli_real_escape_string($db, $_POST['total_price1']);
	$status = mysqli_real_escape_string($db, $_POST['status1']);
	$service_id = mysqli_real_escape_string($db, $_POST['service_id1']);

	// Check if all fields are non-empty
	if (!empty($user_id) && !empty($order_date) && !empty($total_price) && !empty($status) && !empty($service_id)) {
		$insert = mysqli_query($db, "INSERT INTO orders (user_id, order_date, total_price, status, service_id) VALUES ('$user_id', '$order_date', '$total_price', '$status', '$service_id')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_orders");
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
	<table id="ordersTable">
		<thead>
			<tr>
				<th>Order ID</th>
				<th>User ID</th>
				<th>Order Date</th>
				<th>Total Price</th>
				<th>Status</th>
				<th>Service ID</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_orders as $row) { ?>
				<tr>
					<td><?php echo $row['order_id']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['order_id']) { ?>
							<form method="post">
								<input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
								<label for="user_id">User ID:</label>
								<input type="text" name="user_id" value="<?php echo $row['user_id']; ?>">
								<label for="order_date">Order Date:</label>
							 <input type="text" name="order_date" value="<?php echo $row['order_date']; ?>">
								<label for="total_price">Total Price:</label>
								<input type="text" name="total_price" value="<?php echo $row['total_price']; ?>">
								<label for="status">Status:</label>
								<input type="text" name="status" value="<?php echo $row['status']; ?>">
								<label for="service_id">Service ID:</label>
								<input type="text" name="service_id" value="<?php echo $row['service_id']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['user_id']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['order_date']; ?></td>
					<td><?php echo $row['total_price']; ?></td>
					<td><?php echo $row['status']; ?></td>
					<td><?php echo $row['service_id']; ?></td>
					<td>
						 <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['order_id']) { ?>
							<a href='?act=view_orders'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_orders&edit=<?php echo $row['order_id']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_order&order_id=<?php echo $row['order_id']; ?>'>Удалить</a>
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

			<label for="user_id1">User ID:</label>
			<input type="text" name="user_id1">

			<label for="order_date1">Order Date:</label>
			<input type="text" name="order_date1">

			<label for="total_price1">Total Price:</label>
			<input type="text" name="total_price1">

			<label for="status1">Status:</label>
			<input type="text" name="status1">

			<label for="service_id1">Service ID:</label>
			<input type="text" name="service_id1">

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