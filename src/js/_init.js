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

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
