/**
 * Date Input module for Craft CMS
 *
 * Date Input JS
 *
 * @author    meetgoat
 * @copyright Copyright (c) 2022 meetgoat
 * @link      https://meetgoat.com
 * @package   DateInputModule
 * @since     1.0.0
 */

var $start_date   = $('input[name="start_date"]');
var $end_date     = $('input[name="end_date"]');

var config = {
  OnAfterChooseMonth: setMonthMinMax,
  MonthFormat: 'yy-mm',
  Position: {collision: 'fit flip'},
  ShowOn: 'both'
};

$start_date.MonthPicker(config);
$end_date.MonthPicker(config);

var $date_type = $('select[name="type"]');

$date_type.on('change', function(){
  $start_date.val('');
  $end_date.val('');
  setMonthMinMax();
});

function setMonthMinMax() {
  if($date_type) {
    var value = parseInt($date_type.val());
    switch(value){
      case 1:
        $start_date.MonthPicker('option', 'MinMonth', 0);
        $start_date.MonthPicker('option', 'MaxMonth', null);
        $end_date.MonthPicker('option', 'MinMonth', 0);
        $end_date.MonthPicker('option', 'MaxMonth', null);
        break;
      case 2:
        $start_date.MonthPicker('option', 'MinMonth', null);
        $start_date.MonthPicker('option', 'MaxMonth', null);
        $end_date.MonthPicker('option', 'MinMonth', null);
        $end_date.MonthPicker('option', 'MaxMonth', null);
        break;
      case 3:
        $start_date.MonthPicker('option', 'MinMonth', null);
        $start_date.MonthPicker('option', 'MaxMonth', 0);
        $end_date.MonthPicker('option', 'MinMonth', null);
        $end_date.MonthPicker('option', 'MaxMonth', 0);
        break;
      default:
        $start_date.MonthPicker('option', 'MinMonth', 0);
        $start_date.MonthPicker('option', 'MaxMonth', null);
        $end_date.MonthPicker('option', 'MinMonth', 0);
        $end_date.MonthPicker('option', 'MaxMonth', null);
    }
  }

  // Start date must never be greater than the set end date.
  if($end_date.val()) $start_date.MonthPicker('option', 'MaxMonth', $end_date.val());

  // End date must never be lesser than the set start date.
  if($start_date.val()) $end_date.MonthPicker('option', 'MinMonth', $start_date.val());
}

setMonthMinMax();