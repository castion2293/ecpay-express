<?php

namespace Pharaoh\Express;

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Route;
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
        return $this->expressService->createLogistics($data);
    }
}
