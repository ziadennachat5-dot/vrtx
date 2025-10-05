(function () {
  const form = document.getElementById(''auth-form'');
  if (!form) return;
  const messageEl = document.getElementById(''message'');
  form.addEventListener(''click'', function (ev) {
    const target = ev.target;
    if (!(target instanceof HTMLElement)) return;
    const action = target.getAttribute(''data-action'');
    if (!action) return;
    ev.preventDefault();
    const email = document.getElementById(''email'').value.trim();
    const password = document.getElementById(''password'').value;
    if (!email || !password) {
      setMessage(''Veuillez renseigner email et mot de passe.'', ''error'');
      return;
    }
    setMessage(''Veuillez patienter...'');
    fetch(''../../backend/vrt2/compte.php'', {
      method: ''POST'',
      headers: { ''Content-Type'': ''application/x-www-form-urlencoded'' },
      body: new URLSearchParams({ action, email, password }).toString()
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        setMessage(data.message || ''SuccÃ¨s'', ''success'');
      } else {
        setMessage(data.message || ''Une erreur est survenue.'', ''error'');
      }
    })
    .catch(() => setMessage(''Erreur rÃ©seau.'', ''error''));
  });
  function setMessage(text, type) {
    if (!messageEl) return;
    messageEl.textContent = text;
    messageEl.className = ''message'' + (type ? '' '' + type : '''');
  }
})();
