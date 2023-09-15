<?php

namespace Botble\Marketplace\Repositories\Eloquent;

use Botble\Ecommerce\Models\MemberPaket;
use Botble\Marketplace\Repositories\Interfaces\MemberPaketInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class MemberPaketRepository extends RepositoriesAbstract implements MemberPaketInterface
{
    public function __construct(MemberPaket $model)
    {
        $this->model = $model;
    }
}
