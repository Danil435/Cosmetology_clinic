<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_payments = $db->prepare("SELECT * FROM Payments");
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();

// Create arrays to store the data
$data_payments = array();

while ($row = $result_payments->fetch_assoc()) {
	$data_payments[] = $row;
}

// Handle edit form submission
if (isset($_POST['id_payment']) || isset($_POST['id_appointment']) || isset($_POST['payment_date']) || isset($_POST['amount'])) {
	$id_payment = $_POST['id_payment'];
	$id_appointment = mysqli_real_escape_string($db, $_POST['id_appointment']);
	$payment_date = mysqli_real_escape_string($db, $_POST['payment_date']);
	$amount = mysqli_real_escape_string($db, $_POST['amount']);
	$update = mysqli_query($db, "UPDATE Payments SET id_appointment='$id_appointment', payment_date='$payment_date', amount='$amount' WHERE id_payment='$id_payment'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_payments");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_payment' && isset($_GET['id_payment'])) {
	$id_payment = $_GET['id_payment'];
	$delete = mysqli_query($db, "DELETE FROM Payments WHERE id_payment='$id_payment'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_payments");
		exit;
	}
}

// Add new payment form submission
if (isset($_POST['id_appointment1']) && isset($_POST['payment_date1']) && isset($_POST['amount1'])) {
	$id_appointment = mysqli_real_escape_string($db, $_POST['id_appointment1']);
	$payment_date = mysqli_real_escape_string($db, $_POST['payment_date1']);
	$amount = mysqli_real_escape_string($db, $_POST['amount1']);

	// Check if all fields are non-empty
	if (!empty($id_appointment) && !empty($payment_date) && !empty($amount)) {
		$max_id = mysqli_query($db, "SELECT MAX(id_payment) FROM Payments");
		$max_id = mysqli_fetch_assoc($max_id);
		$max_id = $max_id['MAX(id_payment)'] + 1;
		$insert = mysqli_query($db, "INSERT INTO Payments (id_payment, id_appointment, payment_date, amount) VALUES ('$max_id', '$id_appointment', '$payment_date', '$amount')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_payments");
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
	<table id="paymentsTable">
		<thead>
			<tr>
				<th>ID Payment</th>
				<th>ID Appointment</th>
				<th>Payment Date</th>
				<th>Amount</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_payments as $row) { ?>
				<tr>
					<td><?php echo $row['id_payment']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_payment']) { ?>
							<form method="post">
								<input type="hidden" name="id_payment" value="<?php echo $row['id_payment']; ?>">
								<label for="id_appointment">ID Appointment:</label>
								<input type="text" name="id_appointment" value="<?php echo $row['id_appointment']; ?>">
								<label for="payment_date">Payment Date:</label>
								<input type="text" name="payment_date" value="<?php echo $row['payment_date']; ?>">
								<label for="amount">Amount:</label>
								<input type="text" name="amount" value="<?php echo $row['amount']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
						 <?php echo $row['id_appointment']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['payment_date']; ?></td>
					<td><?php echo $row['amount']; ?></td>
					<td>
						 <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_payment']) { ?>
							<a href='?act=view_payments'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_payments&edit=<?php echo $row['id_payment']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_payment&id_payment=<?php echo $row['id_payment']; ?>'>Удалить</a>
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

			<label for="id_appointment1">ID Appointment:</label>
			<input type="text" name="id_appointment1">

			<label for="payment_date1">Payment Date:</label>
			<input type="text" name="payment_date1">

			<label for="amount1">Amount:</label>
			<input type="text" name="amount1">

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