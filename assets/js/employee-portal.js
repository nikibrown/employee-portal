/**
 * Handle employee portal js functionality
 * employee-portal.js
 *
 * @package Wordpress Employee Portal
 */
jQuery.noConflict();
jQuery(document).ready(function($){

	$(document.body).on('click', '.js-new-schedule', function(event){
		event.preventDefault();
		$('.js-new-schedule-form').slideToggle();
		if($('.js-new-schedule-form').is(':visible')) {

		}
	});

	$(document.body).on('click', '.js-portal-confirm', function(event){
		event.preventDefault();
		if(confirm('Are you sure?')) {
			window.location.href = $(this).attr('href');
		}
	});

	$(document.body).on('click', '.js-portal-window', function(event){
		event.preventDefault();
		window.open($(this).attr('href'), 'newwindow', config='height=600, width=800, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=yes, directories=no, status=no');
	});

	if($('#portal_calendar') !== undefined && $('#portal_calendar').length > 0) {
		var thisDay = moment().format('YYYY-MM-DD');
		$('#portal_calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: thisDay,
			defaultView: (typeof viewType !== 'undefined') ? viewType : 'agendaWeek',
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: calEvents,
			eventRender: function(event, element) {
				element.qtip({
					//position: 'left center',
					content: {
						title: event.title,
						text: event.description
					}
				});
			}
		});
	}

	$('.js-portal-datetime').customdatetimepicker();

	$( '.js-portal-color-picker' ).wpColorPicker();

});