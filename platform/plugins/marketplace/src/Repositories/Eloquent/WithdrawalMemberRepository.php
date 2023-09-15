<?php

namespace Botble\Marketplace\Repositories\Eloquent;

use Botble\Ecommerce\Models\MemberWithdrawal;
use Botble\Marketplace\Repositories\Interfaces\MemberWithdrawalInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class WithdrawalMemberRepository extends RepositoriesAbstract implements MemberWithdrawalInterface
{
    public function __construct(MemberWithdrawal $model)
    {
        $this->model = $model;
    }
}
