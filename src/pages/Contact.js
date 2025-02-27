import React, { useState } from 'react';
import { Mail, Phone, Send, Loader, AlertTriangle, Check } from 'lucide-react';

const Contact = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    message: '',
    subject: 'Allgemeine Anfrage'
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
        console.log('Entwicklungsmodus: Simuliere Kontaktanfrage mit Daten:', formData);
        setTimeout(() => {
          setStatus('success');
          setFormData({
            name: '',
            email: '',
            phone: '',
            message: '',
            subject: 'Allgemeine Anfrage'
          });
        }, 1000);
        return;
      }

      // Verwende den universellen Form-Handler mit type=contact
      const response = await fetch('/universal-form-handler.php?type=contact', {
        method: 'POST',
        body: formDataObj
      });
      
      // Parse JSON-Antwort
      const data = await response.json();
      
      if (data.success) {
        setStatus('success');
        setFormData({
          name: '',
          email: '',
          phone: '',
          message: '',
          subject: 'Allgemeine Anfrage'
        });
      } else {
        throw new Error(data.message || 'Ein Fehler ist aufgetreten.');
      }
    } catch (error) {
      console.error('Kontaktformular-Fehler:', error);
      setStatus('error');
      setErrorMessage(
        error.message || 
        'Bei der Übermittlung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.'
      );
    }
  };

  return (
    <div className="py-12 bg-gradient-to-b from-gray-50 to-white">
      <div className="max-w-4xl mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-[var(--secondary)] mb-4">Kontaktieren Sie uns</h1>
          <p className="text-xl text-gray-600">
            Haben Sie Fragen zu housnkuh? Wir sind für Sie da!
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          {/* Kontakt-Infos */}
          <div className="col-span-1">
            <div className="bg-white rounded-lg shadow-lg p-6 h-full">
              <h2 className="text-2xl font-bold text-[var(--secondary)] mb-6">Kontaktdaten</h2>
              
              <div className="space-y-6">
                <div className="flex items-start">
                  <Mail className="w-5 h-5 text-[var(--primary)] mt-1 mr-3 flex-shrink-0" />
                  <div>
                    <h3 className="font-semibold mb-1">E-Mail</h3>
                    <p className="text-gray-600">eva-maria.schaller@housnkuh.de</p>
                  </div>
                </div>
                
                <div className="flex items-start">
                  <Phone className="w-5 h-5 text-[var(--primary)] mt-1 mr-3 flex-shrink-0" />
                  <div>
                    <h3 className="font-semibold mb-1">Telefon</h3>
                    <p className="text-gray-600">0157 35711257</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Kontaktformular */}
          <div className="col-span-1 md:col-span-2">
            <div className="bg-white rounded-lg shadow-lg p-6">
              {status === 'success' ? (
                <div className="flex flex-col items-center justify-center text-center py-8">
                  <div className="bg-green-100 rounded-full p-3 mb-4">
                    <Check className="w-8 h-8 text-green-600" />
                  </div>
                  <h3 className="text-2xl font-bold text-[var(--secondary)] mb-2">Nachricht gesendet!</h3>
                  <p className="text-gray-600 mb-6">
                    Vielen Dank für Ihre Nachricht. Wir werden uns so schnell wie möglich bei Ihnen melden.
                  </p>
                  <button
                    onClick={() => setStatus('idle')}
                    className="bg-[var(--primary)] text-white px-6 py-2 rounded-lg hover:bg-opacity-90 transition-all duration-200"
                  >
                    Neue Nachricht senden
                  </button>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                      Name
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                      E-Mail
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
                      Telefon (optional)
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
                  <div>
                    <label htmlFor="subject" className="block text-sm font-medium text-gray-700 mb-1">
                      Betreff
                    </label>
                    <select
                      id="subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                    >
                      <option value="Allgemeine Anfrage">Allgemeine Anfrage</option>
                      <option value="Verkaufsfläche mieten">Verkaufsfläche mieten</option>
                      <option value="Kooperation">Kooperation</option>
                      <option value="Feedback">Feedback</option>
                    </select>
                  </div>

                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                      Nachricht
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      rows="5"
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors"
                      required
                    ></textarea>
                  </div>

                  {status === 'error' && (
                    <div className="p-4 bg-red-50 text-red-700 rounded-lg flex items-start">
                      <AlertTriangle className="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" />
                      <p>{errorMessage}</p>
                    </div>
                  )}

                  <button
                    type="submit"
                    disabled={status === 'submitting'}
                    className="bg-[var(--primary)] text-white px-6 py-3 rounded-lg hover:bg-opacity-90 
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
                        <span>Nachricht senden</span>
                        <Send className="w-5 h-5" />
                      </>
                    )}
                  </button>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Contact;