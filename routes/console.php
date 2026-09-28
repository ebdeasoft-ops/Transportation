<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
| توليد مفاتيح Web Push (VAPID) وكتابتها في .env مباشرة
|   php artisan webpush:keys          (لو المفاتيح موجودة مش هيغيّرها)
|   php artisan webpush:keys --force  (يولّد مفاتيح جديدة - الأجهزة المشتركة لازم تفعّل تاني)
| بيشتغل على XAMPP ويندوز من غير ما تظبط OPENSSL_CONF لأنه بيدّي مسار openssl.cnf بنفسه
*/
Artisan::command('webpush:keys {--force}', function () {
    $envFile = base_path('.env');
    $env = is_file($envFile) ? file_get_contents($envFile) : '';

    if (!$this->option('force') && preg_match('/^VAPID_PRIVATE_KEY=\S+/m', $env)) {
        $this->warn('المفاتيح موجودة بالفعل في .env - لو عايز تغيّرها استخدم --force');
        return 0;
    }

    $opts = ['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1'];
    if (DIRECTORY_SEPARATOR === '\\') {
        foreach ([
            getenv('OPENSSL_CONF') ?: null,
            'C:\\xampp\\apache\\conf\\openssl.cnf',
            'C:\\xampp\\php\\extras\\ssl\\openssl.cnf',
            dirname(PHP_BINARY) . '\\extras\\ssl\\openssl.cnf',
            dirname(PHP_BINARY, 2) . '\\apache\\conf\\openssl.cnf',
        ] as $f) {
            if ($f && is_file($f)) { $opts['config'] = $f; break; }
        }
    }

    $key = @openssl_pkey_new($opts);
    if (!$key) {
        $this->error('فشل إنشاء المفتاح: ' . (openssl_error_string() ?: 'openssl.cnf مش موجود'));
        return 1;
    }
    $det = openssl_pkey_get_details($key);
    $pad = function ($b) { return str_pad($b, 32, "\0", STR_PAD_LEFT); };
    $b64 = function ($b) { return rtrim(strtr(base64_encode($b), '+/', '-_'), '='); };
    $public  = $b64("\x04" . $pad($det['ec']['x']) . $pad($det['ec']['y']));
    $private = $b64($pad($det['ec']['d']));

    $set = function ($env, $name, $value) {
        $line = $name . '=' . $value;
        if (preg_match('/^' . $name . '=.*$/m', $env)) {
            return preg_replace('/^' . $name . '=.*$/m', $line, $env);
        }
        return rtrim($env) . PHP_EOL . $line . PHP_EOL;
    };
    $env = $set($env, 'VAPID_PUBLIC_KEY', $public);
    $env = $set($env, 'VAPID_PRIVATE_KEY', $private);
    if (!preg_match('/^VAPID_SUBJECT=\S+/m', $env)) {
        $env = $set($env, 'VAPID_SUBJECT', 'mailto:info@ahdlogistc.com');
    }
    file_put_contents($envFile, $env);
    $this->call('config:clear');

    $this->info('تم حفظ مفاتيح VAPID في .env');
    $this->line('Public key: ' . $public);
    $this->line('المفتاح الخاص اتحفظ في .env بس - متبعتهوش لحد ومتحطهوش على GitHub');
    return 0;
})->purpose('Generate Web Push VAPID keys into .env');
