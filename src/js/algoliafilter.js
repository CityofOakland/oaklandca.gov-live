const filterReveal = document.getElementById("filter-reveal");
const filters = document.getElementById("algolia-filters");
const openClasses = ["opacity-100", "h-auto", "z-40", "visible"];
const closedClasses = ["opacity-0", "h-0", "z-neg-10", "invisible"];

if (filterReveal != null) {
  filterReveal.addEventListener("click", function(e) {
    e.preventDefault();

    if (filters.classList.contains("opacity-0")) {
      filters.classList.add(...openClasses);
      filters.classList.remove(...closedClasses);
      filterReveal.innerHTML = "Hide Filters";
    } else {
      filters.classList.add(...closedClasses);
      filters.classList.remove(...openClasses);
      filterReveal.innerHTML = "Show Filters";
    }
  }, false);
}

if (window.section && window.entryTitle) {
  var filtered= `${window.section}:'${window.entryTitle}'`;
}

const ts = Math.round((new Date()).getTime() / 1000);
const search = instantsearch({
  appId: "6V5VJO8ZG2",
  apiKey: "9bded46d3070b2089499c70b2389708b",
  indexName: window.algoliaIndex,
  routing: true,
  searchParameters: {
    filters: filtered || undefined,
    highlightPreTag: '<b class="font-bold">',
    highlightPostTag: '</b>',
    snippetEllipsisText: "…",
  },
});

search.addWidget(
  instantsearch.widgets.analytics({
    pushFunction(formattedParameters, state, results) {
      window.ga('set', 'page', window.location.pathname + window.location.search);
      window.ga('send', 'pageView');
    },
  })
);

search.addWidget(
  instantsearch.widgets.stats({
    container: '#stats',
    autoHideContainer: false,
    templates: {
      body: `
        <h2 class="mb-4 text-base font-normal border-b border-gray-800 pb-1 text-gray-1000">Your search for “{{ query }}” returned <span class="font-bold" aria-live="polite">{{ nbHits }} results</span>.</h2>
      `
    }
  })
);

const defaultTemplate =
`<article class="py-8 sm:py-12 border-gray-300 border-b-2">
  {{#displayDate}}
  <div class="text-base text-gray-600 mb-4">
    Publish Date: <b>{{ displayDate }}</b>
  </div>
  {{/displayDate}}
  <h2 class="text-xl md:text-2xl my-0 {{#leadIn}} mt-0 mb-3 md:mb-6 {{/leadIn}}">
    <a href="{{ url }}">
      {{{ _highlightResult.title.value }}}
    </a>
  </h2>
  {{#leadIn}}
    <p class="text-base text-gray-700 md:text-lg my-2 md:my-4">
      {{{ _highlightResult.leadIn.value }}}
    </p>
  {{/leadIn}}
</article>`;

facetFilters.forEach(facet => {
  search.addWidget(
    instantsearch.widgets.menuSelect({
      container: facet.container,
      attributeName: facet.attribute,
      operator: 'or',
      limit: 10,
      templates: {
        header: facet.header
      }
    })
  );
});

if (typeof moment !== 'undefined') {
  const ONE_DAY_IN_MS = 3600 * 24 * 1000;

  const TODAY = moment().format('L');


  const datePicker = instantsearch.connectors.connectRange(
    (options, isFirstRendering) => {
      if (!isFirstRendering) return;

      const { refine }  = options;

      const MONTHSTART  = new Date(moment().startOf('month')).getTime();
      const MONTHEND    = new Date(moment().add(11, 'month').endOf('month')).getTime();

      refine([MONTHSTART, MONTHEND]);

      new Calendar({
        element: $('#calendar'),
        same_day_range: true,
        start_date: moment().startOf('month'),
        end_date: moment().add(11, 'month').endOf('month'),
        presets: [{
          label: 'This year',
          start: moment().startOf('month'),
          end: moment().add(11, 'month').endOf('month')
        },{
          label: 'This month',
          start: moment().startOf('month'),
          end: moment().endOf('month')
        },{
          label: 'Next month',
          start: moment().add(1, 'month').startOf('month'),
          end: moment().add(1, 'month').endOf('month')
        },{
          label: 'Last month',
          start: moment().subtract(1, 'month').startOf('month'),
          end: moment().subtract(1, 'month').endOf('month')
        },{
          label: 'Last year',
          start: moment().subtract(1, 'year').startOf('year'),
          end: moment().subtract(1, 'year').endOf('year')
        }],
        callback: function() {
          const start = new Date(this.start_date).getTime();
          const end = new Date(this.end_date).getTime();
          const actualEnd = start === end ? end + ONE_DAY_IN_MS - 1 : end;

          refine([start, actualEnd]);
        },
      });
    }
  );

  const dateRangeWidget = datePicker({
    attributeName: 'date',
  });

  search.addWidget(dateRangeWidget);
}

search.addWidget(
  instantsearch.widgets.searchBox({
    container: "#search-input",
    placeholder: window.searchInputText || "Search"
  })
);

search.addWidget(
  instantsearch.widgets.hits({
    container: "#hits",
    cssClasses: {
      root: window.hitsRootClass || "block"
    },
    templates: {
      empty: "No results",
      item: window.indexTemplate || defaultTemplate,
    },
    transformItems: function(items){
      for(var i = 0; i < items.length; i++) {
        var item    = items[i];
        var date    = new Date(item.date);
        var months  = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        var nth = function(d) {
          if (d > 3 && d < 21) return 'th';
          switch (d % 10) {
            case 1:  return "st";
            case 2:  return "nd";
            case 3:  return "rd";
            default: return "th";
          }
        }

        var year  = date.getFullYear();
        var month = months[date.getMonth()];
        var day   = date.getDate();

        item.formattedDate = month + " " + day + nth(day) + ", " + year;
        items[i] = item;
      }
      return items;
    }
  })
);

// 1. Create a render function
const renderPagination = (renderOptions, isFirstRender) => {

  const container = document.querySelector('#bottom-pagination');

  const { pages, currentRefinement, nbPages, refine } = renderOptions;

  if(nbPages > 1 && nbPages <= 7) {

    const previous  = `<li><a class="prev ${currentRefinement > 0 ? '' : 'disabled' }" href="#" data-value="${currentRefinement - 1}">Previous</a></li>`;
    const next      = `<li><a class="next ${currentRefinement < nbPages - 1 ? '' : 'disabled'}" href="#" data-value="${currentRefinement + 1}">Next</a></li>`;

    const pages = [...Array(nbPages).keys()];;

    container.innerHTML = `
      <ul class="pagination">
        ${previous}
        ${pages
          .map(page => `<li><a class="page ${currentRefinement === page ? 'page-active' : ''}" href="#" data-value="${page}">${page + 1}</a></li>`)
          .join('')}
        ${next}
      </ul>
    `;

    [...container.querySelectorAll('a')].forEach(element => {
      element.addEventListener('click', event => {
        event.preventDefault();
        refine(event.currentTarget.dataset.value);
      });
    });
  } else if(nbPages > 1 && nbPages > 7) {
    const previous  = `<li><a class="prev ${currentRefinement > 0 ? '' : 'disabled' }" href="#" data-value="${currentRefinement - 1}">Previous</a></li>`;
    const next      = `<li><a class="next ${currentRefinement < nbPages - 1 ? '' : 'disabled'}" href="#" data-value="${currentRefinement + 1}">Next</a></li>`;

    const first = pages.includes(0) ? '' : `<li><a class="page" href="#" data-value="0">1</a></li><li>...</li>`;
    const last  = pages.includes(nbPages - 1) ? '' : `<li>...</li><li><a class="page" href="#" data-value="${nbPages - 1}">${nbPages}</a></li>`;

    container.innerHTML = `
      <ul class="pagination">
        ${previous}
        ${first}
        ${pages
          .map(page => `<li><a class="page ${currentRefinement === page ? 'page-active' : ''}" href="#" data-value="${page}">${page + 1}</a></li>`)
          .join('')}
        ${last}
        ${next}
      </ul>
    `;

    [...container.querySelectorAll('a')].forEach(element => {
      element.addEventListener('click', event => {
        event.preventDefault();
        refine(event.currentTarget.dataset.value);
      });
    });
  } else {
    container.innerHTML = '';
  }
};

// 2. Create the custom widget
const customPagination = instantsearch.connectors.connectPagination(
  renderPagination
);

// 3. Instantiate
search.addWidgets([
  customPagination({
    padding: 2,
  })
]);

search.start();

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
