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

// function setFilterCommittees($) {
//   var $public_body  = $('select[name="public_body"]');
//   var $subcommittee = $('select[name="subcommittee"]');

//   $public_body.on('change', function(){
//     var $this = $(this);
//     var $options = $this.find(":selected");
//     var options = $options.data('subcommittees');

//     $subcommittee.find('option:not(:first-child)').remove();

//     if(options.length) {
//       $subcommittee.removeAttr('disabled');

//       for(var i = 0; i < options.length; i++) {
//         var $option = $('<option>')
//           .attr('value', options[i]['id'])
//           .text(options[i]['text']);

//         $subcommittee.append($option);
//       }
//     } else {
//       $subcommittee.attr('disabled', true);
//     }

//   });
// }

// setFilterCommittees(jQuery);
setFilterRules(jQuery);

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
