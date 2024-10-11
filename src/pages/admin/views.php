<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cosmetology_clinic";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function get_services_by_type_and_category($conn)
{
  echo "№1 Список и общее число услуг определенного типа и категории:<br>";
  $sql = "SELECT name, COUNT(*) AS total_services FROM Services WHERE category = 'аппаратные' GROUP BY name";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Name: " . $row["name"] . " - Total Services: " . $row["total_services"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_most_frequent_services($conn)
{
  echo "№2 Список услуг, которые чаще всего предоставляются пациентам:<br>";
  $sql = "SELECT s.name, COUNT(a.id_services) AS total_appointments 
          FROM Services s 
          JOIN Appointments a ON s.id_service = a.id_services 
          GROUP BY s.id_service 
          ORDER BY total_appointments DESC";
  
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Name: " . htmlspecialchars($row["name"]) . " - Total Appointments: " . intval($row["total_appointments"]) . "<br>";
    }
  } else {
    echo "0 results";
  }
  
  echo "<br><br>";
}

function get_patients_by_doctor_specialty($conn)
{
  echo "№3 Список пациентов, наблюдающихся у врача указанного профиля:<br>";
  $sql = "SELECT DISTINCT u.name, u.email, u.phone 
          FROM users u 
          JOIN Appointments a ON u.id = a.id_users 
          JOIN employee e ON a.id_employee = e.id 
          WHERE e.specialty = 'Косметолог'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Name: " . $row["name"] . " - Email: " . $row["email"] . " - Phone: " . $row["phone"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_patients_by_doctor_and_time_period($conn)
{
  echo "№4 Список пациентов, перенесших операции у конкретного врача за некоторый промежуток времени:<br>";
  $sql = "SELECT DISTINCT u.name, u.email, u.phone 
          FROM users u 
          JOIN Appointments a ON u.id = a.id_users 
          WHERE a.id_employee = 1 
          AND a.appointment_date BETWEEN '2024-10-01' AND '2024-10-31'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Name: " . $row["name"] . " - Email: " . $row["email"] . " - Phone: " . $row["phone"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_new_patients($conn)
{
  echo "№5 Список пациентов, которые только зарегистрировались в клинике в настоящее время:<br>";
  $sql = "SELECT name, email, phone FROM users WHERE DATE(created_at) = CURDATE()";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Name: " . $row["name"] . " - Email: " . $row["email"] . " - Phone: " . $row["phone"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_doctors_by_specialty_and_experience($conn)
{
  echo "№6 Список и общее число врачей указанного профиля, стаж работы которых не менее заданного:<br>";
  $sql = "SELECT FIO, experience_years FROM employee WHERE specialty = 'Косметолог' AND experience_years >= 3";
  $result = $conn->query($sql);
  if ($result->num_rows >  0) {
    while ($row = $result->fetch_assoc()) {
      echo "FIO: " . $row["FIO"] . " - Experience Years: " . $row["experience_years"] . "<br>";
    }
    echo "Total doctors: " . $result->num_rows . "<br>";
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}
function get_doctor_productivity($conn)
{
  echo "№7 Данные о выработке (среднее число принятых пациентов в день) за указанный период для конкретного врача, либо для всех врачей названного профиля:<br>";
  $sql = "SELECT e.FIO, COUNT(a.id_appointment) / DATEDIFF('2024-10-31', '2024-10-01') AS average_appointments 
          FROM employee e 
          JOIN Appointments a ON e.id = a.id_employee 
          WHERE e.specialty = 'Косметолог' 
          AND a.appointment_date BETWEEN '2024-10-01' AND '2024-10-31' 
          GROUP BY e.id";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "FIO: " . $row["FIO"] . " - Average Appointments per Day: " . number_format($row["average_appointments"], 2) . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_appointments_and_visits($conn)
{
  echo "№8 Общее число кабинетов, число посещений каждого кабинета за определенный период:<br>";
  $sql = "SELECT r.room_number, COUNT(v.visit_id) AS visit_count 
          FROM Rooms r 
          LEFT JOIN Visits v ON r.id_room = v.id_room 
          LEFT JOIN Appointments a ON v.id_appointment = a.id_appointment 
          WHERE a.appointment_date BETWEEN '2024-10-01' AND '2024-10-31' 
          GROUP BY r.id_room";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Room Number: " . $row["room_number"] . " - Visit Count: " . $row["visit_count"] . "<br>";
    }
    echo "Total Rooms: " . $result->num_rows . "<br>";
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_services_by_patient($conn)
{
  echo "№9 Список полученных услуг конкретного пациента, за определенное период:<br>";
  $sql = "SELECT s.name, a.appointment_date 
          FROM Appointments a 
          JOIN Services s ON a.id_services = s.id_service 
          WHERE a.id_users = 1 
          AND a.appointment_date BETWEEN '2024-10-01' AND '2024-10-31'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Service: " . $row["name"] . " - Date: " . $row["appointment_date"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

function get_payment_requests($conn)
{
  echo "№10 Список заявок на оплату услуг, с определенным типом услуги:<br>";
  $sql = "SELECT p.id_payment, s.name AS service_name 
          FROM Payments p 
          JOIN Appointments a ON p.id_appointment = a.id_appointment 
          JOIN Services s ON a.id_services = s.id_service 
          WHERE s.category = 'аппаратные'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Payment ID: " . $row["id_payment"] . " - Service: " . $row["service_name"] . "<br>";
    }
  } else {
    echo "0 results";
  }
  echo "<br><br>";
}

get_services_by_type_and_category($conn);
get_most_frequent_services($conn);
get_patients_by_doctor_specialty($conn);
get_patients_by_doctor_and_time_period($conn);
get_new_patients($conn);
get_doctors_by_specialty_and_experience($conn);
get_doctor_productivity($conn);
get_appointments_and_visits($conn);
get_services_by_patient($conn);
get_payment_requests($conn);

$conn->close();
echo "<a href='../admin.html'>Назад</a>";