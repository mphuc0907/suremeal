<?php /* Template Name: affiliate orders detail */ ?>
<?php
get_header('dealer');
$order_code = $_GET['order_code'];
$get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$order_code}' ORDER BY id DESC");
$dataProduct = $get_newest_order[0]->dataproduct;

$decodedData = json_decode(str_replace('\\', "", $dataProduct), true);

// Tên người dùng
$name_info = $get_newest_order[0]->name_user;
$transport_fee = $get_newest_order[0]->transport_fee;
$discount_price = $get_newest_order[0]->discount_price;
$email = $get_newest_order[0]->email;
$country = $get_newest_order[0]->country;
$time_order = $get_newest_order[0]->time_order;
$address1 = $get_newest_order[0]->address1;
$city = $get_newest_order[0]->city;
$ZIPCode = $get_newest_order[0]->ZIPCode;
$ship = $get_newest_order[0]->transport_fee;
$phoneNumber = $get_newest_order[0]->phoneNumber;
$price_payment = $get_newest_order[0]->price_payment;
$status = $get_newest_order[0]->status;
$price = $get_newest_order[0]->price;
$status_transport = $get_newest_order[0]->status_transport;

$class = "";
$name = "";
if ($status == 1) {
    $class = 'orderdetail-color-yellow process';
    $name = 'Processing';
} elseif ($status == 2) {
    $class = 'orderdetail-bg-blue-sure color-complete';
    $name = 'Completed';
} elseif ($status == 4) {
    $class = 'checkout-color-red-text color-cancel';
    $name = 'Canceled';
} else {
    $class = 'orderdetail-color-yellow processe';
    $name = 'In progress';
}
?>
<style>
    .orderdetail-bg-blue-error {

    }
</style>
<div class="col-span-6 text-center pt-2 px-4 pb-4">
    <div class="2xl:w-10/12 xl:w-11/12 md:w-11/12 m-auto grid grid-cols-1 gap-4 pt-8">
        <h4 class="font-medium text-xl leading-8 checkout-color-text py-0 my-0 text-left">Order information #<?= $order_code ?></h4>
    </div>
    <div class="2xl:w-10/12 xl:w-11/12 md:w-11/12 m-auto grid grid-cols-12 gap-4 pt-6">
        <div class="2xl:col-span-8 xl:col-span-8 lg:col-span-8 col-span-12 text-left">
            <div class="bg-white rounded-lg px-5 mb-6">
                <?php foreach ($decodedData as $key => $value) :
                    $idPro = $value['id'];
                    $price_sub += $value['price'] * $value['qty'];
                    $price = $value['price'];
                    ?>
                <div class="py-6 grid grid-cols-12 gap-4 bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                    <div class="2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-4 flex items-center border rounded-lg p-2">
                        <img class="w-full" src="<?= $value['img'] ?>" alt="">
                    </div>
                    <div class="2xl:col-span-4 xl:col-span-4 md:col-span-10 col-span-8">
                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                            <?= $value['title'] ?>
                        </h5>
                        <div class="tag mt-2">
	       									<span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
	       										Type: <?= $value['pack'] ?> Pack
	       									</span>
                        </div>
                    </div>
                    <div class="2xl:col-span-3 xl:col-span-3 md:col-span-6 col-span-6 flex items-center lg:justify-end md:justify-start">
                        <div class="flex items-center justify-start color-vector">
                            Quantity:&nbsp;<span><?= $value['qty'] ?></span>
                        </div>
                    </div>
                    <div class="2xl:col-span-3 xl:col-span-3 md:col-span-6 col-span-6 flex items-center justify-end">
                        <div class="checkout-color-red-text font-medium"><?= formatBalance($price) ?></div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
        <div class="2xl:col-span-4 xl:col-span-4 lg:col-span-4 col-span-12 ">
            <div class="sale flex items-start px-6 py-4 bg-white rounded-lg mb-4">
                <span class="font-normal text-sm color-vector">Name</span>
                <span class="order-2 ml-auto font-semibold"><?= $name_info ?></span>
            </div>
            <div class="info px-4 py-6 bg-white rounded-lg">
                <div class="price">
                    <div class="flex items-center grid grid-cols-2 pb-3">
                        <span class="text-left color-vector text-sm font-normal">Payment status</span>
                        <div class="flex justify-end">
                            <div class="col-span-2 text-center <?= $class ?> px-2 py-1 font-medium ml-auto order-2 text-sm"><?= $name ?></div>
                        </div>
                    </div>
                    <div class="flex items-center grid grid-cols-2 pb-3">
                        <span class="text-left color-vector text-sm font-normal">Payment method</span>
                        <div class="flex justify-end">
                            <span class="text-base checkout-color-text font-semibold">Visa</span>
                        </div>
                    </div>
                    <div class="flex items-center grid grid-cols-2 pb-3">
                        <span class="text-left color-vector text-sm font-normal">Purchase time</span>
                        <div class="flex justify-end">
                            <span class="text-base checkout-color-text font-normal"><?= date('d/m/Y - g:i A', $time_order) ?></span>
                        </div>
                    </div>
                    <div class="flex items-center grid grid-cols-2 pb-3">
                        <span class="text-left color-vector text-sm font-normal">Revenue</span>
                        <div class="flex justify-end">
                            <?php 
                            $price = 0;
                            foreach($decodedData as $item){
                                $price += $item['price'];
                            } ?>
                            <span class="checkout-color-red-text font-semibold text-base"><?= formatBalance($price) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('dealer')
?>
