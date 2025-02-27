import React, { useState } from 'react';
import { Check, Send, Loader, X, AlertTriangle } from 'lucide-react';

const RentalRequestForm = ({ spaceType, onClose }) => {
  const [formData, setFormData] = useState({
    businessName: '',
    contactPerson: '',
    email: '',
    phone: '',
    productType: '',
    spaceType: spaceType || 'regal-a',
    message: ''
  });

  const [status, setStatus] = useState('idle'); // idle, submitting, success, error
  const [errorMessage, setErrorMessage] = useState('');

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus('submitting');
    setErrorMessage('');

    try {
      // FormData für zuverlässigeren Datentransport
      const formDataObj = new FormData();
      
      // Alle Formularfelder hinzufügen
      Object.entries(formData).forEach(([key, value]) => {
        formDataObj.append(key, value);
      });

      // Lokale Entwicklungsumgebung vs. Produktion
      const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
      
      if (isLocal) {
        // Simuliere erfolgreiche Antwort für Entwicklung
        console.log('Entwicklungsmodus: Simuliere Mietanfrage mit Daten:', formData);
        setTimeout(() => {
          setStatus('success');
        }, 1000);
        return;
      }

      // Verwende den vereinfachten Handler
      const response = await fetch('/universal-form-handler.php?type=rental', {
        method: 'POST',
        body: formDataObj
      });
      
      // Für Debugging
      const responseText = await response.text();
      console.log('Server-Antwort:', responseText);
      
      // Versuche JSON zu parsen
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON-Parsing-Fehler:', parseError);
        throw new Error('Ungültige Antwort vom Server erhalten');
      }
      
      if (data && data.success) {
        setStatus('success');
      } else {
        throw new Error((data && data.message) || 'Ein Fehler ist aufgetreten.');
      }
    } catch (error) {
      console.error('Mietanfrage-Fehler:', error);
      setStatus('error');
      setErrorMessage(
        error.message || 
        'Bei der Übermittlung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.'
      );
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
        <div className="p-6">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-bold text-[var(--secondary)]">Verkaufsfläche anfragen</h2>
            <button 
              onClick={onClose}
              className="text-gray-500 hover:text-gray-700 transition-colors"
              aria-label="Schließen"
            >
              <X className="w-6 h-6" />
            </button>
          </div>

          {status === 'success' ? (
            <div className="flex flex-col items-center justify-center text-center py-8">
              <div className="bg-green-100 rounded-full p-3 mb-4">
                <Check className="w-8 h-8 text-green-600" />
              </div>
              <h3 className="text-xl font-bold text-[var(--secondary)] mb-2">Anfrage erfolgreich gesendet!</h3>
              <p className="text-gray-600 mb-6">
                Vielen Dank für Ihr Interesse an housnkuh! Wir werden Ihre Anfrage prüfen und uns so schnell wie möglich bei Ihnen melden.
              </p>
              <button
                onClick={onClose}
                className="bg-[var(--primary)] text-white px-6 py-2 rounded-lg hover:bg-opacity-90 transition-all duration-200"
              >
                Schließen
              </button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label htmlFor="businessName" className="block text-sm font-medium text-gray-700 mb-1">
                  Firmenname*
                </label>
                <input
                  type="text"
                  id="businessName"
                  name="businessName"
                  value={formData.businessName}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  required
                />
              </div>

              <div>
                <label htmlFor="contactPerson" className="block text-sm font-medium text-gray-700 mb-1">
                  Ansprechpartner*
                </label>
                <input
                  type="text"
                  id="contactPerson"
                  name="contactPerson"
                  value={formData.contactPerson}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                    E-Mail*
                  </label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                    required
                  />
                </div>

                <div>
                  <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-1">
                    Telefon
                  </label>
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value={formData.phone}
                    onChange={handleChange}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  />
                </div>
              </div>

              <div>
                <label htmlFor="productType" className="block text-sm font-medium text-gray-700 mb-1">
                  Art der Produkte*
                </label>
                <input
                  type="text"
                  id="productType"
                  name="productType"
                  value={formData.productType}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  placeholder="z.B. Honig, Käse, Handwerksprodukte, etc."
                  required
                />
              </div>

              <div>
                <label htmlFor="spaceType" className="block text-sm font-medium text-gray-700 mb-1">
                  Gewünschte Verkaufsfläche*
                </label>
                <select
                  id="spaceType"
                  name="spaceType"
                  value={formData.spaceType}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  required
                >
                  <option value="regal-a">Verkaufsblock Lage A (35€/Monat)</option>
                  <option value="regal-b">Verkaufsblock Lage B (15€/Monat)</option>
                  <option value="kuehl">Verkaufsblock gekühlt (50€/Monat)</option>
                  <option value="tisch">Verkaufsblock Tisch (40€/Monat)</option>
                </select>
              </div>

              <div>
                <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                  Zusätzliche Informationen
                </label>
                <textarea
                  id="message"
                  name="message"
                  value={formData.message}
                  onChange={handleChange}
                  rows="4"
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                  placeholder="Haben Sie besondere Wünsche oder Fragen?"
                ></textarea>
              </div>

              {status === 'error' && (
                <div className="p-4 bg-red-50 text-red-700 rounded-lg flex items-start">
                  <AlertTriangle className="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" />
                  <p>{errorMessage}</p>
                </div>
              )}

              <div className="flex justify-end gap-4 pt-4">
                <button
                  type="button"
                  onClick={onClose}
                  className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  Abbrechen
                </button>
                <button
                  type="submit"
                  disabled={status === 'submitting'}
                  className="bg-[var(--primary)] text-white px-6 py-2 rounded-lg hover:bg-opacity-90 
                          transition-all duration-200 flex items-center justify-center gap-2
                          disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {status === 'submitting' ? (
                    <>
                      <Loader className="w-5 h-5 animate-spin" />
                      <span>Wird gesendet...</span>
                    </>
                  ) : (
                    <>
                      <span>Anfrage senden</span>
                      <Send className="w-5 h-5" />
                    </>
                  )}
                </button>
              </div>
            </form>
          )}
        </div>
      </div>
    </div>
  );
};

export default RentalRequestForm;