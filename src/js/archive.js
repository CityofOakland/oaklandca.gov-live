import jQuery from 'jquery'

function setFilterRules($){
  var type          = $('input[name="type"]').val();

  var d             = new Date();
  var currentMonth  = (d.getMonth() + 1);
  var currentYear   = d.getFullYear();

  var $month = $('[name="month"]');
  var $year  = $('[name="year"]');

  if($month.length && $year.length && type){
    function disableMonths(selectedYear, measurement = 'more') {
      if(selectedYear == currentYear) {
        $month.find('option[value="' + currentYear + '"]').prop('disabled', true);

        $month.find('option').each(function(index, element){
          if(measurement === 'more') {
            if(parseInt(element.value) > currentMonth) {
              $(element).prop('disabled', true);
            }
          } else if (measurement === 'less') {
            if(parseInt(element.value) < currentMonth) {
              $(element).prop('disabled', true);
            }
          }
        });
      } else {
        $month.find('option').prop('disabled', false);
      }
    }

    $month.on('change', function(){
      if(type == 2) {
        if(parseInt(this.value) > currentMonth) {
          $year.find('option[value="' + currentYear + '"]').prop('disabled', true);
        } else {
          $year.find('option').prop('disabled', false);
        }
      } else if(type == 1) {
        if(parseInt(this.value) < currentMonth) {
          $year.find('option[value="' + currentYear + '"]').prop('disabled', true);
        } else {
          $year.find('option').prop('disabled', false);
        }
      }
    });

    if($year.find('option').length > 2) {
      $year.on('change', function(){
        if(type == 2) {
          disableMonths(parseInt(this.value), 'more')
        } else if(type == 1) {
          disableMonths(parseInt(this.value), 'less')
        }
      });
    } else {
      console.log('no change');
      if(type == 2) {
        disableMonths(parseInt(currentYear), 'more')
      } else if(type == 1) {
        disableMonths(parseInt(currentYear), 'less')
      }
    }
  }
}

setFilterRules(jQuery);

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
