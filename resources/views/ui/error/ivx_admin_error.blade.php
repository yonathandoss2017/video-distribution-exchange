@extends('ivx_admin.layout')
@push('title') {{ __('app.title') }} @endpush
@section('content')
  <div class="container">
    <!-- error 显示主要代码 -->
    <div class="text-center page-error">
      <p class="page-error-type"><i class="fa fa-exclamation-triangle"></i>ERROR</p>
      <span>An error ocurred, please try again later.</span>
      <!-- 404
        Sorry, the page you are looking for was not found.
        对不起，页面没有找到 -->
      <!-- 403
        Sorry, you do not have access to this page.
        对不起，您没有权限访问此页面 -->
      <!-- 500
        An error ocurred, please try again later.
        出错了，请稍后再试 -->
    </div>
    <!-- error 显示主要代码 end -->
  </div>
@stop
