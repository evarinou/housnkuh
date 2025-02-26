// src/services/api.js

import axios from 'axios';

// Basisinstanz für alle API-Anfragen
const apiClient = axios.create({
  timeout: 10000, // 10 Sekunden Timeout
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// URLs basierend auf Umgebung
const getApiUrl = (endpoint) => {
  const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  
  // Verwende in Produktion den Form-Handler für alle Anfragen
  if (!isLocal) {
    return `/form-handler.php?type=${endpoint}`;
  }
  
  // Im Entwicklungsmodus können wir Mock-Daten simulieren
  return `/api/${endpoint}`;
};

// API Service für alle Formulare
const apiService = {
  // Newsletter-Anmeldung
  subscribeToNewsletter: async (email, type = 'customer') => {
    try {
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Simuliere erfolgreiche Antwort in der Entwicklung
        return new Promise(resolve => {
          setTimeout(() => {
            resolve({ 
              data: { 
                success: true, 
                message: 'Vielen Dank für Ihre Anmeldung!' 
              } 
            });
          }, 1000);
        });
      }
      
      const response = await apiClient.post(getApiUrl('newsletter'), {
        email,
        type
      });
      
      return response;
    } catch (error) {
      console.error('Newsletter Subscription Error:', error);
      throw error;
    }
  },
  
  // Kontaktformular
  submitContactForm: async (formData) => {
    try {
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Simuliere erfolgreiche Antwort in der Entwicklung
        return new Promise(resolve => {
          setTimeout(() => {
            resolve({ 
              data: { 
                success: true, 
                message: 'Vielen Dank für Ihre Nachricht! Wir werden uns so schnell wie möglich bei Ihnen melden.' 
              } 
            });
          }, 1000);
        });
      }
      
      const response = await apiClient.post(getApiUrl('contact'), formData);
      return response;
    } catch (error) {
      console.error('Contact Form Error:', error);
      throw error;
    }
  },
  
  // Mietanfrage
  submitRentalRequest: async (formData) => {
    try {
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Simuliere erfolgreiche Antwort in der Entwicklung
        return new Promise(resolve => {
          setTimeout(() => {
            resolve({ 
              data: { 
                success: true, 
                message: 'Vielen Dank für Ihre Mietanfrage! Wir werden Ihre Anfrage prüfen und uns so schnell wie möglich bei Ihnen melden.' 
              } 
            });
          }, 1000);
        });
      }
      
      const response = await apiClient.post(getApiUrl('rental'), formData);
      return response;
    } catch (error) {
      console.error('Rental Request Error:', error);
      throw error;
    }
  },
  
  // Wettbewerbsteilnahme
  submitVendorContest: async (formData) => {
    try {
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Simuliere erfolgreiche Antwort in der Entwicklung
        return new Promise(resolve => {
          setTimeout(() => {
            resolve({ 
              data: { 
                success: true, 
                message: 'Vielen Dank für Ihre Teilnahme!' 
              } 
            });
          }, 1000);
        });
      }
      
      const response = await apiClient.post(getApiUrl('vendor-contest'), formData);
      return response;
    } catch (error) {
      console.error('Vendor Contest Error:', error);
      throw error;
    }
  }
};

export default apiService;