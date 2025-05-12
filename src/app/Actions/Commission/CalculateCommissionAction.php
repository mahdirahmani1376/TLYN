<?php

namespace App\Actions\Commission;

use App\Models\Commission;

class CalculateCommissionAction
{
    public function execute($amount, $rialPrice)
    {
        $commission = Commission::query()
            ->where('min_amount', '>=', $amount)
            ->first();

        $totalCommissionAmount = ($amount * $rialPrice) * ($commission->rate / 100);

        if ($totalCommissionAmount < $commission->min_fee) {
            $totalCommissionAmount = $commission->min_fee;
        } else if ($totalCommissionAmount > $commission->max_fee) {
            $totalCommissionAmount = $commission->max_fee;
        }

        return $totalCommissionAmount;
    }
}
