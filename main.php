<?php

require_once 'connection.php';

$conn = mysqli_connect($host, $user, $password, $database)
or die("Error " . mysqli_error($conn));

$query = "SHOW TABLES LIKE 'employees'";
$sql = mysqli_query($conn, $query) or die("Error " . mysqli_error($conn));
$result = mysqli_fetch_array($sql);
if (empty($result))
{
    $employeesTable = "
    create table employees
    (
        id   int auto_increment primary key,
        name text not null
    )";
    $sql = mysqli_query($conn, $employeesTable) or die("Error " . mysqli_error($conn));

    $json = json_decode(file_get_contents('employees.json'));
    foreach ($json as $value)
    {
        $sql = "INSERT employees (name) VALUES ('$value->name')";
        $query = mysqli_query($conn, $sql) or die('Error: '.mysqli_error($conn));
    }
}

$query = "SHOW TABLES LIKE 'time_reports'";
$sql = mysqli_query($conn, $query) or die("Error " . mysqli_error($conn));
$result = mysqli_fetch_array($sql);
if (empty($result))
{
    $time_reports_Table = "
    create table time_reports
    (
        id          int auto_increment primary key,
        employee_id int   not null,
        hours       float not null,
        date        date  not null,
        constraint time_reports_ibfk_1
            foreign key (employee_id) references employees (id)
                on update cascade on delete cascade
    )";
    $sql = mysqli_query($conn, $time_reports_Table) or die("Error " . mysqli_error($conn));

    $json = json_decode(file_get_contents('time_reports.json'));
    foreach ($json as $value)
    {
        $sql = "INSERT time_reports (employee_id, hours, date) VALUES ($value->employee_id, $value->hours, '$value->date')";
        $query = mysqli_query($conn, $sql) or die('Error: '.mysqli_error($conn));
    }
}

$query1 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-01'
    order by hours desc
    limit 3";

$query2 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-02'
    order by hours desc
    limit 3";

$query3 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-03'
    order by hours desc
    limit 3";

$query4 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-04'
    order by hours desc
    limit 3";

$query5 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-05'
    order by hours desc
    limit 3";

$query6 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-06'
    order by hours desc
    limit 3";

$query7 ="
    select e.name, time_reports.hours, time_reports.date
    from time_reports
    join employees e on time_reports.employee_id = e.id
    where date='2020-12-07'
    order by hours desc
    limit 3";

$t1 = table($conn, $query1);
$t2 = table($conn, $query2);
$t3 = table($conn, $query3);
$t4 = table($conn, $query4);
$t5 = table($conn, $query5);
$t6 = table($conn, $query6);
$t7 = table($conn, $query7);

function table($conn, $query)
{
    $sql = mysqli_query($conn, $query) or die("Error " . mysqli_error($conn));

    $rows = mysqli_num_rows($sql);

    for ($i = 1; $i <= $rows; ++$i)
    {
        $row = mysqli_fetch_array($sql);
        if ($i == 1)
        {
            $dateArr = explode('-', $row['date']);
            $mk = mktime('0', '0', '0', "$dateArr[1]", "$dateArr[2]", "$dateArr[0]");
            echo '| ' . date('l', $mk)  . ' | ';
        }
        echo (empty($row['name'])) ? '' : $row['name'];
        echo (empty($row['hours'])) ? '' : ' (' . round($row['hours'], 2) . ' hours)';
        echo ($i !== $rows) ? ', ' : ' |' . "\n";
    }
}

mysqli_close($conn);

echo "\n" . 'Press Enter...';
readline();
