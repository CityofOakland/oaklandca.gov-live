(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/app"],{

/***/ "./src/css/app.css":
/*!*************************!*\
  !*** ./src/css/app.css ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY3NzL2FwcC5jc3M/ZmI1NCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3NyYy9jc3MvYXBwLmNzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/css/app.css\n");

/***/ }),

/***/ "./src/js/app.js":
/*!***********************!*\
  !*** ./src/js/app.js ***!
  \***********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var alpinejs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! alpinejs */ \"./node_modules/alpinejs/dist/alpine.js\");\n/* harmony import */ var alpinejs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(alpinejs__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var picturefill__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! picturefill */ \"./node_modules/picturefill/dist/picturefill.js\");\n/* harmony import */ var picturefill__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(picturefill__WEBPACK_IMPORTED_MODULE_1__);\n\n\n\nfunction init() {\n  var selectNavs = document.getElementsByClassName('js-select-nav');\n\n  if (selectNavs) {\n    Array.from(selectNavs).forEach(function (select) {\n      select.addEventListener(\"change\", selectNav);\n    });\n  }\n\n  function selectNav(e) {\n    window.location.href = e.target.value;\n  }\n} //Prevent the function to run before the document is loaded\n\n\ndocument.addEventListener(\"readystatechange\", function () {\n  if (document.readyState === \"complete\") {\n    init();\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvanMvYXBwLmpzPzkwZTkiXSwibmFtZXMiOlsiaW5pdCIsInNlbGVjdE5hdnMiLCJkb2N1bWVudCIsImdldEVsZW1lbnRzQnlDbGFzc05hbWUiLCJBcnJheSIsImZyb20iLCJmb3JFYWNoIiwic2VsZWN0IiwiYWRkRXZlbnRMaXN0ZW5lciIsInNlbGVjdE5hdiIsImUiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJ0YXJnZXQiLCJ2YWx1ZSIsInJlYWR5U3RhdGUiXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUNBOztBQUVBLFNBQVNBLElBQVQsR0FBZ0I7QUFDZCxNQUFNQyxVQUFVLEdBQUdDLFFBQVEsQ0FBQ0Msc0JBQVQsQ0FBZ0MsZUFBaEMsQ0FBbkI7O0FBQ0UsTUFBSUYsVUFBSixFQUFnQjtBQUNoQkcsU0FBSyxDQUFDQyxJQUFOLENBQVdKLFVBQVgsRUFBdUJLLE9BQXZCLENBQStCLFVBQUFDLE1BQU0sRUFBSTtBQUN2Q0EsWUFBTSxDQUFDQyxnQkFBUCxDQUF3QixRQUF4QixFQUFrQ0MsU0FBbEM7QUFDRCxLQUZEO0FBR0Q7O0FBQ0QsV0FBU0EsU0FBVCxDQUFtQkMsQ0FBbkIsRUFBc0I7QUFDcEJDLFVBQU0sQ0FBQ0MsUUFBUCxDQUFnQkMsSUFBaEIsR0FBdUJILENBQUMsQ0FBQ0ksTUFBRixDQUFTQyxLQUFoQztBQUNEO0FBQ0YsQyxDQUVEOzs7QUFDQWIsUUFBUSxDQUFDTSxnQkFBVCxDQUEwQixrQkFBMUIsRUFBOEMsWUFBWTtBQUN4RCxNQUFJTixRQUFRLENBQUNjLFVBQVQsS0FBd0IsVUFBNUIsRUFBd0M7QUFDdENoQixRQUFJO0FBQ0w7QUFDRixDQUpEIiwiZmlsZSI6Ii4vc3JjL2pzL2FwcC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbImltcG9ydCAnYWxwaW5lanMnXG5pbXBvcnQgJ3BpY3R1cmVmaWxsJ1xuXG5mdW5jdGlvbiBpbml0KCkge1xuICBjb25zdCBzZWxlY3ROYXZzID0gZG9jdW1lbnQuZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSgnanMtc2VsZWN0LW5hdicpO1xuICAgIGlmIChzZWxlY3ROYXZzKSB7XG4gICAgQXJyYXkuZnJvbShzZWxlY3ROYXZzKS5mb3JFYWNoKHNlbGVjdCA9PiB7XG4gICAgICBzZWxlY3QuYWRkRXZlbnRMaXN0ZW5lcihcImNoYW5nZVwiLCBzZWxlY3ROYXYpO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIHNlbGVjdE5hdihlKSB7XG4gICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBlLnRhcmdldC52YWx1ZTtcbiAgfVxufVxuXG4vL1ByZXZlbnQgdGhlIGZ1bmN0aW9uIHRvIHJ1biBiZWZvcmUgdGhlIGRvY3VtZW50IGlzIGxvYWRlZFxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihcInJlYWR5c3RhdGVjaGFuZ2VcIiwgZnVuY3Rpb24gKCkge1xuICBpZiAoZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gXCJjb21wbGV0ZVwiKSB7XG4gICAgaW5pdCgpO1xuICB9XG59KTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/js/app.js\n");

/***/ }),

/***/ 0:
/*!***********************************************!*\
  !*** multi ./src/js/app.js ./src/css/app.css ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /Applications/MAMP/htdocs/oaklandca/src/js/app.js */"./src/js/app.js");
module.exports = __webpack_require__(/*! /Applications/MAMP/htdocs/oaklandca/src/css/app.css */"./src/css/app.css");


/***/ })

},[[0,"/js/manifest","/js/vendor"]]]);