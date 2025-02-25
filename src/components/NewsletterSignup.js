import React, { useState } from 'react';
import { Send, Check, Loader, AlertTriangle } from 'lucide-react';

const NewsletterSignup = () => {
  const [email, setEmail] = useState('');
  const [type, setType] = useState('customer');
  const [status, setStatus] = useState('idle'); // idle, submitting, success, error
  const [errorMessage, setErrorMessage] = useState('');
  const [isValidEmail, setIsValidEmail] = useState(true);

  // E-Mail-Validierung bei Eingabe
  const validateEmail = (email) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };

  const handleEmailChange = (e) => {
    const value = e.target.value;
    setEmail(value);
    // Nur validieren wenn nicht leer (um rote Markierung beim Start zu vermeiden)
    if (value) {
      setIsValidEmail(validateEmail(value));
    } else {
      setIsValidEmail(true);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    // Finale Validierung vor dem Absenden
    if (!validateEmail(email)) {
      setIsValidEmail(false);
      setStatus('error');
      setErrorMessage('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
      return;
    }
    
    setStatus('submitting');
    setErrorMessage('');
    
    try {
      // Lokale Entwicklungsumgebung
      const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
      
      if (isLocal) {
        // Simuliere erfolgreiche Antwort für Entwicklung
        setTimeout(() => {
          setStatus('success');
          setEmail('');
        }, 1000);
        return;
      }
      
      // Produktionsumgebung: Senden Sie die Anfrage
      const formData = new FormData();
      formData.append('email', email);
      formData.append('type', type);
      
      // Versuche HTTP/HTTPS je nach aktueller Seite
      const protocol = window.location.protocol;
      const requestUrl = `${protocol}//${window.location.hostname}/newsletter.php`;
      
      // Logging für Debugging
      console.log('Sende Newsletter-Anfrage an:', requestUrl);
      
      const response = await fetch(requestUrl, {
        method: 'POST',
        body: formData,
      });
      
      // Versuche, die Antwort als Text zu bekommen (robuster als direkt JSON)
      const responseText = await response.text();
      
      console.log('Server-Antwort:', responseText);
      
      try {
        // Versuche, den Text als JSON zu parsen
        const data = JSON.parse(responseText);
        
        if (data.success) {
          setStatus('success');
          setEmail('');
        } else {
          setStatus('error');
          setErrorMessage(data.message || 'Ein Fehler ist aufgetreten.');
        }
      } catch (jsonError) {
        console.error('JSON-Parsing-Fehler:', jsonError);
        console.log('Erhaltene Antwort war:', responseText);
        
        // Prüfe, ob die Antwort HTML enthält (z.B. 500 Fehlerseite)
        if (responseText.includes('<!DOCTYPE html>') || responseText.includes('<html>')) {
          setStatus('error');
          setErrorMessage('Der Server hat eine HTML-Antwort zurückgegeben. Bitte kontaktieren Sie den Administrator.');
        } else {
          setStatus('error');
          setErrorMessage('Fehler bei der Verarbeitung der Serverantwort.');
        }
      }
    } catch (error) {
      console.error('Newsletter-Fehler:', error);
      setStatus('error');
      setErrorMessage('Verbindungsfehler. Bitte versuchen Sie es später erneut.');
    }
  };

  return (
    <div className="bg-[#09122c] text-white rounded-lg p-8 text-center shadow-lg">
      <h2 className="text-2xl font-bold mb-4">Bleiben Sie informiert!</h2>
      <p className="mb-6">
        Melden Sie sich für unseren Newsletter an und erfahren Sie als Erste/r von unserer Eröffnung.
      </p>
      
      {status === 'success' ? (
        <div className="flex items-center justify-center gap-3 text-lg bg-[#e17564] py-3 px-4 rounded-lg animate-fadeIn">
          <Check className="text-white" />
          <span>Vielen Dank für Ihre Anmeldung!</span>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="flex flex-col space-y-4">
          <div className="flex flex-col sm:flex-row gap-4">
            <input
              type="email"
              value={email}
              onChange={handleEmailChange}
              placeholder="Ihre E-Mail-Adresse"
              className={`px-4 py-3 rounded-lg text-gray-900 w-full sm:w-auto flex-1 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#e17564] ${
                !isValidEmail ? 'border-2 border-red-500' : ''
              }`}
              required
              aria-invalid={!isValidEmail}
              aria-describedby={!isValidEmail ? "email-error" : undefined}
            />
            
            <div className="flex items-center justify-center gap-4 sm:gap-8 text-white">
              <label className="flex items-center cursor-pointer">
                <input
                  type="radio"
                  name="type"
                  value="customer"
                  checked={type === 'customer'}
                  onChange={() => setType('customer')}
                  className="mr-2 accent-[#e17564] w-4 h-4"
                />
                <span>Kunde</span>
              </label>
              
              <label className="flex items-center cursor-pointer">
                <input
                  type="radio"
                  name="type"
                  value="vendor"
                  checked={type === 'vendor'}
                  onChange={() => setType('vendor')}
                  className="mr-2 accent-[#e17564] w-4 h-4"
                />
                <span>Direktvermarkter</span>
              </label>
            </div>
          </div>
          
          <button 
            type="submit"
            disabled={status === 'submitting' || !isValidEmail}
            className="bg-[#e17564] text-white px-6 py-3 rounded-lg hover:bg-opacity-90 
                     transition-all duration-200 flex items-center justify-center gap-2
                     disabled:opacity-50 disabled:cursor-not-allowed shadow-md mx-auto"
          >
            {status === 'submitting' ? (
              <>
                <Loader className="w-5 h-5 animate-spin" />
                <span>Wird angemeldet...</span>
              </>
            ) : (
              <>
                <span>Anmelden</span>
                <Send className="w-5 h-5" />
              </>
            )}
          </button>
        </form>
      )}
      
      {!isValidEmail && status !== 'submitting' && (
        <div id="email-error" className="mt-2 text-red-400 text-sm flex items-center justify-center gap-1">
          <AlertTriangle className="w-4 h-4" />
          <span>Bitte geben Sie eine gültige E-Mail-Adresse ein</span>
        </div>
      )}
      
      {status === 'error' && (
        <div className="mt-4 p-4 bg-red-700 text-white rounded-lg animate-fadeIn flex items-center gap-2">
          <AlertTriangle className="w-5 h-5 flex-shrink-0" />
          <span>{errorMessage}</span>
        </div>
      )}
      
      {/* Debug-Informationen - nur im lokalen Modus */}
      {(window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') && status !== 'success' && (
        <div className="mt-4 p-2 bg-yellow-600 text-white rounded-lg text-sm">
          Lokaler Entwicklungsmodus: Daten werden simuliert
        </div>
      )}
    </div>
  );
};

export default NewsletterSignup;