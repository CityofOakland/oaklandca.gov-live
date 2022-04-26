import jQuery from 'jquery'

function bindGoogleEvents($){
  // GA Events
  $('#all-hits').on('click', 'a', function(){
      var search_term   = $('#search-input').val() ? $('#search-input').val() : 'No search term';
      var link_clicked  = $(this).attr('href').trim();

      var filters = [];

      dl.push({
          'event'               : 'search_result_clicked',
          'current_url'         : window.location.href,
          'url'                 : link_clicked,
          'input_search_term'   : search_term,
          'filters'             : filters,
          'debug_mode'          : true,
          'search_bar_id'       : 'index'
      });
  });

  $('.breadcrumbs a').on('click', function(){
      var link_clicked    = $(this).attr('href').trim();
      var link_label      = $(this).text().trim().replace(/(\r\n|\n|\r)/gm, "");

      dl.push({
          'event'         : 'breadcrumb_clicked',
          'current_url'   : window.location.href,
          'url'           : link_clicked,
          'label'         : link_label,
          'debug_mode'    : true,
      });
  });

  $('.components table').each(function(index, element){
      var $table   = $(element);
      var $heads  = $table.find('th');

      $heads.each(function(index, element){
        var $heading      = $(element);

        $table.find('td:nth-of-type(' + (index + 1) + ')').attr('data-label', $heading.text());
      });
  });

  $('.components').addClass('js-initialized');

  $('#hits').on('click', 'a', function(){
      var search_term   = $('#search-input').val() ? $('#search-input').val() : 'No search term';
      var link_clicked  = $(this).attr('href').trim();

      var filters = [];

      $('.facet-container > div:visible').each(function(index, element){
          var $this = $(element);
          var item = {
              label : $this.find('.ais-header').text(),
              value : $this.find('select').val() ? $this.find('select').val() : 'N/A',
          };

          filters.push(item);
      });

      dl.push({
          'event'               : 'search_result_clicked',
          'current_url'         : window.location.href,
          'url'                 : link_clicked,
          'input_search_term'   : search_term,
          'filters'             : filters,
          'debug_mode'          : true,
          'search_bar_id'       : 'instant-search'
      });
  });


  $('footer a').on('click', function(){
      var link_clicked    = $(this).attr('href').trim();
      var link_label      = $(this).text().trim();

      dl.push({
          'event'         : 'footer_clicked',
          'current_url'   : window.location.href,
          'url'           : link_clicked,
          'label'         : link_label,
          'debug_mode'    : true,
      });
  });

  $('#nav-wrapper a').on('click', function(){
      var link_clicked    = $(this).attr('href').trim();
      var link_label      = $(this).text().trim().replace(/(\r\n|\n|\r)/gm, "");

      dl.push({
          'event'         : 'topnav_clicked',
          'current_url'   : window.location.href,
          'url'           : link_clicked,
          'label'         : link_label,
          'debug_mode'    : true,
      });
  });

  $('#site-search-form').on('submit', function(){
      var search_term = $('#site-search').val();

      dl.push({
          'event'                 : 'site_search_clicked',
          'current_url'           : window.location.href,
          'input_search_term'     : search_term,
          'debug_mode'            : true,
      });
  });

  $('.subnav-toggler').on('click', function(){
      $(this).parent().siblings('.subnav').toggle('state-active');
  });
}

bindGoogleEvents(jQuery);