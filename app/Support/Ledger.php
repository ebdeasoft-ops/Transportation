<?php

namespace App\Support;

use App\Models\credittransactions;
use App\Models\financial_accounts;
use Carbon\Carbon;

/**
 * سطر قيد في دفتر الحركات (credittransactions) + تحديث رصيد الحساب
 * بنفس طريقة النظام: current_balance بيزيد في اتجاه طبيعة الحساب
 */
class Ledger
{
    /**
     * @param string $side  debit | credit
     * @param bool   $natureDebit  الحساب طبيعته مدينة (أصول / مصروفات / عملاء)
     */
    public static function entry($accountId, $side, $amount, $note, $natureDebit, array $extra = [])
    {
        $acc = financial_accounts::lockForUpdate()->find($accountId);
        if (!$acc || $amount <= 0) return null;
        $debit  = $side === 'debit' ? $amount : 0;
        $credit = $side === 'credit' ? $amount : 0;
        $newBalance = (float) $acc->current_balance + ($natureDebit ? ($debit - $credit) : ($credit - $debit));

        $acc->current_balance  = round($newBalance, 2);
        $acc->debtor_current   = round((float) $acc->debtor_current + $debit, 2);
        $acc->creditor_current = round((float) $acc->creditor_current + $credit, 2);
        $acc->save();

        return credittransactions::create($extra + [
            'user_id'         => Auth()->user()->id ?? 1,
            'customer_id'     => $acc->id,
            'recive_amount'   => $amount,
            'branchs_id'      => Auth()->user()->branchs_id ?? 1,
            'pay_method'      => 'Cash',
            'Pay_Method_Name' => 'نقدي',
            'note'            => $note,
            'currentblance'   => round($newBalance, 2),
            'created_at'      => Carbon::now()->addHours(3),
            'updated_at'      => Carbon::now()->addHours(3),
            'orginal_id'      => 0,
            'debtor'          => $debit,
            'creditor'        => $credit,
        ]);
    }

    /** حساب فرعي بالاسم تحت أب معين (بيتعمل لو مش موجود) */
    public static function childAccount($name, $parentId, $fallbackId = null)
    {
        $acc = financial_accounts::where('name', $name)->where('is_parent', 0)->first();
        if ($acc) return $acc;
        $parent = financial_accounts::find($parentId);
        if (!$parent) return $fallbackId ? financial_accounts::find($fallbackId) : null;
        $last = financial_accounts::where('parent_account_number', $parent->id)->max('account_number');
        return financial_accounts::create([
            'name'                  => $name,
            'account_type'          => financial_accounts::where('parent_account_number', $parent->id)->value('account_type') ?: $parent->account_type,
            'is_parent'             => 0,
            'parent_account_number' => $parent->id,
            'account_number'        => $last ? $last + 1 : $parent->account_number * 10 + 1,
            'start_balance_status'  => 3,
            'start_balance'         => 0,
            'current_balance'       => 0,
            'notes'                 => 'اتعمل تلقائياً من نظام النقل',
            'active'                => 1,
            'added_by'              => Auth()->user()->id ?? 1,
            'date'                  => Carbon::now('Asia/Riyadh')->toDateString(),
            'com_code'              => 0,
        ]);
    }
}
