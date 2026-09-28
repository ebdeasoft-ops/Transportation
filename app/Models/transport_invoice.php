<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** فاتورة نقل ضريبية */
class transport_invoice extends Model
{
    protected $table = 'transport_invoices';

    const ACTIVE    = 1;
    const CANCELLED = 2;

    protected $fillable = [
        'invoice_no', 'customer_account_id', 'customer_name', 'customer_vat', 'customer_cr', 'customer_address', 'customer_phone',
        'issue_date', 'supply_from', 'supply_to', 'subtotal', 'discount', 'taxable', 'vat_rate', 'vat_amount', 'total',
        'prices_include_vat', 'po_number', 'notes', 'status', 'posted', 'cancelled_at', 'cancel_reason', 'cancelled_by',
        'user_id', 'branchs_id', 'vat_category', 'vat_exempt_code', 'vat_exempt_reason',
    ];

    /** أسباب النسبة الصفرية (أكواد هيئة الزكاة) */
    const ZERO_REASONS = [
        'VATEX-SA-34-1' => 'النقل الدولي للسلع',
        'VATEX-SA-34-2' => 'النقل الدولي للركاب',
        'VATEX-SA-34-3' => 'الخدمات المرتبطة مباشرة أو عرضياً بخدمة النقل الدولي للركاب',
        'VATEX-SA-34-4' => 'توريد وسائل النقل المؤهلة',
        'VATEX-SA-34-5' => 'السلع أو الخدمات المرتبطة بتوريد وسائل النقل المؤهلة',
        'VATEX-SA-32'   => 'صادرات السلع',
        'VATEX-SA-33'   => 'صادرات الخدمات',
    ];

    protected $casts = [
        'issue_date'   => 'datetime',
        'supply_from'  => 'date',
        'supply_to'    => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function items()    { return $this->hasMany(transport_invoice_item::class, 'transport_invoice_id'); }
    public function trips()    { return $this->hasMany(truck_trip::class, 'transport_invoice_id'); }
    public function user()     { return $this->belongsTo(User::class, 'user_id'); }
    public function account()  { return $this->belongsTo(financial_accounts::class, 'customer_account_id'); }
    public function branch()   { return $this->belongsTo(branchs::class, 'branchs_id'); }

    /** فاتورة ضريبية (عميل له رقم ضريبي 15 خانة) ولا مبسطة */
    public function getIsStandardAttribute()
    {
        return strlen(preg_replace('/\D/', '', (string) $this->customer_vat)) == 15;
    }

    /** فاتورة بنسبة صفرية؟ */
    public function getIsZeroRatedAttribute()
    {
        return $this->vat_category === 'Z';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status == self::CANCELLED;
    }

    /** QR هيئة الزكاة (المرحلة الأولى - TLV Base64) */
    public function zatcaQr($sellerName, $sellerVat)
    {
        $tlv = function ($tag, $value) {
            $value = (string) $value;
            return chr($tag) . chr(strlen($value)) . $value;
        };
        $time = $this->issue_date ? $this->issue_date->format('Y-m-d\TH:i:s') : '';
        return base64_encode(
            $tlv(1, $sellerName) . $tlv(2, $sellerVat) . $tlv(3, $time)
            . $tlv(4, number_format((float) $this->total, 2, '.', ''))
            . $tlv(5, number_format((float) $this->vat_amount, 2, '.', ''))
        );
    }
}
