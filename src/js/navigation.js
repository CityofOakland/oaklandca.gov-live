//Navigation

var menuItems       = document.querySelectorAll('.has-submenu, #mobile-navigation');
var menuItemsHover  = document.querySelectorAll('.has-submenu:not(.click)');
var menuItemsClick  = document.querySelectorAll('.has-submenu.click > a, #mobile-navigation > a');
var menusResizable  = document.querySelectorAll('#main-navigation .has-submenu > ul');

var menuItemClicked = false;

function closeOtherMenuItems(parent){
  Array.prototype.forEach.call(menuItemsClick, function(el, i){
    if(parent){
      if(el !== document.querySelector(parent)){
        el.parentElement.classList.remove('open');
        el.setAttribute('aria-expanded', "false");

        if(el.getAttribute('data-affects') && el.getAttribute('data-affects-class')){
          document.getElementById(el.getAttribute('data-affects')).classList.remove(el.getAttribute('data-affects-class'));
        }
      }
    } else {
      el.parentElement.classList.remove('open');
      el.setAttribute('aria-expanded', "false");

      if(el.getAttribute('data-affects') && el.getAttribute('data-affects-class')){
        document.getElementById(el.getAttribute('data-affects')).classList.remove(el.getAttribute('data-affects-class'));
      }
    }
  })
}

function clearOverlay(){
  document.querySelector('#topbar').classList.remove('state-active');
}

function toggleOverlay(){
  if(document.querySelectorAll('#topbar .open').length > 0) {
    document.querySelector('#topbar').classList.add('state-active');
  } else {
    document.querySelector('#topbar').classList.remove('state-active');
  }
}

// TODO: Remove if deployed on production, was a feature request that turned out buggy in the actual use case.

// function resizeMenus(){
//   var height = 0;
//
//   Array.prototype.forEach.call(menusResizable, function(el, i){
//     // Reset height
//     el.style.minHeight = "0px";
//     if(el.clientHeight > height) {
//       height = el.clientHeight;
//     }
//   });
//
//   Array.prototype.forEach.call(menusResizable, function(el, i){
//     el.style.minHeight = height + "px";
//   });
// }
//
// window.addEventListener('resize', function(){
//   console.log('resizing menus');
//   resizeMenus();
// })
//
// resizeMenus();

// Click
Array.prototype.forEach.call(menuItemsClick, function(el, i){
    el.addEventListener("click", function(event){
      if(this.parentElement.classList.contains('open')){
        this.parentElement.classList.remove('open');
        el.setAttribute('aria-expanded', "false");

        if(el.getAttribute('data-affects') && el.getAttribute('data-affects-class')){
          document.getElementById(el.getAttribute('data-affects')).classList.remove(el.getAttribute('data-affects-class'));
        }

      } else {
        closeOtherMenuItems(el.getAttribute('data-parent'));
        this.parentElement.classList.add('open');
        el.setAttribute('aria-expanded', "true");

        if(el.getAttribute('data-affects') && el.getAttribute('data-affects-class')){
          document.getElementById(el.getAttribute('data-affects')).classList.add(el.getAttribute('data-affects-class'));
        }
      }

      toggleOverlay();

      el.parentElement.addEventListener("click", function(event){
        menuItemClicked = true;
        event.stopPropagation();
      });
    });
});

// Hover
Array.prototype.forEach.call(menuItemsHover, function(el, i){
    el.addEventListener("mouseover", function(event){
        this.className = "has-submenu open";
        toggleOverlay();
    });
    el.addEventListener("mouseout", function(event){
        document.querySelector(".has-submenu.open").className = "has-submenu";
        toggleOverlay();
    });
});

// Keyboard
Array.prototype.forEach.call(menuItems, function(el, i){
    var trigger     = el.querySelector('a[aria-expanded]');
    var triggers    = Array.prototype.slice.call(el.querySelectorAll('a.card, a.button, a.focusable, input, button'));

    Array.prototype.forEach.call(triggers, function(innerTrigger, i){
        innerTrigger.addEventListener('blur', function(event){
          if(menuItemClicked === false){
            if(triggers.indexOf(event.relatedTarget) == -1){
              el.classList.remove('open');
              trigger.setAttribute('aria-expanded', "false");
            }
          }
          menuItemClicked = false;
        });
    })

    el.addEventListener('keydown', function (event) {
        // var focused         = el.querySelector(':focus');
        var target          = event.target;
        var key             = event.which.toString();
        var ctrlModifier    = (event.ctrlKey && key.match(/33|34/));

        if(key == "27") {
          el.classList.remove('open');
          trigger.setAttribute('aria-expanded', "false");
          trigger.focus();
          event.stopPropagation();
        }

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
});

window.addEventListener('click', function(){
  closeOtherMenuItems();
  clearOverlay();
});

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
