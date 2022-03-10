const allSearch = instantsearch({
  appId: "6V5VJO8ZG2",
  apiKey: "9bded46d3070b2089499c70b2389708b",
  indexName: document.querySelector('#all-hits') ? (document.querySelector('#all-hits').dataset.index ? document.querySelector('#all-hits').dataset.index : 'production_all' ) : 'production_all',
  searchParameters: {
    highlightPreTag: '<b class="font-bold">',
    highlightPostTag: '</b>',
  },
  routing: true,
  searchFunction: function(helper) {
    const query = allSearch.helper.state.query;
    const page = allSearch.helper.state.page;
    helper.search();
  },
});

allSearch.addWidget(
  instantsearch.widgets.searchBox({
    container: "#search-input",
    placeholder: "Search"
  })
);

allSearch.addWidget(
  instantsearch.widgets.clearAll({
    container: '#clear-refinements',
    templates: {
      link: "Remove Filters"
    }  })
);

allSearch.addWidget(
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
allSearch.addWidgets([
  customPagination({
    padding: 2,
  })
]);

allSearch.addWidget(
  instantsearch.widgets.menu({
    container: "#section-filter",
    attributeName: "section",
    operator: 'or',
    limit: 10,
  })
);    

allSearch.addWidget(
  instantsearch.widgets.hits({
    container: "#all-hits",
    templates: {
      empty: "No results",
      item: `
      <div class="result">
        <h3><a href="{{ url }}">{{{_highlightResult.title.value}}}</a> <span>{{ section }}</span></h3>
        <p>{{{ _snippetResult.leadIn.value }}}</p>
        {{{ #summary }}}<p>{{{ _snippetResult.summary.value }}}</p>{{{ /summary }}}
        {{ ^summary }}<p>{{{ _snippetResult.body.value }}}</p>{{{ /summary }}}
        <p>{{{ _snippetResult.bio.value }}}</p>
        <p class="updated">Updated: {{ dateUpdated }}</p>
      </div>
      `
    },
    cssClasses: {
      root: "block"
    },
  })
);

allSearch.start();

const searchNav = Array.from(document.querySelectorAll('a[data-holder]'));

