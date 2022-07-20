import jQuery from 'jquery'

function updateExternalLinks($){
  var $links = $('main a[target="_blank"]');
  if($links.length > 0) {
    $links.each(function(index, element){
      var $this = $(element);
      $this.attr('aria-label', $this.text() + ' (open in new tab)');

      if($this.find('img').length == 0 && $this.find('svg').length == 0){
        $this.addClass('external-link');
      }
    });
  }
}

updateExternalLinks(jQuery);

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
