const handleSubmit = async (e) => {
  e.preventDefault();
  setStatus('submitting');
  setErrorMessage('');
  
  // Validierung: Alle Felder ausgefüllt
  if (!formData.name || !formData.email || formData.guessedVendors.some(vendor => !vendor)) {
    setStatus('error');
    setErrorMessage('Bitte alle Pflichtfelder ausfüllen');
    return;
  }

  try {
    // Im lokalen Entwicklungsmodus
    const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (isLocal) {
      // Simulieren einer erfolgreichen Antwort für Entwicklung
      setTimeout(() => {
        setStatus('success');
      }, 1000);
      return;
    }
    
    // Produktionsmodus: Senden der Anfrage an unseren Form-Handler
    const response = await axios.post('/form-handler.php?type=vendor-contest', formData);
    
    if (response.data.success) {
      setStatus('success');
    } else {
      throw new Error(response.data.message || 'Ein unbekannter Fehler ist aufgetreten');
    }
  } catch (error) {
    console.error('Fehler beim Absenden:', error);
    setStatus('error');
    setErrorMessage(error.message || 'Bei der Übermittlung ist ein Fehler aufgetreten');
  }
};