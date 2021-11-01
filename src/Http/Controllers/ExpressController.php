<?php

namespace Pharaoh\Express\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pharaoh\Express\Services\ExpressService;

class ExpressController extends BaseController
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
     * 開啟物流選擇頁
     *
     * @param Request $request
     * @return string
     * @throws \Pharaoh\Express\Exceptions\ExpressException
     */
    public function redirectToLogisticsSelection(Request $request): string
    {
        return $this->expressService->redirectToLogisticsSelection($request->all());
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Pharaoh\Express\Exceptions\ExpressException
     */
    public function printTradeDocument(Request $request):string
    {
        return $this->expressService->printTradeDocument($request->all());
    }

    /**
     * 建立暫存物流訂單結果通知
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Pharaoh\Express\Exceptions\ExpressException
     */
    public function clientReply(Request $request)
    {
        $resultData = $this->expressService->clientReply(json_decode($request->input('ResultData'), true));
        $resultData = collect($resultData)->reject(
            function ($value, $key) {
                if ($key === 'RtnCode') {
                    return true;
                }

                if ($key === 'RtnMsg') {
                    return true;
                }

                return empty($value);
            }
        )->toArray();

        return view('pharaoh_express::client-reply', $resultData);
    }
}
