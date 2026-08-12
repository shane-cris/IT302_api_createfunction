// Admin panel sidebar navigation
(function () {
  var pagebtns = document.querySelectorAll('.pagebtn');
  var frames = document.querySelectorAll('.frames');

  function activateFrame(index) {
    pagebtns.forEach(function (btn) {
      btn.classList.remove('active');
    });
    frames.forEach(function (frame) {
      frame.classList.remove('active');
    });

    if (pagebtns[index]) pagebtns[index].classList.add('active');
    if (frames[index]) frames[index].classList.add('active');
  }

  pagebtns.forEach(function (btn, i) {
    btn.addEventListener('click', function () {
      activateFrame(i);
    });
  });
})();

// Add-booking modal
var detailpanel = document.getElementById('guestdetailpanel');

function adduseropen() {
  detailpanel.style.display = 'flex';
}

function adduserclose() {
  detailpanel.style.display = 'none';
}

if (detailpanel) {
  detailpanel.addEventListener('click', function (e) {
    if (e.target === detailpanel) adduserclose();
  });
}

// Table search
function searchFun() {
  var filter = document.getElementById('search_bar').value.toUpperCase();
  var table = document.getElementById('table-data');
  var rows = table.getElementsByTagName('tr');

  for (var i = 0; i < rows.length; i++) {
    var nameCell = rows[i].getElementsByTagName('td')[1];
    if (nameCell) {
      var text = nameCell.textContent || nameCell.innerText;
      rows[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
    }
  }
}