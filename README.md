# 綠界物流服務

## 安裝
使用 composer 做安裝
```bash
composer require thoth-pharaoh/ecpay-express
```

## 匯出 Config
```bash
php artisan vendor:publish --tag=express-config --force
```

## 添加 .env 支付工具必要環境參數
```bash
EXPRESS_URL="https://logistics-stage.ecpay.com.tw/Express/v2/"
EXPRESS_MERCHANT_ID="2000132"
EXPRESS_HASH_KEY="5294y06JbISpM5x9"
EXPRESS_HASH_IV="v77hoKGq4kWxNNIS"
EXPRESS_VISION="1.0.0"
```

## 使用方法

### 先引入門面
```bash
use Pharaoh\Express\Facades\Express;
```

### 註冊物流相關路由
```bash
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // 註冊物流相關路由
        Express::routes();
    }
}
```

- [一段標測試資料產生(B2C)](#create-test-data)
- [獲取開啟物流選擇頁連結](#create-logistics)
- [更新暫存物流訂單](#update-temp-trade)
- [建立正式物流訂單](#create-by-temp-trade)

### <a name="create-test-data">一段標測試資料產生(B2C)</a>
```bash
$express = Express::createTestData($type);
```

#### $type 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
| LogisticsSubType | 物流子類型 | String (20) | 支援以下超商: <br> FAMI:全家物流(B2C) <br> UNIMART:7-ELEVEN 超商物流(B2C) |

### <a name="create-logistics">獲取開啟物流選擇頁連結</a>
```bash
$express = Express::createLogistics($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| GoodsAmount |✔| 商品金額 | int | 商品金額範圍為 1~20,000 元。訂單的商品金額如果超出範圍，會訂 單失敗且回傳錯誤代碼 10500040 商品金額錯誤 |
| GoodsName |✔| 商品名稱 | string(50) |注意事項:  <br>  不得輸入^ ' ` ! @ # % & * + \ " < >| _ [ ]等特殊符號 |
| SenderName |✔| 寄件人姓名 | string(10) |注意事項:  <br>  1.字元限制為 10 個字元(最多 5 個中文字、10 個英文字)、不可有^ ' ` ! @# % & * + \ " < >|_ [ ] ,，等符號且不允許空白，若帶有空白系統 自動去除 <br> <br> 2. C2C 店到店未取退回原寄件門市，須出示身分證件領取，請勿填寫 公司名稱，避免無法領取退件 |
| SenderZipCode |✔| 寄件人郵遞區號 | String (5) |  |
| SenderAddress |✔| 寄件人地址 | String (60) |  |
| IsCollection | | 是否代收貨款 | String (1) | N:不代收貨款，為預設值 <br> Y:代收貨款，帶入 Y 則物流選擇頁將不會出現宅配選項 |
| Remark | | 備註 | String (60) | |
| Temperature | | 溫層 | String (4) | 宅配專用參數 <br> 0001:常溫 (預設值) <br> 0002:冷藏 <br> 0003:冷凍 <br> 注意事項: <br> 如物流子類型為 ECAN(宅配通)只能使用 0001 常溫|
| Specification | | 規格 | String (4) | 宅配專用參數 <br> 0001: 60cm (預設值) <br> 0002: 90cm <br> 0003: 120cm <br> 0004: 150cm <br> 注意事項: <br> 溫層選擇 0002:冷藏和 0003:冷凍時， 此規格參數不可帶入 0004:150cm|
| ScheduledPickupTime | | 預定取件時段 | String (1) | 宅配專用參數 <br> 宅配物流商預定取貨的時段 <br>  1: 9~12 <br> 2: 12~17 <br> 4: 不限時(固定填 4 不限時) <br> 注意事項: <br> 當物流子類型選擇 ECAN(宅配通)時，該參數可不填|
| PackageCount | | 包裹件數 | String (20) | 宅配專用參數，預設值為 1 <br> 當物流子類型選擇 ECAN(宅配通)時時，此參數才有作用，表示同訂 單編號之包裹件數|
| ReceiverAddress | | 收件人地址 | String (60) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 |
| ReceiverCellPhone | | 收件人手機 | String (20) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 <br> 注意事項: <br> 只允許數字、10 碼且 09 開頭|
| ReceiverPhone | | 收件人電話 | String (20) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 |
| ReceiverName | | 收件人姓名 | String (10) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中|
| EnableSelectDeliveryTime | | 是否允許選擇送達時間 | String (1) | N:不允許(預設值) <br> Y:允許|
| EshopMemberID | | 廠商平台的會員 ID | String (24) | 1. 允許英文、數字組成 <br> 2. 若此參數有帶入值，且使用者於物流選擇頁有勾選「您同意綠界 科技股份有限公司記錄您上述收件資訊，以利您日後於該店消費時 之收件資訊。」欄位，綠界將記錄使用者於物流選擇頁中輸入的配送通路及收件人資料，以利於下次使用物流選擇頁時，自動帶入前一次輸入的資料|


### <a name="update-temp-trade">更新暫存物流訂單</a>
```bash
$express = Express::updateTempTrade($data);
```

參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| TempLogisticsID |✔| 暫存物流訂單編號 | string(20) | |
| GoodsAmount | | 商品金額 | int | 商品金額範圍為 1~20,000 元。訂單的商品金額如果超出範圍，會訂 單失敗且回傳錯誤代碼 10500040 商品金額錯誤 |
| GoodsName | | 商品名稱 | string(50) |注意事項:  <br>  不得輸入^ ' ` ! @ # % & * + \ " < >| _ [ ]等特殊符號 |
| SenderName | | 寄件人姓名 | string(10) |注意事項:  <br>  1.字元限制為 10 個字元(最多 5 個中文字、10 個英文字)、不可有^ ' ` ! @# % & * + \ " < >|_ [ ] ,，等符號且不允許空白，若帶有空白系統 自動去除 <br> <br> 2. C2C 店到店未取退回原寄件門市，須出示身分證件領取，請勿填寫 公司名稱，避免無法領取退件 |
| SenderZipCode | | 寄件人郵遞區號 | String (5) |  |
| SenderAddress | | 寄件人地址 | String (60) |  |
| Remark | | 備註 | String (60) | |
| ReturnStoreID | | 退貨門市代 號 | String (6) | 若沒有設定 ReturnStoreID，當貨品退貨時，會返回原寄件門市，若有設定則會送至指定的退貨門市 <br> 注意事項: <br> 僅 7-11 C2C 適用，其他超商 C2C 帶此參數仍會退回原寄件門市 |
| Specification | | 規格 | String (4) | 宅配專用參數 <br> 0001: 60cm (預設值) <br> 0002: 90cm <br> 0003: 120cm <br> 0004: 150cm <br> 注意事項: <br> 溫層選擇 0002:冷藏和 0003:冷凍時， 此規格參數不可帶入 0004:150cm|
| PackageCount | | 包裹件數 | String (20) | 宅配專用參數，預設值為 1 <br> 當物流子類型選擇 ECAN(宅配通)時時，此參數才有作用，表示同訂 單編號之包裹件數|
| ReceiverAddress | | 收件人地址 | String (60) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 |
| ReceiverZipCode | | 收件人郵遞區號 | String (5) | |
| ReceiverCellPhone | | 收件人手機 | String (20) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 <br> 注意事項: <br> 只允許數字、10 碼且 09 開頭|
| ReceiverPhone | | 收件人電話 | String (20) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中 |
| ReceiverName | | 收件人姓名 | String (10) | 若此參數有帶入值，將自動帶入物流選擇頁的對應欄位中|

### <a name="create-by-temp-trade">建立正式物流訂單</a>
```bash
$express = Express::createByTempTrade($tempLogisticsId);
```

#### $tempLogisticsId 內容說明
參數 |  名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------|
| LogisticsID | 綠界訂單編號 | String (20) | 請保存廠商交易編號與 LogisticsID 的關連 |
