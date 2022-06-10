// Instantiate Algolia
const allSearch = instantsearch({
  appId: "6V5VJO8ZG2",
  apiKey: "9bded46d3070b2089499c70b2389708b",
  indexName: document.querySelector('#all-hits') ? (document.querySelector('#all-hits').dataset.index ? document.querySelector('#all-hits').dataset.index : 'production_all') : 'production_all',
  searchParameters: {
    highlightPreTag: '<b class="font-bold">',
    highlightPostTag: '</b>',
  },
  routing: true,
  searchFunction: function (helper) {
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
    },
    cssClasses: {
      link: 'text-base bg-green-800 border-2 border-green-800 text-white font-black mt-4 py-4 px-8 no-underline hover:border-green-800 hover:bg-green-800 inline-block hover:text-white'
    }
  })
);

allSearch.addWidget(
  instantsearch.widgets.stats({
    container: '#stats',
    autoHideContainer: false,
    templates: {
      body: `
        <h2 class="mb-4 text-xl leading-7 md:text-2xl md:leading-loose font-bold text-gray-1000">Your search for “{{ query }}” returned <em class="not-italic" aria-live="polite">{{ nbHits }} results</em>.</h2>
      `
    }
  })
);

const kebabCase = string => string
.replace(/([a-z])([A-Z])/g, "$1-$2")
.replace(/[\s_]+/g, '-')
.toLowerCase();

// 1. Create a render function for the pagination widget
const renderPagination = (renderOptions, isFirstRender) => {

  const container = document.querySelector('#bottom-pagination');

  const { pages, currentRefinement, nbPages, refine } = renderOptions;

  if (nbPages > 1 && nbPages <= 7) {

    const previous = `<li><a class="prev ${currentRefinement > 0 ? '' : 'disabled'}" href="#" data-value="${currentRefinement - 1}">Previous</a></li>`;
    const next = `<li><a class="next ${currentRefinement < nbPages - 1 ? '' : 'disabled'}" href="#" data-value="${currentRefinement + 1}">Next</a></li>`;

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
  } else if (nbPages > 1 && nbPages > 7) {
    const previous = `<li><a class="prev ${currentRefinement > 0 ? '' : 'disabled'}" href="#" data-value="${currentRefinement - 1}">Previous</a></li>`;
    const next = `<li><a class="next ${currentRefinement < nbPages - 1 ? '' : 'disabled'}" href="#" data-value="${currentRefinement + 1}">Next</a></li>`;

    const first = pages.includes(0) ? '' : `<li><a class="page" href="#" data-value="0">1</a></li><li>...</li>`;
    const last = pages.includes(nbPages - 1) ? '' : `<li>...</li><li><a class="page" href="#" data-value="${nbPages - 1}">${nbPages}</a></li>`;

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

// 1. Create a render function
const renderMenu = (renderOptions, isFirstRender) => {
  const {
    items,
    refine,
    widgetParams,
  } = renderOptions;

  if (isFirstRender) {
    // Sets up the HTML structure:
    const wrapper = document.createElement('fieldset');
    wrapper.setAttribute('role', 'complementary');

    const header = document.createElement('legend');
    header.classList.add('font-bold', 'border-t-2', 'border-gray-1000', 'pb-0', 'pt-4', 'mt-0', 'mb-6', 'w-full', 'leading-7');
    header.innerHTML = 'Filter by Type';

    const container = document.createElement('div');
    container.classList.add('mt-3', 'first-of-type:mt-0', 'filters');

    wrapper.appendChild(header);
    wrapper.appendChild(container);

    widgetParams.container.appendChild(wrapper);
  }

  widgetParams.container.querySelector('.filters').innerHTML = items
    .map(
      item => `
        <div class="flex gap-2 items-center mt-3 first-of-type:mt-0">
          <div class="flex items-center">
            <input
              type="radio" 
              id="${kebabCase(item.value)}"
              class="h-4 w-4 cursor-pointer border border-gray-300 appearance-none rounded-full checked:border-5 checked:border-green-800"
              data-refine-value="${item.value}" 
              name="section"
              value="${item.value}" ${item.isRefined ? 'checked' : ''} />
          </div>
          <label class="text-gray-700 text-sm font-medium cursor-pointer" for="${kebabCase(item.value)}">
            ${item.label} (${item.count}<span class="sr-only">pages</span>)
          </label>
        </div>
      `
    )
    .join('');

  [...widgetParams.container.querySelectorAll('input')].forEach(element => {
    element.addEventListener('click', event => {
      event.preventDefault();
      refine(event.currentTarget.dataset.refineValue);
    });
  });
  [...widgetParams.container.querySelectorAll('input')].forEach(element => {
    if(element.checked) element.focus();
  });
};

// 2. Create the custom widget
const customMenu = instantsearch.connectors.connectMenu(
  renderMenu
);

// 3. Instantiate
allSearch.addWidgets([
  customMenu({
    container: document.querySelector('#section-filter'),
    attributeName: 'section',
    sortBy: ['name'],
    limit: 20,
  })
]);

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
