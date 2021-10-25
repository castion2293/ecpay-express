<?php

namespace Pharaoh\Express\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Pharaoh\Express\Exceptions\ExpressException;

class ExpressService
{
    /**
     * 物流服務的設定參數
     *
     * @var array
     */
    private array $settings = [];

    /**
     * 請求參數
     *
     * @var array
     */
    private array $requestData = [];

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
        $urlEncodeString = openssl_decrypt($encryptString, 'aes-128-cbc', $this->settings['hash_key'], 0, $this->settings['hash_iv']);

        // URLDecode 解碼
        return json_decode(urldecode($urlEncodeString), true);
    }
}
