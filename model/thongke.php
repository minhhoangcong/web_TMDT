<?php
    function doanhthu(){
        // Lấy năm hiện tại tự động (2025, 2026, 2027...)
        $sql="SELECT 
        MONTH(ngaylap) AS thang,
        YEAR(ngaylap) AS nam,
        SUM(tongtien) AS doanhthu_thang
    FROM 
        donhang
    WHERE
        YEAR(ngaylap) = YEAR(CURDATE())
    GROUP BY
        MONTH(ngaylap), YEAR(ngaylap)
    ORDER BY
        nam, thang;";
        return pdo_query($sql);
    }

    function luotview(){
        $sql="SELECT
        catalog.id AS id_catalog,
        catalog.name AS name_catalog,
        SUM(product.view) AS tongview
    FROM
        product
    JOIN
        catalog ON product.idcatalog = catalog.id
    GROUP BY
        id_catalog, name_catalog;";
        return pdo_query($sql);
    }

    function tongdoanhthu(){
        $sql="SELECT 
        SUM(tongtien) AS tongdoanhthu
    FROM 
        donhang;";
        return pdo_query($sql);
    }
?>
