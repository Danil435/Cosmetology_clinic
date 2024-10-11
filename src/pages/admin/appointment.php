<?php
$db = mysqli_connect('localhost', 'root', '', 'cosmetology_clinic');

if (!$db) {
  die("Ошибка подключения: " . mysqli_connect_error());
}

// Query the database to retrieve the data
$stmt_appointments = $db->prepare("SELECT * FROM appointment");
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();

// Create arrays to store the data
$data_appointments = array();

while ($row = $result_appointments->fetch_assoc()) {
  $data_appointments[] = $row;
}

// Handle edit form submission
if (isset($_POST['id_appointment']) || isset($_POST['id_users']) || isset($_POST['id_employee']) || isset($_POST['id_services']) || isset($_POST['appointment_date']) || isset($_POST['appointment_time'])) {
  $id_appointment = $_POST['id_appointment'];
  $id_users = mysqli_real_escape_string($db, $_POST['id_users']);
  $id_employee = mysqli_real_escape_string($db, $_POST['id_employee']);
  $id_services = mysqli_real_escape_string($db, $_POST['id_services']);
  $appointment_date = mysqli_real_escape_string($db, $_POST['appointment_date']);
  $appointment_time = mysqli_real_escape_string($db, $_POST['appointment_time']);
  $update = mysqli_query($db, "UPDATE appointments SET id_users='$id_users', id_employee='$id_employee', id_services='$id_services', appointment_date='$appointment_date', appointment_time='$appointment_time' WHERE id_appointment='$id_appointment'");
  if ($update) {
    // Refresh the page
    header("Location: ?act=view_appointments");
    exit;
  }
}

// Handle delete record
if (isset($_GET['act']) && $_GET['act'] == 'delete_appointment' && isset($_GET['id_appointment'])) {
  $id_appointment = $_GET['id_appointment'];
  $delete = mysqli_query($db, "DELETE FROM appointments WHERE id_appointment='$id_appointment'");
  if ($delete) {
    // Refresh the page
    header("Location: ?act=view_appointments");
    exit;
  }
}

// Add new appointment form submission
if (isset($_POST['id_users1']) && isset($_POST['id_employee1']) && isset($_POST['id_services1']) && isset($_POST['appointment_date1']) && isset($_POST['appointment_time1'])) {
  $id_users = mysqli_real_escape_string($db, $_POST['id_users1']);
  $id_employee = mysqli_real_escape_string($db, $_POST['id_employee1']);
  $id_services = mysqli_real_escape_string($db, $_POST['id_services1']);
  $appointment_date = mysqli_real_escape_string($db, $_POST['appointment_date1']);
  $appointment_time = mysqli_real_escape_string($db, $_POST['appointment_time1']);

  // Check if all fields are non-empty
  if (!empty($id_users) && !empty($id_employee) && !empty($id_services) && !empty($appointment_date) && !empty($appointment_time)) {
    $insert = mysqli_query($db, "INSERT INTO appointments (id_users, id_employee, id_services, appointment_date, appointment_time) VALUES ('$id_users', '$id_employee', '$id_services', '$appointment_date', '$appointment_time')");
    if ($insert) {
      // Refresh the page
      header("Location: ?act=view_appointments");
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
  <table id="appointmentsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Пользователь</th>
        <th>Сотрудник</th>
        <th>Услуга</th>
        <th>Дата записи</th>
        <th>Время записи</th>
        <th>Редактировать</th>
        <th>Удалить</th>
      </tr>
    </thead>

    <tbody>

      <?php foreach ($data_appointments as $row) { ?>
        <tr>
          <td><?php echo $row['id_appointment']; ?></td>
          <td>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_appointment']) { ?>
              <form method="post">
                <input type="hidden" name="id_appointment" value="<?php echo $row['id_appointment']; ?>">
                <label for="id_users">Пользователь:</label>
                <input type="text" name="id_users" value="<?php echo $row['id_users']; ?>">
                <label for="id_employee">Сотрудник:</label>
                <input type="text" name="id_employee" value="<?php echo $row['id_employee']; ?>">
                <label for="id_services">Услуга:</label>
                <input type="text" name="id_services" value="<?php echo $row['id_services']; ?>">
                <label for="appointment_date">Дата записи:</label>
                <input type="text" name="appointment_date" value="<?php echo $row['appointment_date']; ?>">
                <label for="appointment_time">Время записи:</label>
                <input type="text" name="appointment_time" value="<?php echo $row['appointment_time']; ?>">
                <input type="submit" onclick="reload(); return false;" value="Сохранить">
              </form>
            <?php } else { ?>
              <?php echo $row['id_users']; ?>
            <?php } ?>
          </td>
          <td><?php echo $row['id_employee']; ?></td>
          <td><?php echo $row['id_services']; ?></td>
          <td><?php echo $row['appointment_date']; ?></td>
          <td><?php echo $row['appointment_time']; ?></td>
          <td>
            <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id_appointment']) { ?>
              <a href='?act=view_appointments'>Отмена</a>
            <?php } else { ?>
              <a href='?act=view_appointments&edit=<?php echo $row['id_appointment']; ?>'>Редактировать</a>
            <?php } ?>
          </td>
          <td>
            <a href='?act=delete_appointment&id_appointment=<?php echo $row['id_appointment']; ?>'>Удалить</a>
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

      <label for="id_users1">Пользователь:</label>
      <input type="text" name="id_users1">

      <label for="id_employee1">Сотрудник:</label>
      <input type="text" name="id_employee1">

      <label for="id_services1">Услуга:</label>
      <input type="text" name="id_services1">

      <label for="appointment_date1">Дата записи:</label>
      <input type="text" name="appointment_date1">

      <label for="appointment_time1">Время записи:</label>
      <input type="text" name="appointment_time1">

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