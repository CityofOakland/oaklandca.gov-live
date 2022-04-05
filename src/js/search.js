// Instantiate Algolia
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

// Instantiate the search input box
allSearch.addWidget(
  instantsearch.widgets.searchBox({
    container: "#search-input",
    placeholder: "Search"
  })
);

// Instantiate the refinement clear button
allSearch.addWidget(
  instantsearch.widgets.clearAll({
    container: '#clear-refinements',
    templates: {
      link: "Remove Filters"
    }  })
);

/*
  Create a custom pagination widget
  This is used to create a custom pagination widget that is used in the search results page
*/
  // 1. Create a render function for the pagination widget
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

  // 2. Create the custom pagination widget
  const customPagination = instantsearch.connectors.connectPagination(
    renderPagination
  );

  // 3. Instantiate the pagination widget
  allSearch.addWidgets([
    customPagination({
      padding: 2,
    })
  ]);

// custom `renderFn` to render the custom RefinementList widget
function renderFn(RefinementListRenderingOptions, isFirstRendering) {
  if (isFirstRendering) {
    RefinementListRenderingOptions.widgetParams.containerNode
      .html('<fieldset></fieldset>')
  }

    RefinementListRenderingOptions.widgetParams.containerNode
      .find('li[data-refine-value]')
      .each(function() { $(this).off('click'); });

  if (RefinementListRenderingOptions.canRefine) {
    var list = RefinementListRenderingOptions.items.map(function(item) {
      return `
        <li data-refine-value="${item.value}">
          <input type="radio" name="Section" value="${item.value}" ${item.isRefined ? 'checked' : ''} />
          <a href="${RefinementListRenderingOptions.createURL(item.value)}">
            ${item.label} (${item.count})
          </a>
        </li>
      `;
    });

    RefinementListRenderingOptions.widgetParams.containerNode.find('fieldset').html(list);
    RefinementListRenderingOptions.widgetParams.containerNode
      .find('li[data-refine-value]')
      .each(function() {
        $(this).on('click', function(event) {
          event.stopPropagation();
          event.preventDefault();

          RefinementListRenderingOptions.refine($(this).data('refine-value'));
        });
      });
  } else {
    RefinementListRenderingOptions.widgetParams.containerNode.find('fieldset').html('');
  }
}

// connect `renderFn` to RefinementList logic
var customRefinementList = instantsearch.connectors.connectRefinementList(renderFn);

// mount widget on the page
allSearch.addWidget(
  customRefinementList({
    containerNode: $('#section-filter'),
    attributeName: 'section',
    limit: 10,
  })
);
    
// Instantiate the results/hits
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

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
