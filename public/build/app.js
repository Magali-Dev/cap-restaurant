(self["webpackChunkcap_restaurant"] = self["webpackChunkcap_restaurant"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ (() => {

// LIENS AVEC ANCRE 
document.querySelectorAll('a[href*="#"]').forEach(function (link) {
  link.addEventListener('click', function (e) {
    var href = this.getAttribute('href');
    if (href.includes('#') && href !== '#') {
      var targetId = href.split('#')[1];
      var targetElement = document.getElementById(targetId);
      if (targetElement) {
        e.preventDefault();
        var navbarHeight = document.querySelector('.navbar').offsetHeight;
        var targetPosition = targetElement.offsetTop - navbarHeight;
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    }
  });
});

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/app.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7O0FBQUE7QUFDSUEsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBQyxjQUFjLENBQUMsQ0FBQ0MsT0FBTyxDQUFDLFVBQUFDLElBQUksRUFBSTtFQUN0REEsSUFBSSxDQUFDQyxnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsVUFBU0MsQ0FBQyxFQUFFO0lBQ3ZDLElBQU1DLElBQUksR0FBRyxJQUFJLENBQUNDLFlBQVksQ0FBQyxNQUFNLENBQUM7SUFDdEMsSUFBSUQsSUFBSSxDQUFDRSxRQUFRLENBQUMsR0FBRyxDQUFDLElBQUlGLElBQUksS0FBSyxHQUFHLEVBQUU7TUFDcEMsSUFBTUcsUUFBUSxHQUFHSCxJQUFJLENBQUNJLEtBQUssQ0FBQyxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUM7TUFDbkMsSUFBTUMsYUFBYSxHQUFHWCxRQUFRLENBQUNZLGNBQWMsQ0FBQ0gsUUFBUSxDQUFDO01BQ3ZELElBQUlFLGFBQWEsRUFBRTtRQUNmTixDQUFDLENBQUNRLGNBQWMsQ0FBQyxDQUFDO1FBQ2xCLElBQU1DLFlBQVksR0FBR2QsUUFBUSxDQUFDZSxhQUFhLENBQUMsU0FBUyxDQUFDLENBQUNDLFlBQVk7UUFDbkUsSUFBTUMsY0FBYyxHQUFHTixhQUFhLENBQUNPLFNBQVMsR0FBR0osWUFBWTtRQUU3REssTUFBTSxDQUFDQyxRQUFRLENBQUM7VUFDWkMsR0FBRyxFQUFFSixjQUFjO1VBQ25CSyxRQUFRLEVBQUU7UUFDZCxDQUFDLENBQUM7TUFDTjtJQUNKO0VBQ0osQ0FBQyxDQUFDO0FBQ04sQ0FBQyxDQUFDLEMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9jYXAtcmVzdGF1cmFudC8uL2Fzc2V0cy9hcHAuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLy8gTElFTlMgQVZFQyBBTkNSRSBcclxuICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ2FbaHJlZio9XCIjXCJdJykuZm9yRWFjaChsaW5rID0+IHtcclxuICAgICAgICBsaW5rLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICBjb25zdCBocmVmID0gdGhpcy5nZXRBdHRyaWJ1dGUoJ2hyZWYnKTtcclxuICAgICAgICAgICAgaWYgKGhyZWYuaW5jbHVkZXMoJyMnKSAmJiBocmVmICE9PSAnIycpIHtcclxuICAgICAgICAgICAgICAgIGNvbnN0IHRhcmdldElkID0gaHJlZi5zcGxpdCgnIycpWzFdO1xyXG4gICAgICAgICAgICAgICAgY29uc3QgdGFyZ2V0RWxlbWVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKHRhcmdldElkKTtcclxuICAgICAgICAgICAgICAgIGlmICh0YXJnZXRFbGVtZW50KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IG5hdmJhckhlaWdodCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5uYXZiYXInKS5vZmZzZXRIZWlnaHQ7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgdGFyZ2V0UG9zaXRpb24gPSB0YXJnZXRFbGVtZW50Lm9mZnNldFRvcCAtIG5hdmJhckhlaWdodDtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LnNjcm9sbFRvKHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiB0YXJnZXRQb3NpdGlvbixcclxuICAgICAgICAgICAgICAgICAgICAgICAgYmVoYXZpb3I6ICdzbW9vdGgnXHJcbiAgICAgICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG4iXSwibmFtZXMiOlsiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZm9yRWFjaCIsImxpbmsiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsImhyZWYiLCJnZXRBdHRyaWJ1dGUiLCJpbmNsdWRlcyIsInRhcmdldElkIiwic3BsaXQiLCJ0YXJnZXRFbGVtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJwcmV2ZW50RGVmYXVsdCIsIm5hdmJhckhlaWdodCIsInF1ZXJ5U2VsZWN0b3IiLCJvZmZzZXRIZWlnaHQiLCJ0YXJnZXRQb3NpdGlvbiIsIm9mZnNldFRvcCIsIndpbmRvdyIsInNjcm9sbFRvIiwidG9wIiwiYmVoYXZpb3IiXSwic291cmNlUm9vdCI6IiJ9