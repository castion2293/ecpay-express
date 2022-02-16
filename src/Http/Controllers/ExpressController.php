<?php

namespace Pharaoh\Express\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Pharaoh\Express\Events\ServerReplyEvent;
use Pharaoh\Express\Exceptions\ExpressException;
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
    public function printTradeDocument(Request $request): string
    {
        return $this->expressService->printTradeDocument($request->all());
    }

    /**
     * 暫存物流訂單通知結果
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Pharaoh\Express\Exceptions\ExpressException
     */
    public function clientReply(Request $request)
    {
        $replyData = $this->expressService->getReplyData(json_decode($request->input('ResultData'), true));
        $replyData = collect($replyData)->reject(
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

        return view('pharaoh_express::client-reply', $replyData);
    }

    /**
     * 物流狀態(貨態)通知結果
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ExpressException
     */
    public function tempTradeReply(Request $request)
    {
        $replyData = $this->expressService->getReplyData($request->all());

        // 發送 Server Reply 事件
        ServerReplyEvent::dispatch($replyData, 'temp_trade_reply');

        $successResponseData = $this->expressService->successResponse();

        return response($successResponseData);
    }

    /**
     * 物流狀態(逆物流)通知
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ExpressException
     */
    public function returnTradeReply(Request $request)
    {
        $replyData = $this->expressService->getReplyData($request->all());

        // 發送 Server Reply 事件
        ServerReplyEvent::dispatch($replyData, 'return_trade_reply');

        $successResponseData = $this->expressService->successResponse();

        return response($successResponseData);
    }
}
