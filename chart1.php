<?php
$dsn = 'mysql:host=localhost;port=3306;dbname=coffee_shop;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT
            DATE_FORMAT(TransactionHeader.TransDateTime, '%m/%d/%Y') AS `Date`,
            SUM(TransactionDetail.Quantity * ProductPrice.Price) AS `TotalAmount`
        FROM
            POSTransHeader AS TransactionHeader
        JOIN
            POSTransDetails AS TransactionDetail ON TransactionHeader.TransID = TransactionDetail.TransID
        JOIN
            ProductPrice AS ProductPrice ON TransactionDetail.PriceID = ProductPrice.PriceID
        GROUP BY
            DATE_FORMAT(TransactionHeader.TransDateTime, '%m/%d/%Y')
        ORDER BY
            `Date` DESC;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
