import React, { useState } from 'react';
import { Mail, Loader } from 'lucide-react';

const NewsletterSignup = () => {
  const [email, setEmail] = useState('');
  const [type, setType] = useState('customer');
  const [loading, setLoading] = useState(false);
  const [status, setStatus] = useState({
    type: '',
    message: ''
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setStatus({ type: '', message: '' });

    try {
      const response = await fetch('http://localhost:5000/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, type }),
      });

      const data = await response.json();

      if (response.ok) {
        setStatus({
          type: 'success',
          message: 'Vielen Dank für Ihre Anmeldung zum Newsletter!'
        });
        setEmail('');
      } else {
        setStatus({
          type: 'error',
          message: data.error || 'Ein Fehler ist aufgetreten.'
        });
      }
    } catch (error) {
      setStatus({
        type: 'error',
        message: 'Verbindungsfehler. Bitte versuchen Sie es später erneut.'
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-gradient-to-br from-gray-50 to-white rounded-lg shadow-lg p-8">
      <div className="flex items-center justify-center mb-6">
        <Mail className="w-8 h-8 text-[var(--primary)]" />
        <h2 className="text-2xl font-bold text-[var(--secondary)] ml-3">
          Newsletter
        </h2>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Ihre E-Mail-Adresse
          </label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[var(--primary)] focus:border-[var(--primary)]"
            placeholder="ihre-email@beispiel.de"
            required
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Ich bin...
          </label>
          <div className="flex gap-4">
            <label className="flex items-center">
              <input
                type="radio"
                value="customer"
                checked={type === 'customer'}
                onChange={(e) => setType(e.target.value)}
                className="text-[var(--primary)]"
              />
              <span className="ml-2">Kunde</span>
            </label>
            <label className="flex items-center">
              <input
                type="radio"
                value="vendor"
                checked={type === 'vendor'}
                onChange={(e) => setType(e.target.value)}
                className="text-[var(--primary)]"
              />
              <span className="ml-2">Direktvermarkter</span>
            </label>
          </div>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-[var(--primary)] text-white py-2 px-4 rounded-md hover:bg-[var(--secondary)] 
                   transition-colors duration-300 flex items-center justify-center"
        >
          {loading ? (
            <Loader className="animate-spin h-5 w-5" />
          ) : (
            'Newsletter abonnieren'
          )}
        </button>

        {status.message && (
          <div 
            className={`mt-4 p-4 rounded-md ${
              status.type === 'success' 
                ? 'bg-green-50 text-green-800' 
                : 'bg-red-50 text-red-800'
            }`}
          >
            {status.message}
          </div>
        )}
      </form>
    </div>
  );
};

export default NewsletterSignup;