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
- [建立列印託運單連結](#create-trade-document)
- [(B2C) 7-ELEVEN 逆物流訂單](#return-unimart-cvs)
- [(B2C) 全家逆物流訂單](return-fami-cvs)
- [宅配逆物流訂單](return-home)
- [特店進行物流訂單查詢作業](query-logistics-trade-info)
- [(B2C) 7-ELEVEN-更新出貨日、門市](update-shipment-info)
- [(C2C)7-ELEVEN、全家、OK - 更新門市](update-store-info)
- [取消訂單(7-EVEVEN 超商 C2C)](cancel-c2c-order)

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

#### $data 內容說明(array格式)
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

### <a name="create-trade-document">建立列印託運單連結</a>
```bash
$express = Express::createTradeDocument($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| LogisticsID |✔| 綠界訂單編號 | Array | 1. 可支援單筆或批次列印 <br> 2. 流子類型[LogisticsSubType]與綠界訂單編號[LogisticsID]須為同 一物流方式 <br> 請帶入建立正式物流訂單時綠界回復之綠界訂單編號 [LogisticsID]|
| LogisticsSubType |✔| 物流子類型 | string(20) | |

### <a name="return-unimart-cvs">(B2C) 7-ELEVEN 逆物流訂單</a>
```bash
$express = Express::returnUniMartCVS($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| GoodsAmount |✔| 商品金額 | int | 金額範圍為 1~20,000 元 此為商品遺失賠償依據，僅可使用數字 |
| SenderName |✔| 退貨人姓名 | string(50 | 注意事項: 不可有符號不可有符號^ ' ` !@# % & * + \ " < >|_ [ ] , ，且 不可有空白，若帶有空白系統自動去除 |
| LogiscisID | | 綠界訂單編號 | String (20) | |
| GoodsName | | 商品名稱 | String (50) | 注意事項: <br> 不得輸入^ ' ` ! @ # % & * + \ " < >| _ [ ]等特殊符號 |
| SenderPhone | | 退貨人手機 | String (20) | |
| Remark | | 備註 | String (40) | |

### <a name="return-fami-cvs">(B2C) 全家逆物流訂單</a>
```bash
$express = Express::returnFamiCVS($data);
```

#### $data 內容說明(array格式)與 7-ELEVEN 逆物流訂單 相同

### <a name="return-home">宅配逆物流訂單</a>
```bash
$express = Express::returnHome($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| GoodsAmount |✔| 商品金額 | int | 金額範圍為 1~20,000 元 此為商品遺失賠償依據，僅可使用數字 |
| LogiscisID | | 綠界訂單編號 | string(20) | |
| LogisticsSubType | | 物流子類型 | string(20) | TCAT:黑貓 <br> ECAN:宅配通 <br> 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值，且原物流類型[LogisticsType]為HOME(宅配)，本欄位可為空值 <br> 若綠界訂單編號[LogisticsID]有值，且原物流類型[LogisticsType]為CVS(超商取貨)，本欄位不可為空 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位不可為空值|
| SenderName | | 退貨人姓名 | string(10) | 1. 若綠界訂單編號[LogisticsID]有值且與該訂單為相同的「物流子類型」時，退貨收件人與寄件人資訊會與原訂單相反且自動帶入宅配規格、溫層、距離、品名 <br> 2. 若資訊與關連物流交易不同時，可自行輸入 <br> 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值，且原物流類型[LogisticsType]為HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值，且原物流類型[LogisticsType]為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位不可為空值 <br> 4. 長度限制最多 10 個字元(中文 5 個字，英文 10 個字)|
| SenderPhone | | 退貨人電話 | string(20) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]不為空值時，且原物流類型 [LogisticsType]為HOME(宅配)，本欄位可為空值 <br> 2. 若物流交易編號[LogisticsID]不為 空值時，且原物流類型 [LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若物流交易編號[LogisticsID]為空值時，本欄位與退貨人手機 [SenderCellPhone]擇一不可為空) <br> 4. 允許數字+特殊符號;特殊符號僅限 ()-#|
| SenderCellPhone | | 退貨人手機 | string(20) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]不為空值時，且原物流類型[LogisticsType] 為 HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]有值時，本欄位與退貨人電話 [SenderPhone]擇一不可為空 <br> 4. 只允許數字、10 碼、09 開頭|
| SenderZipCode | | 退貨人郵遞 區號 | string(5) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位不可為空值|
| SenderAddress | | 退貨人地址 | string(60) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號 [LogisticsID]有值時，且原物流類型[LogisticsType] 為 CVS(超商取貨)，本欄位字元限制需大於 6 個字元，且不可超過 60 個字元 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位字元限制需大於 6 個字元，且不可超過 60 個字元|
| ReceiverName | | 收件人姓名 | string(10) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位不可為空值 <br> 4. 字元限制為 10 個字元(最多 5 個中文字、10 個英文字)、不可有空白，若帶有空白系統自動去除|
| ReceiverPhone | | 收件人電話 | string(20) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]不為空值時，且原物流類型 [LogisticsType] 為 HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]不為空值時，且原物流類型 [LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位與收件人手機 [ReceiverCellPhone]擇一不可為空) <br> 4. 允許數字+特殊符號;特殊符號僅限()-#|
| ReceiverCellPhone | | 收件人手機 | string(20) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為宅配HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位與收件人電話 [ReceiverPhone]擇一不可為空 <br> 4. 只允許數字、10 碼、09 開頭|
| ReceiverZipCode | | 收件人郵遞區號 | string(5) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空 值時，本欄位不可為空值|
| ReceiverAddress | | 收件人地址 | string(60) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 CVS(超商取貨)，本欄位字元限制需大 於 6 個字元，且不可超過 60 個字元 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位字元限制需大於 6 個字 元，且不可超過 60 個字元|
| ReceiverEmail | | 收件人 mail | string(50) | 注意事項: <br> 1. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 HOME(宅配)，本欄位可為空值 <br> 2. 若綠界訂單編號[LogisticsID]有值時，且原物流類型[LogisticsType] 為 CVS(超商取貨)，本欄位不可為空值 <br> 3. 若綠界訂單編號[LogisticsID]為空值時，本欄位不可為空值|
| GoodsName | | 物品名稱 | string(60) | 注意事項: <br> 不得輸入^ ' ` ! @ # % & * + \ " < >| _ [ ]等特殊符號|
| Temperature | | 溫層 | string(4) | 0001:常溫 (預設值) <br> 0002:冷藏 <br> 0003:冷凍|
| Distance | | 距離 | string(2) | 00:同縣市 (預設值) <br> 01:外縣市 <br> 2:離島 <br> ※注意事項 <br> 當系統檢查到收件人地址 (ReceiverAddress)與寄件人地址 (SenderAddress)所屬縣市與距離(Distance) 輸入的值不相符時，系統將自動更正距離 (Distance)為正確值|
| Specification | | 規格 | string(4) | 0001: 60cm (預設值) <br> 0002: 90cm <br> 0003: 120cm <br> 0004: 150cm <br> 注意事項: <br> 溫層選擇 0002:冷藏和 0003:冷凍時，此規格參數不可帶入 0004:150cm|
| ScheduledDeliveryTime | | 預定送達時段 | string(2) | 1:13 前 <br> 2: 14~18 <br> 4:不限時 <br> 當子物流選擇 ECAN(宅配通)時，可選擇以下時段 <br> 12:13 前 <br> 23: 14~18 <br> 4:不限時|
| ScheduledDeliveryDate | | 指定送達日 | string(10) | 當物流子類型選擇 ECAN(宅配通)時，此參數才有作用 <br> 注意事項: <br> 日期指定限制 D+3 (D:該訂單建立時間)|
| Remark | | 備註 | string(60) | |
| PlatformID | | 特約合作平台商代號 | string(10) | 由綠界科技提供，此參數為專案合作的平 台商使用，一般廠商介接 請放空值。若為專案合作的平台商使用時，MerchantID 請帶賣家所綁定的 MerchantID|


### <a name="query-logistics-trade-info">特店進行物流訂單查詢作業</a>
```bash
$express = Express::queryLogisticsTradeInfo($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| LogiscisID | | 綠界訂單編號 | string(20) | 與 MerchantTradeNo(廠商交易編號)需二擇一填寫 |
| MerchantTradeNo | | 廠商交易編號 | string(20) | 1. 廠商交易編號均為唯一值，不可重複使用 <br> 2. 英數字大小寫混合 <br> 3. 與 LogisticsID(綠界訂單編號)需二擇一填寫 |

### <a name="update-shipment-info">(B2C) 7-ELEVEN-更新出貨日、門市</a>
```bash
$express = Express::updateShipmentInfo($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| LogiscisID |✔| 綠界訂單編號 | string(20) |  |
| ShipmentDate | | 物流訂單出貨日 期 | string(10) | 物流訂單出貨日期(貨到物流中心的日期)、物流訂單取貨門市需擇一必填 |
| ReceiverStoreID | | 物流訂單取貨門市 | string(6) | 物流訂單出貨日期(貨到物流中心的日期)、物流訂單取貨門市需擇一必填 <br> 注意事項: <br> 1. 當 7-ELEVEN 貨態為 2037 門市關轉時，才能變更取貨門市 <br> 2. 收到 2037 門市關轉貨態時，請於第 3 日早上 10 點前(如5/26 當天收到門市關轉貨態，須於 5/28 早上 10 點前回傳) 變更取貨門市|

### <a name="update-store-info">(C2C)7-ELEVEN、全家、OK - 更新門市</a>
```bash
$express = Express::updateStoreInfo($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| LogiscisID |✔| 綠界訂單編號 | string(20) |  |
| CVSPaymentNo |✔| 寄貨編號 | string(15) |  |
| CVSValidationNo |✔| 驗證碼 | string(10) | 若為 OK 超商非必填 |
| StoreType |✔| 門市類型 | string(2) | 01:取件門市更新 <br> 02:退件門市更新 |
| ReceiverStoreID | | 物流訂單取貨門市 | string(6) | 注意事項: <br> 物流訂單取貨門市、物流訂單退貨門市 需擇一必填 |
| ReturnStoreID | | 物流訂單退貨門市 | string(6) | 注意事項: <br> 物流訂單取貨門市、物流訂單退貨門市 需擇一必填 |

### <a name="cancel-c2c-order">取消訂單(7-EVEVEN 超商 C2C)</a>
```bash
$express = Express::cancelC2COrder($data);
```

#### $data 內容說明(array格式)
參數 | 必填 | 名稱 | 類型 | 說明 |
| ------------|---|:----------------------- | :------| :------|
| LogiscisID |✔| 綠界訂單編號 | string(20) |  |
| CVSPaymentNo |✔| 寄貨編號 | string(15) |  |
| CVSValidationNo |✔| 驗證碼 | string(10) |  |
