@extends('layouts.master')
@section('css')
<style>
.nt-item{ display:flex; gap:12px; align-items:flex-start; padding:14px 16px; border-bottom:1px solid #F0F3F8; text-decoration:none !important; color:#0F172A !important; transition:background .12s; }
.nt-item:hover{ background:#F5F8FF; }
.nt-item.unread{ background:#F8FBFF; }
.nt-ico{ width:40px; height:40px; flex:none; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:20px; }
.nt-title{ font-weight:800; font-size:14px; }
.nt-body{ font-size:13px; color:#475569; margin-top:2px; }
.nt-ago{ font-size:12px; color:#94A3B8; margin-top:4px; }
.nt-dot{ width:9px; height:9px; border-radius:50%; background:#2F6FED; margin-top:6px; margin-inline-start:auto; flex:none; }
</style>
@endsection
@section('title')
الإشعارات
@stop
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0 my-auto">الإشعارات</h4></div>
    <form method="post" action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/notifications/read-all') }}">
        {{ csrf_field() }}<button class="btn btn-secondary btn-sm"><i class="bx bx-check-double"></i> تعليم الكل كمقروء</button>
    </form>
</div>
@endsection
@section('content')
<div class="card"><div class="card-body" style="padding:0 !important">
    @forelse ($items as $n)
        <a class="nt-item {{ $n->read_at ? '' : 'unread' }}" href="{{ url('notifications/' . $n->id . '/open') }}">
            <span class="nt-ico" style="background:{{ $n->color ?: '#2F6FED' }}"><i class="bx {{ $n->icon ?: 'bx-bell' }}"></i></span>
            <div style="min-width:0">
                <div class="nt-title">{{ $n->title }}</div>
                @if ($n->body)<div class="nt-body">{{ $n->body }}</div>@endif
                <div class="nt-ago">{{ \Carbon\Carbon::parse($n->created_at)->locale('ar')->diffForHumans() }} · {{ \Carbon\Carbon::parse($n->created_at)->setTimezone('Asia/Riyadh')->format('Y/m/d h:i A') }}</div>
            </div>
            @if (!$n->read_at)<span class="nt-dot"></span>@endif
        </a>
    @empty
        <div style="text-align:center;color:#94A3B8;padding:50px 0"><i class="bx bx-bell-off" style="font-size:40px"></i><br>مفيش إشعارات لسه</div>
    @endforelse
</div></div>
<div class="d-flex justify-content-center">{{ $items->links() }}</div>
@endsection
