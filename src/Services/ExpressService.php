<?php

namespace Pharaoh\Express\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Pharaoh\Express\Exceptions\ExpressException;

class ExpressService
{
    /**
     * 物流服務的設定參數
     *
     * @var array
     */
    private $settings = [];

    /**
     * 請求參數
     *
     * @var array
     */
    private $requestData = [];

    public function __construct()
    {
        $this->settings = config('express');

        $this->requestData = [
            'MerchantID' => Arr::get($this->settings, 'merchant_id'),
            'RqHeader' => [
                'Timestamp' => now()->timestamp,
                'Revision' => Arr::get($this->settings, 'vision')
            ],
            'Data' => [
                'MerchantID' => Arr::get($this->settings, 'merchant_id'),
            ],
        ];
    }

    /**
     * 一段標測試資料產生(B2C)
     *
     * @param string $type
     * @return array
     * @throws ExpressException
     */
    public function createTestData(string $type): array
    {
        try {
            $this->requestData['Data']['LogisticsSubType'] = $type;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('CreateTestData');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 獲取開啟物流選擇頁連結
     *
     * @param array $data
     * @return string
     * @throws ExpressException
     */
    public function createLogistics(array $data): string
    {
        try {
            $this->requestData['Data']['TempLogisticsID'] = '0';
            $this->requestData['Data']['ServerReplyURL'] = config('app.url') . '/express/temp-trade-reply';
            $this->requestData['Data']['ClientReplyURL'] = config('app.url') . '/express/client-reply';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            return URL::temporarySignedRoute(
                'redirect-to-logistics-selection',
                now()->addSeconds(5),
                $this->requestData
            );
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 開啟物流選擇頁
     *
     * @param array $data
     * @return string
     * @throws ExpressException
     */
    public function redirectToLogisticsSelection(array $data): string
    {
        try {
            $url = config('express.express_url') . 'RedirectToLogisticsSelection';
            return Http::post($url, $data)->body();
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 更新暫存物流訂單
     *
     * @param array $data
     * @return array
     * @throws ExpressException
     */
    public function updateTempTrade(array $data): array
    {
        try {
            $this->requestData['Data']['ServerReplyURL'] = config('app.url') . '/express/temp-trade-reply';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('UpdateTempTrade');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data']['TempLogisticsID'] = $tempLogisticsId;

            $this->requestData['Data'] = $this->encryptData($this->requestData['Data']);

            $responseData = $this->httpRequest('CreateByTempTrade');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            return URL::temporarySignedRoute('print-trade-document', now()->addSeconds(5), $this->requestData);
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 列印託運單
     *
     * @param array $data
     * @return string
     * @throws ExpressException
     */
    public function printTradeDocument(array $data): string
    {
        try {
            $url = config('express.express_url') . 'PrintTradeDocument';
            return Http::post($url, $data)->body();
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data']['ServerReplyURL'] = config('app.url') . '/express/return-trade-reply';
            $this->requestData['Data']['ServiceType'] = '4';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('ReturnUniMartCVS');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data']['ServerReplyURL'] = config('app.url') . '/express/return-trade-reply';
            $this->requestData['Data']['ServiceType'] = '4';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('ReturnCVS');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data']['ServerReplyURL'] = config('app.url') . '/express/return-trade-reply';

            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('ReturnHome');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
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
        try {
            $this->requestData['Data'] = $this->encryptData(array_merge($this->requestData['Data'], $data));

            $responseData = $this->httpRequest('QueryLogisticsTradeInfo');

            // RtnCode !== 1 一律回傳錯誤
            if (Arr::get($responseData, 'RtnCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $responseData;
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 建立暫存物流訂單結果通知
     *
     * @param array $responseData
     * @return array
     * @throws ExpressException
     */
    public function getReplyData(array $responseData): array
    {
        try {
            if (Arr::get($responseData, 'TransCode') !== 1) {
                throw new ExpressException(Arr::get($responseData, 'RtnMsg'));
            }

            return $this->decryptData(Arr::get($responseData, 'Data'));
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * 成功接收的回傳內容
     *
     * @return array
     * @throws ExpressException
     */
    public function successResponse(): array
    {
        try {
            return [
                'MerchantID' => Arr::get($this->settings, 'merchant_id'),
                'RqHeader' => [
                    'Timestamp' => now()->timestamp,
                ],
                'TransCode' => 1,
                'TransMsg' => "",
                "Data" => $this->encryptData(
                    [
                        'RtnCode' => 1,
                        'RtnMsg' => '成功'
                    ]
                )
            ];
        } catch (\Exception $exception) {
            throw new ExpressException($exception->getMessage());
        }
    }

    /**
     * HTTP請求
     *
     * @param string $method
     * @return array
     */
    private function httpRequest(string $method): array
    {
        $url = Arr::get($this->settings, 'express_url') . $method;
        $responseRawData = Http::post($url, $this->requestData)->json();
        return $this->decryptData($responseRawData['Data']);
    }

    /**
     * 加密請求參數
     *
     * @param array $data
     * @return string
     */
    private function encryptData(array $data): string
    {
        // URLEncode 編碼
        $urlEncodeString = urlencode(json_encode($data));

        // AES 加密
        return base64_encode(
            openssl_encrypt($urlEncodeString, 'aes-128-cbc', $this->settings['hash_key'], 1, $this->settings['hash_iv'])
        );
    }

    private function decryptData(string $encryptString): array
    {
        // AES 解密
        $urlEncodeString = openssl_decrypt(
            $encryptString,
            'aes-128-cbc',
            $this->settings['hash_key'],
            0,
            $this->settings['hash_iv']
        );

        // URLDecode 解碼
        return json_decode(urldecode($urlEncodeString), true);
    }
}
