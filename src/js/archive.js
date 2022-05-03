import jQuery from 'jquery'

function zeroPad(num, places) {
  var zero = places - num.toString().length + 1;
  return Array(+(zero > 0 && zero)).join("0") + num;
}

function setFilterRules($){
  var $type         = $('input[name="type"]');

  var d             = new Date();
  var currentMonth  = (d.getMonth() + 1);
  var currentYear   = d.getFullYear();

  var $month = $('[name="month"]');
  var $year  = $('[name="year"]');

  if($month.length && $year.length && $type.length){

    var getInputDate = function(){
      return $year.val() + zeroPad($month.val(), 2);
    }

    var currentDate = currentYear + zeroPad(currentMonth, 2);

    $month.on('change', function(){
      if($year.val()){
        // Check with entire value
        if(parseInt(getInputDate()) < parseInt(currentDate)){
          $type.val('past');
        } else {
          $type.val('upcoming');
        }

      } else {
        // Assume it is current year
        if(parseInt(this.value, 10) < currentMonth) {
          $type.val('past');
        } else {
          $type.val('upcoming');
        }
      }
    });

    $year.on('change', function(){
      if($month.val()){
        // Check with entire value
        if(parseInt(getInputDate()) < parseInt(currentDate)){
          $type.val('past');
        } else {
          $type.val('upcoming');
        }

      } else {
        // Assume it is current month
        if(parseInt(this.value, 10) < currentYear) {
          $type.val('past');
        } else {
          $type.val('upcoming');
        }
      }
    });

    function changeToBlack() {
      if(this.value){
        $(this).css('color', 'black');
      } else {
        $(this).css('color', '');
      }
    }

    $('select').on('change', changeToBlack);
    $('select').each(changeToBlack);
  }
}

setFilterRules(jQuery);

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
