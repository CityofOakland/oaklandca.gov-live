import 'alpinejs'
import 'picturefill'
import jQuery from 'jquery'

function init() {
  const selectNavs = document.getElementsByClassName('js-select-nav');
    if (selectNavs) {
    Array.from(selectNavs).forEach(select => {
      select.addEventListener("change", selectNav);
    });
  }
  function selectNav(e) {
    window.location.href = e.target.value;
  }
}

//Prevent the function to run before the document is loaded
document.addEventListener("readystatechange", function () {
  if (document.readyState === "complete") {
    init();
  }
});

// Accordion
Array.prototype.slice.call(document.querySelectorAll('.accordion')).forEach(function (accordion) {

  // Allow for multiple accordion sections to be expanded at the same time
  var allowMultiple = accordion.hasAttribute('data-allow-multiple');
  // Allow for each toggle to both open and close individually
  var allowToggle = (allowMultiple) ? allowMultiple : accordion.hasAttribute('data-allow-toggle');

  // Create the array of toggle elements for the accordion group
  var triggers = Array.prototype.slice.call(accordion.querySelectorAll('.accordion-header'));
  var panels = Array.prototype.slice.call(accordion.querySelectorAll('.accordion-panel'));


  accordion.addEventListener('click', function (event) {
    var target = event.target;

    if (target.classList.contains('accordion-header')) {
      // Check if the current toggle is expanded.
      var isExpanded = target.getAttribute('aria-expanded') == 'true';
      var active = accordion.querySelector('[aria-expanded="true"]');

      // without allowMultiple, close the open accordion
      if (!allowMultiple && active && active !== target) {
        // Set the expanded state on the triggering element
        active.setAttribute('aria-expanded', 'false');
        // Hide the accordion sections, using aria-controls to specify the desired section
        document.getElementById(active.getAttribute('aria-controls')).setAttribute('hidden', '');

        // When toggling is not allowed, clean up disabled state
        if (!allowToggle) {
          active.removeAttribute('aria-disabled');
        }
      }

      if (!isExpanded) {
        // Set the expanded state on the triggering element
        target.setAttribute('aria-expanded', 'true');
        // Hide the accordion sections, using aria-controls to specify the desired section
        document.getElementById(target.getAttribute('aria-controls')).removeAttribute('hidden');

        // If toggling is not allowed, set disabled state on trigger
        if (!allowToggle) {
          target.setAttribute('aria-disabled', 'true');
        }
      }
      else if (allowToggle && isExpanded) {
        // Set the expanded state on the triggering element
        target.setAttribute('aria-expanded', 'false');
        // Hide the accordion sections, using aria-controls to specify the desired section
        document.getElementById(target.getAttribute('aria-controls')).setAttribute('hidden', '');
      }

      event.preventDefault();
    }
  });

  // Bind keyboard behaviors on the main accordion container
  accordion.addEventListener('keydown', function (event) {
    var target = event.target;
    var key = event.which.toString();

    var isExpanded = target.getAttribute('aria-expanded') == 'true';
    var allowToggle = (allowMultiple) ? allowMultiple : accordion.hasAttribute('data-allow-toggle');

    // 33 = Page Up, 34 = Page Down
    var ctrlModifier = (event.ctrlKey && key.match(/33|34/));

    // Is this coming from an accordion header?
    if (target.classList.contains('accordion-header')) {
      // Up/ Down arrow and Control + Page Up/ Page Down keyboard operations
      // 38 = Up, 40 = Down
      if (key.match(/38|40/) || ctrlModifier) {
        var index = triggers.indexOf(target);
        var direction = (key.match(/34|40/)) ? 1 : -1;
        var length = triggers.length;
        var newIndex = (index + length + direction) % length;

        triggers[newIndex].focus();

        event.preventDefault();
      }
      else if (key.match(/35|36/)) {
        // 35 = End, 36 = Home keyboard operations
        switch (key) {
          // Go to first accordion
          case '36':
            triggers[0].focus();
            break;
            // Go to last accordion
          case '35':
            triggers[triggers.length - 1].focus();
            break;
        }
        event.preventDefault();

      }

    }
  });

  // These are used to style the accordion when one of the buttons has focus
  accordion.querySelectorAll('.accordion-header').forEach(function (trigger) {

    trigger.addEventListener('focus', function (event) {
      accordion.classList.add('focus');
    });

    trigger.addEventListener('blur', function (event) {
      accordion.classList.remove('focus');
    });

  });

  // Minor setup: will set disabled state, via aria-disabled, to an
  // expanded/ active accordion which is not allowed to be toggled close
  if (!allowToggle) {
    // Get the first expanded/ active accordion
    var expanded = accordion.querySelector('[aria-expanded="true"]');

    // If an expanded/ active accordion is found, disable
    if (expanded) {
      expanded.setAttribute('aria-disabled', 'true');
    }
  }

});

//Navigation

var menuItems = document.querySelectorAll('li.has-submenu');

// Hover
Array.prototype.forEach.call(menuItems, function(el, i){
    el.addEventListener("mouseover", function(event){
        this.className = "has-submenu open";
        clearTimeout(timer);
    });
    el.addEventListener("mouseout", function(event){
        timer = setTimeout(function(event){
            document.querySelector(".has-submenu.open").className = "has-submenu";
        }, 500);
    });
});

// Keyboard
Array.prototype.forEach.call(menuItems, function(el, i){
    var trigger     = el.querySelector('a');
    var triggers    = Array.prototype.slice.call(el.querySelectorAll('a.card'));

    Array.prototype.forEach.call(triggers, function(card, i){
        card.addEventListener('blur', function(event){
            if(triggers.indexOf(event.relatedTarget) == -1){
                trigger.setAttribute('aria-expanded', "false");
            }
        });
    })

    el.addEventListener('keydown', function (event) {
        var focused         = el.querySelector(':focus');
        var target          = event.target;
        var key             = event.which.toString();
        var ctrlModifier    = (event.ctrlKey && key.match(/33|34/));

        // Press Enter
        if(key == "13"){
            if (trigger.getAttribute("aria-expanded") == "true") {
                trigger.setAttribute('aria-expanded', "false");
             } else {
                trigger.setAttribute('aria-expanded', "true");
                triggers[0].focus();
             }
        }

        if (key.match(/38|40/) || ctrlModifier) {
            var index = triggers.indexOf(target);

            var direction   = (key.match(/34|40/)) ? 1 : -1;
            var length      = triggers.length;
            var newIndex    = (index + length + direction) % length;

            if(index == -1) {
                triggers[0].focus();
            } else if(direction == 1 && newIndex == 0) {
                trigger.setAttribute('aria-expanded', "false");
                trigger.focus();
            } else if(direction == -1 && newIndex == (length - 1)) {
                trigger.setAttribute('aria-expanded', "false");
                trigger.focus();
            } else {            
                triggers[newIndex].focus();
            }

            event.preventDefault();

        } else if (key.match(/35|36/)) {
            // 35 = End, 36 = Home keyboard operations
            switch (key) {
              // Go to first accordion
              case '36':
                triggers[0].focus();
                break;
                // Go to last accordion
              case '35':
                triggers[triggers.length - 1].focus();
                break;
            }
            event.preventDefault();

        }
    });
})

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