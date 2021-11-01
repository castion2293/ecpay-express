<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="antialiased">
<div class="container ">
    <table class="table table-bordered border-primary mt-3">
        <tbody>
            @if (isset($TempLogisticsID))
                <tr>
                    <td class="table-active">綠界暫存物流訂單編號</td>
                    <td>{{ $TempLogisticsID }}</td>
                </tr>
            @endif
            @if (isset($LogisticsType))
                <tr>
                    <td class="table-active">物流類型</td>
                    <td>{{ $LogisticsType }}</td>
                </tr>
            @endif
            @if (isset($LogisticsSubType))
                <tr>
                    <td class="table-active">物流子類型</td>
                    <td>{{ $LogisticsSubType }}</td>
                </tr>
            @endif
            @if (isset($ScheduledDeliveryDate))
                <tr>
                    <td class="table-active">預定送達日期</td>
                    <td>{{ $ScheduledDeliveryDate }}</td>
                </tr>
            @endif
            @if (isset($ScheduledDeliveryTime))
                <tr>
                    <td class="table-active">預定送達時段</td>
                    <td>{{ $ScheduledDeliveryTime }}</td>
                </tr>
            @endif
            @if (isset($ReceiverName))
                <tr>
                    <td class="table-active">收件人姓名</td>
                    <td>{{ $ReceiverName }}</td>
                </tr>
            @endif
            @if (isset($ReceiverPhone))
                <tr>
                    <td class="table-active">收件人電話</td>
                    <td>{{ $ReceiverPhone }}</td>
                </tr>
            @endif
            @if (isset($ReceiverCellPhone))
                <tr>
                    <td class="table-active">收件人手機</td>
                    <td>{{ $ReceiverCellPhone }}</td>
                </tr>
            @endif
            @if (isset($ReceiverZipCode))
                <tr>
                    <td class="table-active">收件人郵遞區號</td>
                    <td>{{ $ReceiverZipCode }}</td>
                </tr>
            @endif
            @if (isset($ReceiverAddress))
                <tr>
                    <td class="table-active">收件人地址</td>
                    <td>{{ $ReceiverAddress }}</td>
                </tr>
            @endif
            @if (isset($ReceiverStoreID))
                <tr>
                    <td class="table-active">收件人門市代號</td>
                    <td>{{ $ReceiverStoreID }}</td>
                </tr>
            @endif
            @if (isset($ReceiverStoreName))
                <tr>
                    <td class="table-active">收件門市名稱</td>
                    <td>{{ $ReceiverStoreName }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
