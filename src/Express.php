<?php

namespace Pharaoh\Express;

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
}
