(function () {
  function postJson(url, payload) {
    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload || {})
    }).then(function (response) {
      return response.json().then(function (json) {
        if (!response.ok || !json.success) {
          throw new Error(json.message || 'Request failed');
        }

        return json;
      });
    });
  }

  function initActiveToggle() {
    var toggles = document.querySelectorAll('.js-pzfc-toggle');

    toggles.forEach(function (toggle) {
      toggle.addEventListener('change', function () {
        var targetState = !!toggle.checked;

        postJson(toggle.dataset.url, { active: targetState }).catch(function () {
          toggle.checked = !targetState;
          window.showErrorMessage && window.showErrorMessage('Failed to update status');
        });
      });
    });
  }

  function initIconToggle() {
    var toggles = document.querySelectorAll('.js-pzfc-toggle-icon');

    toggles.forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var currentState = toggle.dataset.active === '1';
        var targetState = !currentState;

        postJson(toggle.dataset.url, { active: targetState })
          .then(function () {
            toggle.dataset.active = targetState ? '1' : '0';
            toggle.innerHTML = targetState
              ? '<span class="pzfc-status-ok">✓</span>'
              : '<span class="pzfc-status-ko">×</span>';
          })
          .catch(function () {
            window.showErrorMessage && window.showErrorMessage('Failed to update status');
          });
      });
    });
  }

  function initSorting() {
    var tableBody = document.getElementById('pzfc-sortable');
    if (!tableBody) {
      return;
    }

    var draggedRow = null;

    tableBody.querySelectorAll('tr[data-item-id]').forEach(function (row) {
      row.addEventListener('dragstart', function () {
        draggedRow = row;
        row.classList.add('pzfc-row-dragging');
      });

      row.addEventListener('dragend', function () {
        row.classList.remove('pzfc-row-dragging');
      });

      row.addEventListener('dragover', function (event) {
        event.preventDefault();
      });

      row.addEventListener('drop', function (event) {
        event.preventDefault();

        if (!draggedRow || draggedRow === row) {
          return;
        }

        var rect = row.getBoundingClientRect();
        var shouldInsertAfter = (event.clientY - rect.top) > rect.height / 2;

        if (shouldInsertAfter) {
          row.parentNode.insertBefore(draggedRow, row.nextSibling);
        } else {
          row.parentNode.insertBefore(draggedRow, row);
        }

        saveOrder(tableBody);
      });
    });
  }

  function saveOrder(tableBody) {
    var orderedIds = [];

    tableBody.querySelectorAll('tr[data-item-id]').forEach(function (row) {
      orderedIds.push(parseInt(row.dataset.itemId, 10));
    });

    postJson(tableBody.dataset.reorderUrl, { orderedIds: orderedIds }).catch(function () {
      window.showErrorMessage && window.showErrorMessage('Failed to save order');
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initActiveToggle();
    initIconToggle();
    initSorting();
  });
})();
