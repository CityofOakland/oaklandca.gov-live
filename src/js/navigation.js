var menuItems = document.querySelectorAll('li.has-submenu');

// Hover
Array.prototype.forEach.call(menuItems, function (el, i) {
  el.addEventListener("mouseover", function (event) {
    this.classList.add("open");
    clearTimeout(timer);
  });
  el.addEventListener("mouseout", function (event) {
    timer = setTimeout(function (event) {
      el.classList.remove("open");
    }, 500);
  });
});

// Keyboard
Array.prototype.forEach.call(menuItems, function (el, i) {
  var trigger = el.querySelector('a');
  var triggers = Array.prototype.slice.call(el.querySelectorAll('a.card'));

  Array.prototype.forEach.call(triggers, function (card, i) {
    card.addEventListener('blur', function (event) {
      if (triggers.indexOf(event.relatedTarget) == -1) {
        trigger.setAttribute('aria-expanded', "false");
      }
    });
  })

  el.addEventListener('keydown', function (event) {
    var focused = el.querySelector(':focus');
    var target = event.target;
    var key = event.which.toString();
    var ctrlModifier = (event.ctrlKey && key.match(/33|34/));

    // Press Enter
    if (key == "13") {
      if (trigger.getAttribute("aria-expanded") == "true") {
        trigger.setAttribute('aria-expanded', "false");
      } else {
        trigger.setAttribute('aria-expanded', "true");
        triggers[0].focus();
      }
    }

    if (key.match(/38|40/) || ctrlModifier) {
      var index = triggers.indexOf(target);

      var direction = (key.match(/34|40/)) ? 1 : -1;
      var length = triggers.length;
      var newIndex = (index + length + direction) % length;

      if (index == -1) {
        triggers[0].focus();
      } else if (direction == 1 && newIndex == 0) {
        trigger.setAttribute('aria-expanded', "false");
        trigger.focus();
      } else if (direction == -1 && newIndex == (length - 1)) {
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
