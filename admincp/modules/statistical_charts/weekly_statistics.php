<?php
    use Carbon\Carbon;
    include('../../config/connection.php');
    require('../../../Carbon/autoload.php');

    $subdays3 = Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)->toDateString();
    $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
    $sql3 = "SELECT 
    DATE(order_created_time) AS NgayDat,
    COUNT(*) AS DonHang,
    SUM(order_value) AS DoanhThu FROM
    tblorder WHERE order_status = '1' AND
    DATE(order_created_time) BETWEEN '$subdays3' AND '$now'
    GROUP BY DATE(order_created_time)";
    $sql_query3 = mysqli_query($mysqli, $sql3);
    while($val3 = mysqli_fetch_array($sql_query3)) {
        $chart_data3[] = array (
            'date3' => $val3['NgayDat'],
            'order3' => $val3['DonHang'],
            'sales3' => $val3['DoanhThu']
        );
    }
    echo $data3 = json_encode($chart_data3);
?>