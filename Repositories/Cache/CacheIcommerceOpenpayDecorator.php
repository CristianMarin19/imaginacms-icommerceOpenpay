<?php

namespace Modules\Icommerceopenpay\Repositories\Cache;

use Modules\Core\Repositories\Cache\BaseCacheDecorator;
use Modules\Icommerceopenpay\Repositories\IcommerceOpenpayRepository;

class CacheIcommerceOpenpayDecorator extends BaseCacheDecorator implements IcommerceOpenpayRepository
{
    public function __construct(IcommerceOpenpayRepository $icommerceopenpay)
    {
        parent::__construct();
        $this->entityName = 'icommerceopenpay.icommerceopenpays';
        $this->repository = $icommerceopenpay;
    }

    public function calculate($parameters, $conf)
    {
        return $this->remember(function () use ($parameters, $conf) {
            return $this->repository->calculate($parameters, $conf);
        });
    }
}
