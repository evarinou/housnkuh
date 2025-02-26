import React, { useState } from 'react';
import { Send, Check } from 'lucide-react';

const NewsletterSignup = () => {
  const [email, setEmail] = useState('');
  const [type, setType] = useState('customer'); // 'customer' oder 'vendor'
  const [status, setStatus] = useState('idle'); // idle, submitting, success, error
  const [errorMessage, setErrorMessage] = useState('');

  // API-Pfad bestimmen basierend auf der Umgebung
/*  const getApiUrl = () => {
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    return isLocalhost ? 'http://localhost:5000/api/newsletter/subscribe' : '/api/newsletter.php';
  };*/

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus('submitting');
    setErrorMessage('');
    
    try {
      // Im lokalen Entwicklungsmodus simulieren wir einfach den Erfolg
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Simulierte Antwort für lokale Entwicklung
        setTimeout(() => {
          setStatus('success');
          setEmail('');
        }, 1000);
        return;
      }
      
      // Auf dem Server: Echten API-Call durchführen
      const formData = new FormData();
      formData.append('email', email);
      formData.append('type', type);
      
      const response = await fetch('/newsletter.php', {
        method: 'POST',
        body: formData,
      });
      
      // Versuche, die Antwort zu parsen
      try {
        const data = await response.json();
        
        if (data.success) {
          setStatus('success');
          setEmail('');
        } else {
          setStatus('error');
          setErrorMessage(data.message || 'Ein Fehler ist aufgetreten.');
        }
      } catch (jsonError) {
        // Fehler beim JSON-Parsing
        console.error('JSON Parsing error:', jsonError);
        setStatus('error');
        setErrorMessage('Fehler bei der Verarbeitung der Serverantwort.');
      }
    } catch (error) {
      setStatus('error');
      setErrorMessage('Verbindungsfehler. Bitte versuchen Sie es später erneut.');
      console.error('Newsletter error:', error);
    }
  };

  return (
    <div className="bg-[var(--secondary)] text-white rounded-lg p-8 text-center">
      <h2 className="text-2xl font-bold mb-4">Bleiben Sie informiert!</h2>
      <p className="mb-6">
        Melden Sie sich für unseren Newsletter an und erfahren Sie als Erste/r von unserer Eröffnung.
      </p>
      
      {status === 'success' ? (
        <div className="flex items-center justify-center text-lg">
          <Check className="mr-2" />
          Vielen Dank für Ihre Anmeldung!
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="flex flex-col sm:flex-row gap-4 justify-center">
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="Ihre E-Mail-Adresse"
            className="px-4 py-2 rounded-lg text-gray-900 w-full sm:w-auto"
            required
          />
          
          <div className="flex items-center gap-4 text-white">
            <label className="flex items-center">
              <input
                type="radio"
                name="type"
                value="customer"
                checked={type === 'customer'}
                onChange={() => setType('customer')}
                className="mr-2"
              />
              Kunde
            </label>
            
            <label className="flex items-center">
              <input
                type="radio"
                name="type"
                value="vendor"
                checked={type === 'vendor'}
                onChange={() => setType('vendor')}
                className="mr-2"
              />
              Direktvermarkter
            </label>
          </div>
          
          <button 
            type="submit"
            disabled={status === 'submitting'}
            className="bg-[var(--primary)] text-white px-6 py-2 rounded-lg hover:bg-opacity-90 
                     transition-all duration-200 flex items-center justify-center gap-2
                     disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {status === 'submitting' ? (
              'Wird angemeldet...'
            ) : (
              <>
                <span>Anmelden</span>
                <Send size={18} />
              </>
            )}
          </button>
        </form>
      )}
      
      {status === 'error' && (
        <div className="mt-4 p-4 bg-red-600 text-white rounded-lg">
          {errorMessage}
        </div>
      )}
      
      {/* Debug-Informationen - nur im lokalen Modus oder temporär anzeigen */}
      {(window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') && (
        <div className="mt-4 p-2 bg-yellow-600 text-white rounded-lg text-sm">
          Lokaler Entwicklungsmodus: Daten werden simuliert
        </div>
      )}
    </div>
  );
};

export default NewsletterSignup;