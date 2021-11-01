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

                Route::post('client-reply', [ExpressController::class, 'clientReply']);

                Route::post('server-reply', function () {
                    \Log::info(request()->all());
                });
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
