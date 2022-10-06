var $modalOpen      = $('.modal-open'),
    $modalClose     = $('.modal-close'),
    $modal          = $('#modal'),
    $modalContent   = $('#modalContent');

var $mainContent    = $('#main');

function openModal(){
  $mainContent.attr('aria-hidden', 'true');
  $modal.attr('aria-hidden', 'false');
  $modalClose.focus();
}

function closeModal(){
  $modal.attr('aria-hidden', 'true');
  $mainContent.attr('aria-hidden', 'false');
  $modalOpen.focus();
}

if($modal.length && $modalOpen.length && $modalClose.length){
  $modalOpen.on("click", openModal);
  $modalClose.on("click", closeModal);

  var modalElements = Array.prototype.slice.call($modal[0].querySelectorAll('input, button'));

  $(document).on('keydown', function(event){
    var key = event.which.toString();
    if(key == "27" && $modal.attr('aria-hidden') == 'false') {
      closeModal();
    }

    if(key == "9") {
      if(modalElements.indexOf(document.activeElement) == (modalElements.length - 1)){
        closeModal();
      }
    }
  });

  $modal.on('click', closeModal);

  $modalContent.on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
  });
}