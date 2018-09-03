function hideTdo() {
  var timer = null;
  var target = document.querySelectorAll('#tidio-chat iframe')[0];
  if(!target || typeof target === 'undefined') {
    if(timer !== null) {
      clearTimeout(timer);
    }
    timer = setTimeout(hideTdo, 500);
    return;
  } else {
    var timer2 = null;
    var tdo = document.querySelectorAll('#tidio-chat iframe')[0].contentWindow.document.querySelectorAll('#root > div > div.online > div.input-wrapper > a.powered')[0];
    if(!tdo || typeof tdo === 'undefined') {
      if(timer2 !== null) {
        clearTimeout(timer2);
      }
      timer2 = setTimeout(hideTdo, 1);
      return;
    }
    document.querySelectorAll('#tidio-chat iframe')[0].contentWindow.document.querySelectorAll('#root > div > div.online > div.input-wrapper > a.powered')[0].style.display = 'none';
    return true;
  }
}

hideTdo();