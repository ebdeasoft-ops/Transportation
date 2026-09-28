<?php

use Illuminate\Database\Migrations\Migration;

/**
 * فواتير النقل الضريبية + جداول الموارد البشرية
 * (نفس الجداول بتتعمل تلقائياً أول ما تفتح الشاشات عن طريق App\Support\AppSchema،
 *  الملف ده موجود لو حبيت تشغّل php artisan migrate)
 */
return new class extends Migration
{
    public function up()
    {
        \App\Support\AppSchema::transport();
        \App\Support\AppSchema::hr();
    }

    public function down()
    {
        // مفيش حذف تلقائي للجداول عشان البيانات
    }
};
