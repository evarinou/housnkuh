//// filepath: /c:/Users/evams/github-Projekte/housnkuh/src/components/NewsletterSignup.js
import React, { useState } from 'react';
import { Send, Check } from 'lucide-react';

const NewsletterSignup = () => {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState('idle'); // idle, submitting, success, error

  const handleSubmit = async (e) => {
    e.preventDefault();
    setStatus('submitting');
    
    // Simuliere API-Call
    setTimeout(() => {
      setStatus('success');
      setEmail('');
    }, 1000);
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
    </div>
  );
};

export default NewsletterSignup;