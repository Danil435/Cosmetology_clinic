<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
	die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_doctors = $db->prepare("SELECT * FROM Doctors");
$stmt_doctors->execute();
$result_doctors = $stmt_doctors->get_result();

// Create arrays to store the data
$data_doctors = array();

while ($row = $result_doctors->fetch_assoc()) {
	$data_doctors[] = $row;
}

// Handle edit form submission
if (isset($_POST['id']) || isset($_POST['FIO']) || isset($_POST['specialty']) || isset($_POST['experience_years'])) {
	$id = $_POST['id'];
	$FIO = mysqli_real_escape_string($db, $_POST['FIO']);
	$specialty = mysqli_real_escape_string($db, $_POST['specialty']);
	$experience_years = mysqli_real_escape_string($db, $_POST['experience_years']);
	$update = mysqli_query($db, "UPDATE Doctors SET FIO='$FIO', specialty='$specialty', experience_years='$experience_years' WHERE id='$id'");
	if ($update) {
		// Refresh the page
		header("Location: ?act=view_doctors");
		exit;
	}
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_doctor' && isset($_GET['id'])) {
	$id = $_GET['id'];
	$delete = mysqli_query($db, "DELETE FROM Doctors WHERE id='$id'");
	if ($delete) {
		// Refresh the page
		header("Location: ?act=view_doctors");
		exit;
	}
}

// Add new doctor form submission
if (isset($_POST['FIO1']) && isset($_POST['specialty1']) && isset($_POST['experience_years1'])) {
	$FIO = mysqli_real_escape_string($db, $_POST['FIO1']);
	$specialty = mysqli_real_escape_string($db, $_POST['specialty1']);
	$experience_years = mysqli_real_escape_string($db, $_POST['experience_years1']);

	// Check if all fields are non-empty
	if (!empty($FIO) && !empty($specialty) && !empty($experience_years)) {
		$max_id = mysqli_query($db, "SELECT MAX(id) FROM Doctors");
		$max_id = mysqli_fetch_assoc($max_id);
		$max_id = $max_id['MAX(id)'] + 1;
		$insert = mysqli_query($db, "INSERT INTO Doctors (id, FIO, specialty, experience_years) VALUES ('$max_id', '$FIO', '$specialty', '$experience_years')");
		if ($insert) {
			// Refresh the page
			header("Location: ?act=view_doctors");
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
	<table id="doctorsTable">
		<thead>
			<tr>
				<th>ID</th>
				<th>FIO</th>
				<th>Специальность</th>
				<th>Стаж</th>
				<th>Редактировать</th>
				<th>Удалить</th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ($data_doctors as $row) { ?>
				<tr>
					<td><?php echo $row['id']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
								<label for="FIO">FIO:</label>
								<input type="text" name="FIO" value="<?php echo $row['FIO']; ?>">
								<label for="specialty">Специальность:</label>
								<input type="text" name="specialty" value="<?php echo $row['specialty']; ?>">
								<label for="experience_years">Стаж:</label>
								<input type="text" name="experience_years" value="<?php echo $row['experience_years']; ?>">
								<input type="submit" onclick="reload(); return false;" value="Сохранить">
							</form>
						<?php } else { ?>
							<?php echo $row['FIO']; ?>
						<?php } ?>
					</td>
					<td><?php echo $row['specialty']; ?></td>
					<td><?php echo $row['experience_years']; ?></td>
					<td>
						<?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']) { ?>
							<a href='?act=view_doctors'>Отмена</a>
						<?php } else { ?>
							<a href='?act=view_doctors&edit=<?php echo $row['id']; ?>'>Редактировать</a>
						<?php } ?>
					</td>
					<td>
						<a href='?act=delete_doctor&id=<?php echo $row['id']; ?>'>Удалить</a>
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

			<label for="FIO1">FIO:</label>
			<input type="text" name="FIO1">

			<label for="specialty1">Специальность:</label>
			<input type="text" name="specialty1">

			<label for="experience_years1">Стаж:</label>
			<input type="text" name="experience_years1">

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