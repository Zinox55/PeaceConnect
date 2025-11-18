<?php
require_once __DIR__ . "/../../Controller/DonController.php";

$controller = new DonController();
$donations = $controller->listDon(); // fetch all donations
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liste des Dons</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<h1>Liste des Dons</h1>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Montant</th>
            <th>Nom Donneur</th>
            <th>Email</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($donations as $don) : ?>
            <tr>
                <td><?= htmlspecialchars($don['id']) ?></td>
                <td><?= htmlspecialchars($don['amount']) ?> dt</td>
                <td><?= htmlspecialchars($don['name']) ?></td>
                <td><?= htmlspecialchars($don['email']) ?></td>
                <td><?= htmlspecialchars($don['date_don']) ?></td>

                <td>
                    <a href="deleteDon.php?id=<?= $don['id'] ?>">Supprimer</a>
                    |
                    <a href="updateDon.php?id=<?= $don['id'] ?>">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
