<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <script src="https://kit.fontawesome.com/164ea36700.js" crossorigin="anonymous"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
        content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4" />
    @include('layouts.head')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Jost:wght@100;300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=Shantell+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0B1A33">
    {{-- ثيم موحّد لكل الصفحات (لازم يبقى آخر CSS) --}}
    <link href="{{ URL::asset('assets/css/ahl-theme.css') }}?v={{ filemtime(public_path('assets/css/ahl-theme.css')) }}" rel="stylesheet">

 
</head>
{{-- style="direction:{{App::getLocale()=='ar'?'rtl':'ltr'}}" --}}
<body class="main-body app sidebar-mini  master ahl-ui" style="direction:{{App::getLocale()=='ar'?'rtl':'ltr'}}"
 >
    <!-- Loader -->
    <div id="global-loader">
        <img src="{{ URL::asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->
    <div class="main-content app-content">
        @include('layouts.main-sidebar')
        <!-- main-content -->
        @include('layouts.main-header')
        <!-- container -->
        <div class="container-fluid">
            @yield('page-header')
            @yield('content')
            @include('layouts.sidebar')
            @include('layouts.models')
            @include('layouts.footer')
            @include('layouts.footer-scripts')
            <script>window.AHL_HOME_URL = "{{ url('dashboard') }}";</script>
            <script src="{{ URL::asset('assets/js/ahl-theme.js') }}?v={{ filemtime(public_path('assets/js/ahl-theme.js')) }}"></script>
        </div>
    </div>        
</body>

</html>

<script>
 
setInterval(myTimer, 1000);

function myTimer() {
    
    
      const d = new Date();
           currenttime = d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
                       if(currenttime=="16:25:20"){
            window.open("{{ URL::to('our_backup_database') }}",'_blank');
           alert('تنبية : جاري تحميل النسخة الاحتياطية الان          \n   Attention: Downloading the backup now')
             }
    
}
</script>

