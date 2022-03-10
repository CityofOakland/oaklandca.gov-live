"use strict";

var indexTemplate = "<article class=\"p-4 rounded flex-col border-gray-300 border h-full\">\n  {{#eventImage}}\n    <figure class=\"oak-hidden rounded w-full h-24 sm:h-32 md:h-48 bg-white relative z-0 md:block\">\n      <img src=\"{{ eventImage }}\" class=\"rounded object-cover absolute h-full w-full inset-0\" alt=\"\">\n    </figure>\n  {{/eventImage}}\n  {{^eventImage}}\n    <figure class=\"oak-hidden rounded w-full h-24 sm:h-32 md:h-48 p-4 bg-gray-300 relative z-0 md:block text-gray-500 fill-current p-12\">\n      <img src=\"/dist/img/icon-calendar.svg\" class=\"opacity-75 h-full mx-auto block\" alt=\"\">\n    </figure>\n  {{/eventImage}}\n  <div>\n    <div class=\"text-base text-gray-500 mb-4 md:mt-2\">\n      {{ displayDate }}\n    </div>\n    <h2 class=\"text-2xl sm:text-lg mt-4 mb-0 sm:mt-0 sm:mb-4\">\n      <a href=\"\/{{ url }}\">\n        {{{_highlightResult.title.value}}}\n      </a>\n    </h2>\n    <div class=\"text-gray-1000 sm:text-sm my-4 oak-hidden sm:block\">\n      {{{ _snippetResult.body.value }}}\n    </div>\n    <div class=\"oak-hidden sm:block\">\n      <a aria-label=\"Continue Reading \{{ title }}\" href=\"\/{{ url }}\">Read More \xBB</a>\n    </div>\n  </div>\n</article>";
