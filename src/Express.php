<?php

namespace Pharaoh\Express;

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
use Pharaoh\Express\Exceptions\ExpressException;
use Pharaoh\Express\Http\Controllers\ExpressController;
use Pharaoh\Express\Services\ExpressService;

class Express
{
    /**
     * @var ExpressService
     */
    private $expressService;

    public function __construct(ExpressService $expressService)
    {
        $this->expressService = $expressService;
    }

    /**
     * 註冊物流相關路由
     */
    public function routes()
    {
        Route::prefix('express')->group(
            function () {
                // 開啟物流選擇頁
                Route::get('redirect-to-logistics-selection', [ExpressController::class, 'redirectToLogisticsSelection'])
                    ->name('redirect-to-logistics-selection')
                    ->middleware(ValidateSignature::class);

                // 列印託運單
                Route::get('print-trade-document', [ExpressController::class, 'printTradeDocument'])
                    ->name('print-trade-document')
                    ->middleware(ValidateSignature::class);

                // 暫存物流訂單通知結果
                Route::post('client-reply', [ExpressController::class, 'clientReply']);

                // 物流狀態(貨態)通知結果
                Route::post('server-reply', [ExpressController::class, 'serverReply']);
            }
        );
    }

    /**
     * 一段標測試資料產生(B2C)
     *
     * @param string $type
     * @return array
     * @throws Exceptions\ExpressException
     */
    public function createTestData(string $type): array
    {
        return $this->expressService->createTestData($type);
    }

    /**
     * 獲取開啟物流選擇頁連結
     *
     * @param array $data
     * @return string
     * @throws Exceptions\ExpressException
     */
    public function createLogistics(array $data): string
    {
        $dataRequiredFields = ['GoodsAmount', 'GoodsName', 'SenderName', 'SenderZipCode', 'SenderAddress'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->createLogistics($data);
    }

    /**
     * 更新暫存物流訂單
     *
     * @param array $data
     * @return array
     * @throws Exceptions\ExpressException
     */
    public function updateTempTrade(array $data): array
    {
        $dataRequiredFields = ['TempLogisticsID'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->updateTempTrade($data);
    }

    /**
     * 建立正式物流訂單
     *
     * @param string $tempLogisticsId
     * @return array
     * @throws ExpressException
     */
    public function createByTempTrade(string $tempLogisticsId): array
    {
        return $this->expressService->createByTempTrade($tempLogisticsId);
    }

    /**
     * 建立列印託運單連結
     *
     * @param array $data
     * @return string
     * @throws ExpressException
     */
    public function createTradeDocument(array $data): string
    {
        $dataRequiredFields = ['LogisticsID', 'LogisticsSubType'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->createTradeDocument($data);
    }

    /**
     * (B2C) 7-ELEVEN 逆物流訂單
     *
     * @param array $data
     * @return array
     * @throws ExpressException
     */
    public function returnUniMartCVS(array $data): array
    {
        $dataRequiredFields = ['GoodsAmount', 'SenderName'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->returnUniMartCVS($data);
    }

    /**
     * (B2C) 全家逆物流訂單
     *
     * @param array $data
     * @return array
     * @throws ExpressException
     */
    public function returnFamiCVS(array $data): array
    {
        $dataRequiredFields = ['GoodsAmount', 'SenderName'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->returnFamiCVS($data);
    }

    /**
     * 宅配逆物流訂單
     *
     * @param array $data
     * @return array
     * @throws ExpressException
     */
    public function returnHome(array $data): array
    {
        $dataRequiredFields = ['LogisticsSubType', 'GoodsAmount'];

        $this->checkRequiredFields($dataRequiredFields, $data);

        return $this->expressService->returnHome($data);
    }

    /**
     * 特店進行物流訂單查詢作業
     *
     * @param array $data
     * @return array
     * @throws ExpressException
     */
    public function queryLogisticsTradeInfo(array $data): array
    {
        return $this->expressService->queryLogisticsTradeInfo($data);
    }

    /**
     * 檢查必填欄位
     *
     * @param array $requiredFields
     * @param array $data
     * @throws ExpressException
     */
    private function checkRequiredFields(array $requiredFields, array $data)
    {
        $requiredFields = array_diff($requiredFields, array_keys($data));
        if (!empty($requiredFields)) {
            throw new ExpressException('必填欄位: ' . implode(',', $requiredFields) . ' 未填入');
        }
    }
}
