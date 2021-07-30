import { publicDecrypt } from "crypto";

const allSearch = instantsearch({
  appId: "6V5VJO8ZG2",
  apiKey: "9bded46d3070b2089499c70b2389708b",
  indexName: document.querySelector('#all-hits') ? (document.querySelector('#all-hits').dataset.index ? document.querySelector('#all-hits').dataset.index : 'production_all' ) : 'production_all',
  searchParameters: {
    highlightPreTag: '<b class="font-bold"><em>',
    highlightPostTag: '</em></b>',
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

// 1. Create a render function
const renderPagination = (renderOptions, isFirstRender) => {

  const container = document.querySelector('#bottom-pagination');

  const { pages, currentRefinement, nbPages, refine } = renderOptions;

  if(nbPages > 1) {

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
      <div class="mb-4">
        <h3 class="text-lg my-0"><a class="hover:bg-green-300 hover:text-white" href="{{ url }}">{{{_highlightResult.title.value}}}</a></h3>
        <p class="text-sm my-0">{{{ _snippetResult.leadIn.value }}}</p>
        {{{ #summary }}}
          <p class="text-sm my-0">{{{ _snippetResult.summary.value }}}</p>
        {{{ /summary }}}
        {{ ^summary }}
          <p class="text-sm my-0">{{{ _snippetResult.body.value }}}</p>
        {{{ /summary }}}
        <p class="text-sm my-0">{{{ _snippetResult.bio.value }}}</p>
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

