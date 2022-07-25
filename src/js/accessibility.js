import jQuery from 'jquery'

function updateExternalLinks($){
  var $links = $('a[target="_blank"]');
  if($links.length > 0) {
    $links.each(function(index, element){
      var $this = $(element);
      $this.attr('aria-label', $this.text() + ' (open in new tab)');

      if(!$this.hasClass('.btn')){
        if($this.find('img').length == 0 && $this.find('svg').length == 0){
          // $this.addClass('external-link');
          $this.append('<i class="fa-regular fa-arrow-up-right-from-square ml-1"></i>');
        }
      }
    });
  }
}

function mobileTables($){
  $('.components table').each(function(index, element){
      var $table   = $(element);
      var $heads  = $table.find('th');

      $heads.each(function(index, element){
        var $heading      = $(element);

        $table.find('td:nth-of-type(' + (index + 1) + ')').attr('data-label', $heading.text());
      });
  });

  $('.components').addClass('js-initialized');
}

updateExternalLinks(jQuery);
mobileTables(jQuery);

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
