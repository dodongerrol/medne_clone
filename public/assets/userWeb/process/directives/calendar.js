app.directive('calendarDirective', [
	"$http",
	"serverUrl",
	function directive( $http, serverUrl ) {
		return {
			restrict: "A",
			scope: true,
			link: function link( scope, element, attributeSet ) {
				// console.log("Calendar Directive Runnning !");

				scope.userInfo = function userInfo( ) {
					$http.get(serverUrl.url + 'auth/userprofile')
					.success(function(response){
						scope.user = response;
					})
					.error(function(err){
						
					});
				};

				scope.initializeCalendar = function(){
					$('#calendar').fullCalendar({
					    // put your options and callbacks here
					    defaultView: 'agendaWeek',
					    editable: true,
						selectable: true,
						eventLimit: true, // allow "more" link when too many events
						firstDay: 1 ,
						columnFormat: 'ddd, MMM DD',
						slotLabelInterval: '01:00:00',
						slotDuration : '00:15:00',
						timezone: 'Asia/SingaPore',
						height: 'parent',
						header: {
							left: '',
							// left: 'prev,next today',
							center: '',
							right: ''
							// right: 'agendaDay,agendaTwoDay,agendaWeek,month'
						},
						//// uncomment this line to hide the all-day slot
						allDaySlot: false,
					})
				}

				scope.onLoad = function(){
					$( ".sidebar ul li" ).removeClass('active');
					$( "#calendar_li" ).addClass('active');
					$('.modal').modal('hide');
				}

				scope.getCalendar = function(){
					$('#calendar').fullCalendar('getView');
				}
				scope.userInfo( );
				scope.initializeCalendar();
				scope.onLoad();
			}
		}
	}
]);
