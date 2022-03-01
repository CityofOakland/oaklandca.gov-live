import 'alpinejs'
import 'picturefill'

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

var menuItems = document.querySelectorAll('.has-submenu, #mobile-navigation');
var menuItemsHover = document.querySelectorAll('.has-submenu:not(.click)');
var menuItemsClick = document.querySelectorAll('.has-submenu.click > a, #mobile-navigation > a');

function closeOtherMenuItems(parent){
  Array.prototype.forEach.call(menuItemsClick, function(el, i){
    if(parent){
      if(el !== document.querySelector(parent)){
        el.parentElement.classList.remove('open');
        el.setAttribute('aria-expanded', "false");
      }
    } else {
      el.parentElement.classList.remove('open');
      el.setAttribute('aria-expanded', "false");
    }
  })
}

// Click
Array.prototype.forEach.call(menuItemsClick, function(el, i){
    el.addEventListener("mousedown", function(event){
      if(this.parentElement.classList.contains('open')){
        this.parentElement.classList.remove('open');
        el.setAttribute('aria-expanded', "false");

      } else {
        closeOtherMenuItems(el.getAttribute('data-parent'));
        this.parentElement.classList.add('open');
        el.setAttribute('aria-expanded', "true");

      }
    });
});

// Hover
Array.prototype.forEach.call(menuItemsHover, function(el, i){
    el.addEventListener("mouseover", function(event){
        this.className = "has-submenu open";
    });
    el.addEventListener("mouseout", function(event){
        document.querySelector(".has-submenu.open").className = "has-submenu";
    });
});

// Keyboard
Array.prototype.forEach.call(menuItems, function(el, i){
    var trigger     = el.querySelector('a[aria-expanded]');
    var triggers    = Array.prototype.slice.call(el.querySelectorAll('a.card, a.button, a.focusable, input, button'));

    Array.prototype.forEach.call(triggers, function(innerTrigger, i){
        innerTrigger.addEventListener('blur', function(event){
            if(triggers.indexOf(event.relatedTarget) == -1){
                el.classList.remove('open');
                trigger.setAttribute('aria-expanded', "false");

            }
        });
    })

    el.addEventListener('keydown', function (event) {
        // var focused         = el.querySelector(':focus');
        var target          = event.target;
        var key             = event.which.toString();
        var ctrlModifier    = (event.ctrlKey && key.match(/33|34/));

        // Press Enter
        if(key == "13"){
          if(trigger.getAttribute("aria-expanded") == "true") {
            el.classList.remove('open');
            trigger.setAttribute('aria-expanded', "false");
          } else {
            trigger.setAttribute('aria-expanded', "true");
            triggers[0].focus();

            if(triggers[0].tagName == 'INPUT') {
              event.preventDefault();
            }
          }
          event.stopPropagation();
        }

        if(trigger.getAttribute('aria-expanded') == "true"){
          if (key.match(/38|40/) || ctrlModifier) {
              var index       = triggers.indexOf(target);
              var direction   = (key.match(/34|40/)) ? 1 : -1;
              var length      = triggers.length;
              var newIndex    = (index + length + direction) % length;

              if(index == -1) {
                  triggers[0].focus();
              } else if(direction == 1 && newIndex == 0) {
                  el.classList.remove('open');
                  trigger.setAttribute('aria-expanded', "false");
                  trigger.focus();
              } else if(direction == -1 && newIndex == (length - 1)) {
                  el.classList.remove('open');
                  trigger.setAttribute('aria-expanded', "false");
                  trigger.focus();
              } else {            
                  if(isVisible(triggers[newIndex])){
                    triggers[newIndex].focus();
                    event.stopPropagation();
                  } else {
                    if(direction == 1) {
                      for(var i = newIndex; i < triggers.length; i++){
                        if(isVisible(triggers[i])){
                          triggers[i].focus();
                          break;
                        }

                        if(i == triggers.length - 1) {
                          el.classList.remove('open');
                          trigger.setAttribute('aria-expanded', "false");
                          trigger.focus();
                        }
                      }
                    } else {
                      for(var i = newIndex; i >= 0; i--){
                        if(isVisible(triggers[i])){
                          triggers[i].focus();
                          break;
                        }

                        if(i == 0) {
                          el.classList.remove('open');
                          trigger.setAttribute('aria-expanded', "false");
                          trigger.focus();
                        }
                      }
                    }
                  }
              }
              event.preventDefault();
          } else if (key.match(/35|36/)) {
              switch (key) {
                case '36':
                  triggers[0].focus();
                  break;
                case '35':
                  triggers[triggers.length - 1].focus();
                  break;
              }
              event.preventDefault();
          }
        }
    });
})

function isVisible(elem) {
    if (!(elem instanceof Element)) throw Error('DomUtil: elem is not an element.');
    const style = getComputedStyle(elem);
    if (style.display === 'none') return false;
    if (style.visibility !== 'visible') return false;
    if (style.opacity < 0.1) return false;
    if (elem.offsetWidth + elem.offsetHeight + elem.getBoundingClientRect().height +
        elem.getBoundingClientRect().width === 0) {
        return false;
    }
    const elemCenter   = {
        x: elem.getBoundingClientRect().left + elem.offsetWidth / 2,
        y: elem.getBoundingClientRect().top + elem.offsetHeight / 2
    };
    if (elemCenter.x < 0) return false;
    if (elemCenter.x > (document.documentElement.clientWidth || window.innerWidth)) return false;
    if (elemCenter.y < 0) return false;
    if (elemCenter.y > (document.documentElement.clientHeight || window.innerHeight)) return false;
    let pointContainer = document.elementFromPoint(elemCenter.x, elemCenter.y);
    do {
        if (pointContainer === elem) return true;
    } while (pointContainer = pointContainer.parentNode);
    return false;
}