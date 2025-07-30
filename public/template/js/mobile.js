let button = document.getElementsByClassName("hamburger")[0];
let hamburger = document.getElementsByClassName("mobile")[0];
let display = document.getElementsByClassName("display");
let active = false;

button.addEventListener("click", click);
  function click() {
    if (active) {
      button.children[0].src = "/template/img/icon-hamburger.svg";
      hamburger.style.opacity = "0";
      hamburger.style.display = "none";
      hamburger.style.pointerEvents = "none";
      active = false;
    }
    else {
      button.children[0].src = "/template/img/icon-close.svg";
      hamburger.style.opacity = "1";
      hamburger.style.display = "block";
      hamburger.style.pointerEvents = "auto";
      active = true;
    }
  }
