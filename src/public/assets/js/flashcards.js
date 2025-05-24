document.addEventListener('DOMContentLoaded', () => {
  setupDeleteButtons();
  setupEditable();
});

function setupDeleteButtons() {
  document.querySelectorAll('.delete-x-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const id = btn.dataset.id;
      if (confirm("Delete this flashcard?")) {
        fetch('delete_flashcard.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        }).then(() => {
          const flashcard = btn.closest('.flashcard');
          flashcard.remove();
        });
      }
    });
  });
}

function setupEditable() {
  document.querySelectorAll('.editable').forEach(el => {
    el.addEventListener('dblclick', () => {
      const original = el.textContent;
      const input = document.createElement('input');
      input.value = original;
      input.className = 'inline-input';
      el.replaceWith(input);
      input.focus();

      input.addEventListener('blur', () => saveEdit(input, el.classList.contains('front') ? 'front' : 'back', original));
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') input.blur();
        if (e.key === 'Escape') cancelEdit(input, original, el.classList.contains('front') ? 'front' : 'back');
      });
    });
  });
}

function saveEdit(input, field, original) {
  const newValue = input.value.trim();
  const li = input.closest('.flashcard');
  const id = li.dataset.id;

  if (!newValue || newValue === original) {
    cancelEdit(input, original, field);
    return;
  }

  fetch('update_flashcard.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, field, value: newValue })
  }).then(() => {
    const span = document.createElement('span');
    span.className = 'editable ' + field;
    span.textContent = newValue;
    input.replaceWith(span);
    setupEditable(); 
  });
}

function cancelEdit(input, original, field) {
  const span = document.createElement('span');
  span.className = 'editable ' + field;
  span.textContent = original;
  input.replaceWith(span);
  setupEditable(); 
}
