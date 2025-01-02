<?php /* Template Name: PDF */ ?>
<?php


require '/var/www/domains/qixtech.com/suremeal.qixtech.com/vendor/autoload.php'; // Đảm bảo rằng bạn đã cài đặt dompdf qua Composer

use Dompdf\Dompdf;
use Dompdf\Options;

// Tạo đối tượng Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);  // Bật chế độ HTML5
$options->set('isPhpEnabled', true);          // Bật khả năng sử dụng PHP trong HTML (nếu cần)
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);



// Dữ liệu ví dụ (thông tin sản phẩm và thanh toán)
global $wpdb;
$code = $_GET['order_code'];

$get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$code}' ORDER BY id DESC");
$dataProduct = $get_newest_order[0]->dataproduct;
$decodedData = json_decode(str_replace('\\' ,"",$dataProduct), true);
$jsonid = str_replace('\\' ,"",$dataProduct);
$data = json_decode($jsonid, true);
$jsonIds = json_encode($ids);

// In ra mảng JSON

$time_order_uat = $get_newest_order[0]->time_order;
date_default_timezone_set('America/New_York');
$formattedDateEastern = date('m/d/Y', $time_order_uat);

$totalRecords = count($decodedData);
$url = get_template_directory_uri();
// Tên người dùng
$name_info = $get_newest_order[0]->name_user;
$email = $get_newest_order[0]->email;
$state = $get_newest_order[0]->state;
$country = $get_newest_order[0]->country;
$address1 = $get_newest_order[0]->address1;
$city = $get_newest_order[0]->city;
$ZIPCode = $get_newest_order[0]->ZIPCode;
$ship = $get_newest_order[0]->transport_fee;
$phoneNumber = $get_newest_order[0]->phoneNumber;
$price_payment = $get_newest_order[0]->price_payment;
$price = $get_newest_order[0]->price;
header('Content-Type: text/html; charset=utf-8');

// Mẫu HTML của bạn (có chứa các dữ liệu PHP)
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <title>Invoice Export</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
    * {
    font-family: DejaVu Sans, sans-serif;
}
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }

        #invoice {
       
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            margin: 5px 0;
        }

        .section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .total {
            font-weight: bold;
        }

        #download {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #download:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
<div id="invoice">
    <h1>Invoice Export</h1>

    <!-- Invoice content here -->
    <div class="section">
        <p><strong>Bill to:</strong> '.  $name_info .'<br>
            Email: ' . $email .'<br>
            Phone Number: ' . $phoneNumber .'<br>
            Cty: ' . $city .'<br>
            '. $ZIPCode .'</p>

        <p><strong>Ship to:</strong> ' . $address1 .', '. $state .'<br>
            ' . $country .', '. $ZIPCode .'</p>
    </div>

    <div class="section">
        <p><strong>Invoice Date:</strong>'. $formattedDateEastern .'</p>
        <p><strong>Invoice #:</strong> '. $code .'</p>
        <p><strong>Currency:</strong> USD</p>
    </div>

    <div class="section">
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Amount (USD)</th>
            </tr>
            </thead>
            <tbody>';

foreach ($decodedData as $product) {
    $productTitle = $product['title'];
    $productPrice = (int)$product['price'];
    $productQuantity = (int)$product['qty'];
    $totalProductPrice = $productPrice * $productQuantity;
    $id_product = $product['id'];
    $qty = get_field('quantity', $id_product);
    // Cộng dồn tổng tiền
    $totalPrice += $totalProductPrice;
    $i++;
    $html .= ' <tr>
                    <td>' . $i .'</td>
                    <td>' . $productTitle .'</td>
                    <td>' . $productQuantity . '</td>
                    <td>' . number_format($productPrice, 2) .'</td>
                    <td>' . number_format($totalProductPrice, 2) .'</td>
                </tr>';
}
$html .= ' </tbody>
        </table>
    </div>

    <div class="section">
        <p><strong>Subtotal:</strong> ' . number_format($price, 2) .'</p>
        <p><strong>Shipping fee:</strong> '. number_format($ship, 2) .'</p>
        <p class="total"><strong>Total Amount:</strong> ' . number_format($price_payment, 2) .' </p>
    </div>
    
</div></body>
</html>
';

// Load HTML vào Dompdf
$dompdf->loadHtml($html);

// Thiết lập kích thước giấy
$dompdf->setPaper('A4', 'portrait'); // Hoặc 'landscape' cho hướng ngang

// Tạo PDF
$dompdf->render();

// Xuất file PDF và tải về
$dompdf->stream("order_summary_". $code .".pdf", array("Attachment" => 1)); // `Attachment => 1` để tải file về
?>
