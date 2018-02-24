;(function ($, window, undefined) {
  'use strict';

  var $doc = $(document),
      Modernizr = window.Modernizr;

  $(document).ready(function() {
    $.fn.foundationAlerts           ? $doc.foundationAlerts() : null;
    $.fn.foundationButtons          ? $doc.foundationButtons() : null;
    $.fn.foundationAccordion        ? $doc.foundationAccordion() : null;
    $.fn.foundationNavigation       ? $doc.foundationNavigation() : null;
    $.fn.foundationTopBar           ? $doc.foundationTopBar() : null;
    $.fn.foundationCustomForms      ? $doc.foundationCustomForms() : null;
    $.fn.foundationMediaQueryViewer ? $doc.foundationMediaQueryViewer() : null;
    $.fn.foundationTabs             ? $doc.foundationTabs({callback : $.foundation.customForms.appendCustomMarkup}) : null;
    $.fn.foundationTooltips         ? $doc.foundationTooltips() : null;
    $.fn.foundationMagellan         ? $doc.foundationMagellan() : null;
    $.fn.foundationClearing         ? $doc.foundationClearing() : null;

    // $('input, textarea').placeholder(); mac_
		
		$('#publish_date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#expired_date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#start_date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#end_date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#datatable').DataTable( {
        "iDisplayLength": 25
    } );
      var url = window.location.href;
      $("table#cttdrive").bind("click",function() {
          window.location = url.replace(/\/[^\/]*$/, '/test_drive_reserve');
      });
      $("table#chservice").bind("click",function() {
          window.location = url.replace(/\/[^\/]*$/, '/sreserve');
      });
      $("table#cwservice").bind("click",function() {
          window.location = url.replace(/\/[^\/]*$/, '/sreserve');
      });
      $("table#ctcomplain").bind("click",function() {
          window.location = url.replace(/\/[^\/]*$/, '/tech_complain');
      });
      $("table#ctconsult").bind("click",function() {
          window.location = url.replace(/\/[^\/]*$/, '/tech_consult');
      });
		
		$("#login-form").validate();

		base_url = base_url;
		
		$("#colapse-sidebar").click(function(){
			if ($(".nav-bar").hasClass('collapsed')) {
			//if ($("#nav-bar").css("margin-left")=="0") {
				$(".nav-bar").removeClass('collapsed');
				$(".nav-bar").addClass('expanded',1000);
				//$(".nav-bar").css("margin-left","14px");
				$("#col-ex").html("&lt;&lt;&lt;");
			} else {
				$(".nav-bar").removeClass('expanded');
				$(".nav-bar").addClass('collapsed',1000);
				//$(".nav-bar").css("margin-left","0");
				$("#col-ex").html("&gt;&gt;&gt;");
			}
		});
		
  });

  // UNCOMMENT THE LINE YOU WANT BELOW IF YOU WANT IE8 SUPPORT AND ARE USING .block-grids
  // $('.block-grid.two-up>li:nth-child(2n+1)').css({clear: 'both'});
  // $('.block-grid.three-up>li:nth-child(3n+1)').css({clear: 'both'});
  // $('.block-grid.four-up>li:nth-child(4n+1)').css({clear: 'both'});
  // $('.block-grid.five-up>li:nth-child(5n+1)').css({clear: 'both'});

  // Hide address bar on mobile devices (except if #hash present, so we don't mess up deep linking).
  if (Modernizr.touch && !window.location.hash) {
    $(window).load(function () {
      setTimeout(function () {
        window.scrollTo(0, 1);
      }, 0);
    });
  }

})(jQuery, this);

// $(document).foundationAlerts();  //mac_
// $(document).foundationButtons();
// $(document).foundationAccordion();
// $(document).foundationNavigation();
// $(document).foundationTopBar();
//$(document).foundationCustomForms();
//$(document).foundationMediaQueryViewer();
//$(document).foundationTabs();
//$(document).foundationTooltips();
//$('input, textarea').placeholder();
