<?php
$dsn = 'mysql:host=localhost;port=3306;dbname=coffee_shop;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT
            ProductType.ProductType AS `ProductType`,
            Product.ProductID,
            Product.ProductName,
            Price.Size,
            Price.Price,
            SUM(TransactionDetail.Quantity) AS `Quantity`,
            SUM(TransactionDetail.Quantity * Price.Price) AS `TotalAmount`
        FROM 
            POSTransDetails AS TransactionDetail
        JOIN
            POSTransHeader AS TransactionHeader ON TransactionDetail.TransID = TransactionHeader.TransID
        JOIN
            Products AS Product ON TransactionDetail.ProductID = Product.ProductID
        JOIN
            ProductPrice AS Price ON TransactionDetail.PriceID = Price.PriceID
        JOIN
            ProductType AS ProductType ON Product.ProductTypeID = ProductType.ProductTypeID
        GROUP BY
            ProductType.ProductType,
            Product.ProductID,
            Product.ProductName,
            Price.Size,
            Price.Price
        ORDER BY
            Product.ProductName ASC,
            CASE
                WHEN Price.Size = 'Small (12 Oz)' THEN 1
                WHEN Price.Size = 'Medium (24 Oz)' THEN 2
                WHEN Price.Size = 'Large (36 Oz)' THEN 3
                ELSE 4
            END;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
