@extends('partials.layout_min')

@push('body_attr')
    class="marketplace"
@endpush

@section('body')
	@yield('style')
	@yield('wrapper')
@stop

@push('js')
<script src="/vendor/jquery/jquery-ui.js"></script>
<script>
	$(function() {
		if($(window).width() > 767) {
			var $row = $(".genre-wrapper .search-filter-result .row");
			var slideWidth = $row.width();
			var width = 0;
			var i;
			var $imgItem = $(".genre-wrapper .search-filter-result .playlist-main")

			if($(window).width() > 991) {
				i = $imgItem.length % 4 == 0 ? $imgItem.length / 4 - 1 : Math.floor($imgItem.length / 4);
			} else {
				i = $imgItem.length % 2 == 0 ? $imgItem.length / 2 - 1 : Math.floor($imgItem.length / 2);
			}

			$(".fa-chevron-right").on("click", function () {
				if(width > (-slideWidth * i)) {
					width -= slideWidth;
					$row.animate({left: width}, 1200);
				} else {
					return;
				}
			});

			$(".fa-chevron-left").on("click", function () {
				if(width < 0) {
					width += slideWidth;
					$row.animate({left: width}, 1200);
				} else {
					return;
				}
			});


			$("#searchform-holder .search-field .dropdown-menu.genre-options li>a").on("click", function () {
				var select = $(this).text();
				var $dropdown = $(this).closest(".input-group-btn").find(".dropdown-toggle span");
				$dropdown.text(select);
				$('#genre-value').val(select.toLowerCase());
			})

			$("#searchform-holder .search-field .dropdown-menu.filter-options li>a").on("click", function () {
				var select = $(this).attr('title');
				var $dropdown = $(this).closest(".input-group-btn").find(".dropdown-toggle span");
				$dropdown.text(select);
				$('#filter-value').val(select);
			});
		}
	});
</script>
@endpush

@yield('footerjs')
