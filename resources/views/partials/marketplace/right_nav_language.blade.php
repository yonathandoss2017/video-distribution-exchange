<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
	@if(session()->get('locale') == 'zh')
    <img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文<i class="fa fa-caret-down" aria-hidden="true"></i>
  @else
    <img class="language-img" src="/images/english.png">&nbsp;&nbsp;ENGLISH<i class="fa fa-caret-down" aria-hidden="true"></i>
  @endif

</a>
<ul class="dropdown-menu">
	<li><a href="{{ route('ivx.setLocale','zh') }}"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></li>
	<li><a href="{{ route('ivx.setLocale','en') }}"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;ENGLISH</a></li>
</ul>
