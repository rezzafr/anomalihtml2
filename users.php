<?php
require_once 'includes/db_connect.php';

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<h2>Users</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Role</th>
    <th>Created At</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['username'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>
    <td><?= $row['created_at'] ?></td>
</tr>
<?php } ?>
</table>
