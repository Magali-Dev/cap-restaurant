(self["webpackChunkcap_restaurant"] = self["webpackChunkcap_restaurant"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ (() => {

// ===== LIENS AVEC ANCRE =====
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7O0FBRUk7QUFDQUEsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBQyxjQUFjLENBQUMsQ0FBQ0MsT0FBTyxDQUFDLFVBQUFDLElBQUksRUFBSTtFQUN0REEsSUFBSSxDQUFDQyxnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsVUFBU0MsQ0FBQyxFQUFFO0lBQ3ZDLElBQU1DLElBQUksR0FBRyxJQUFJLENBQUNDLFlBQVksQ0FBQyxNQUFNLENBQUM7SUFDdEMsSUFBSUQsSUFBSSxDQUFDRSxRQUFRLENBQUMsR0FBRyxDQUFDLElBQUlGLElBQUksS0FBSyxHQUFHLEVBQUU7TUFDcEMsSUFBTUcsUUFBUSxHQUFHSCxJQUFJLENBQUNJLEtBQUssQ0FBQyxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUM7TUFDbkMsSUFBTUMsYUFBYSxHQUFHWCxRQUFRLENBQUNZLGNBQWMsQ0FBQ0gsUUFBUSxDQUFDO01BQ3ZELElBQUlFLGFBQWEsRUFBRTtRQUNmTixDQUFDLENBQUNRLGNBQWMsQ0FBQyxDQUFDO1FBQ2xCLElBQU1DLFlBQVksR0FBR2QsUUFBUSxDQUFDZSxhQUFhLENBQUMsU0FBUyxDQUFDLENBQUNDLFlBQVk7UUFDbkUsSUFBTUMsY0FBYyxHQUFHTixhQUFhLENBQUNPLFNBQVMsR0FBR0osWUFBWTtRQUU3REssTUFBTSxDQUFDQyxRQUFRLENBQUM7VUFDWkMsR0FBRyxFQUFFSixjQUFjO1VBQ25CSyxRQUFRLEVBQUU7UUFDZCxDQUFDLENBQUM7TUFDTjtJQUNKO0VBQ0osQ0FBQyxDQUFDO0FBQ04sQ0FBQyxDQUFDLEMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9jYXAtcmVzdGF1cmFudC8uL2Fzc2V0cy9hcHAuanMiXSwic291cmNlc0NvbnRlbnQiOlsiXG5cbiAgICAvLyA9PT09PSBMSUVOUyBBVkVDIEFOQ1JFID09PT09XG4gICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnYVtocmVmKj1cIiNcIl0nKS5mb3JFYWNoKGxpbmsgPT4ge1xuICAgICAgICBsaW5rLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgICAgICAgICAgY29uc3QgaHJlZiA9IHRoaXMuZ2V0QXR0cmlidXRlKCdocmVmJyk7XG4gICAgICAgICAgICBpZiAoaHJlZi5pbmNsdWRlcygnIycpICYmIGhyZWYgIT09ICcjJykge1xuICAgICAgICAgICAgICAgIGNvbnN0IHRhcmdldElkID0gaHJlZi5zcGxpdCgnIycpWzFdO1xuICAgICAgICAgICAgICAgIGNvbnN0IHRhcmdldEVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0YXJnZXRJZCk7XG4gICAgICAgICAgICAgICAgaWYgKHRhcmdldEVsZW1lbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBuYXZiYXJIZWlnaHQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcubmF2YmFyJykub2Zmc2V0SGVpZ2h0O1xuICAgICAgICAgICAgICAgICAgICBjb25zdCB0YXJnZXRQb3NpdGlvbiA9IHRhcmdldEVsZW1lbnQub2Zmc2V0VG9wIC0gbmF2YmFySGVpZ2h0O1xuXG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5zY3JvbGxUbyh7XG4gICAgICAgICAgICAgICAgICAgICAgICB0b3A6IHRhcmdldFBvc2l0aW9uLFxuICAgICAgICAgICAgICAgICAgICAgICAgYmVoYXZpb3I6ICdzbW9vdGgnXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG4iXSwibmFtZXMiOlsiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZm9yRWFjaCIsImxpbmsiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsImhyZWYiLCJnZXRBdHRyaWJ1dGUiLCJpbmNsdWRlcyIsInRhcmdldElkIiwic3BsaXQiLCJ0YXJnZXRFbGVtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJwcmV2ZW50RGVmYXVsdCIsIm5hdmJhckhlaWdodCIsInF1ZXJ5U2VsZWN0b3IiLCJvZmZzZXRIZWlnaHQiLCJ0YXJnZXRQb3NpdGlvbiIsIm9mZnNldFRvcCIsIndpbmRvdyIsInNjcm9sbFRvIiwidG9wIiwiYmVoYXZpb3IiXSwic291cmNlUm9vdCI6IiJ9