jQuery(document).ready(function($) {

  // window.parent.caches.delete("call");
  window.base_url = window.location.origin + '/app/';
  window.image_url = window.location.origin + '/';
  window.base_loading_image = '<img src="'+ image_url +'assets/images/loading.svg" width="32" height="32" alt=""/>';


  display_calendar_single();
  // display_calendar_group();
  // getExistingAppointments();

  function display_calendar_single() {
    $("#calendar_page_container").html("");
    $.ajax({
      url: base_url+'clinic/calendar-view-single',
      // url: base_url+'clinic/calendar-view-group',
      type: 'GET',
    })
    .done(function(data) {
      $("#calendar_page_container").html(data);
      window.localStorage.setItem('search_log_event', true);
    })
  }

  function display_calendar_group() {
    $("#calendar_page_container").html("");
    $.ajax({
      url: base_url+'clinic/calendar-view-group',
      type: 'GET',
    })
    .done(function(data) {
      $("#calendar_page_container").html(data);

    })
  }

  function getExistingAppointments( ) {
    setTimeout(function() {
      $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: 'http://medicloud.dev/app/get_existing_appointments/4332',
                dataType: 'json',
                method: 'get'
            }).done(function (response) {
                self.setContent('Description: ' + response);
                // self.setContentAppend('<br>Version: ' + response.version);
                // self.setTitle(response.name);
            }).fail(function(){
                self.setContent('Something went wrong.');
            });
        }
      });
      
    }, 3000);
  }

})
