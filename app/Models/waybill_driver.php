<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waybill_driver extends Model
{
    use HasFactory;
    protected $table = 'waybill_drivers';
    protected $fillable = ['name','phone','id_number','license_number','license_issue_date','nationality','notes','user_id'];


    /**
     * توحيد رقم الجوال السعودي لصيغة 05xxxxxxxx
     * بيقبل: 05xxxxxxxx / 5xxxxxxxx / 9665xxxxxxxx / +9665xxxxxxxx / 009665xxxxxxxx
     * بيرجّع null لو الرقم مش صحيح
     */
    public static function normalizePhone($phone)
    {
        $d = preg_replace('/\D+/', '', strtr((string) $phone, ['٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']));
        if (strpos($d, '00966') === 0) $d = substr($d, 5);
        elseif (strpos($d, '966') === 0) $d = substr($d, 3);
        if (strlen($d) == 9 && $d[0] == '5') $d = '0' . $d;
        return preg_match('/^05\d{8}$/', $d) ? $d : null;
    }

    /** رقم الجوال بصيغة دولية للواتساب (05xxxxxxxx => 9665xxxxxxxx) */
    public static function intlPhone($phone)
    {
        $d = preg_replace('/\D+/', '', (string) $phone);
        if ($d === '') return '';
        if (strpos($d, '00') === 0) $d = substr($d, 2);
        if (strpos($d, '966') === 0) return $d;
        if (strpos($d, '05') === 0) return '966' . substr($d, 1);
        if (strlen($d) == 9 && $d[0] == '5') return '966' . $d;
        return $d;
    }

    public function getTelLinkAttribute()
    {
        return $this->phone ? 'tel:' . preg_replace('/[^\d+]/', '', $this->phone) : null;
    }

    public function getWaLinkAttribute()
    {
        $n = self::intlPhone($this->phone);
        return $n ? 'https://wa.me/' . $n : null;
    }
}
