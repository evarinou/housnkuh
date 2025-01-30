import React from 'react';
import { MapPin, Clock, Phone, Mail } from 'lucide-react';

const Location = () => {
  return (
    <div className="py-12 bg-gray-50">
      <div className="max-w-6xl mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-12">Unser Standort</h2>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Map Placeholder */}
          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="h-96 bg-gray-200 flex items-center justify-center">
              <MapPin size={48} className="text-gray-400" />
              <span className="ml-2 text-gray-500">Karte wird geladen...</span>
            </div>
          </div>
          
          {/* Info Section */}
          <div className="bg-white rounded-lg shadow-md p-8">
            <div className="space-y-6">
              <div className="flex items-start">
                <MapPin className="w-6 h-6 text-blue-600 mr-3 mt-1" />
                <div>
                  <h3 className="font-semibold mb-2">Adresse</h3>
                  <p>Strauer Str. 15</p>
                  <p>96317 Kronach</p>
                </div>
              </div>
              
              <div className="flex items-start">
                <Clock className="w-6 h-6 text-blue-600 mr-3 mt-1" />
                <div>
                  <h3 className="font-semibold mb-2">Öffnungszeiten</h3>
                  <p>24 Stunden täglich</p>
                  <p>7 Tage die Woche</p>
                  <p className="text-sm text-gray-600 mt-1">
                    (Zugang mit EC- oder Kreditkarte)
                  </p>
                </div>
              </div>
              
              <div className="flex items-start">
                <Phone className="w-6 h-6 text-blue-600 mr-3 mt-1" />
                <div>
                  <h3 className="font-semibold mb-2">Telefon</h3>
                  <p>0157 35711257</p>
                </div>
              </div>
              
              <div className="flex items-start">
                <Mail className="w-6 h-6 text-blue-600 mr-3 mt-1" />
                <div>
                  <h3 className="font-semibold mb-2">E-Mail</h3>
                  <p>evam.schaller@gmail.com</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        {/* Parkmöglichkeiten */}
        <div className="mt-12 bg-white rounded-lg shadow-md p-8">
          <h3 className="text-xl font-semibold mb-4">Parkmöglichkeiten</h3>
          <ul className="space-y-2 text-gray-600">
            <li className="flex items-center">
              <span className="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
              Direkt vor dem Laden (evangelische Kirche)
            </li>
            <li className="flex items-center">
              <span className="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
              Andreas Limmer Straße (50m entfernt)
            </li>
            <li className="flex items-center">
              <span className="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
              Entlang der Strauer Straße
            </li>
            <li className="flex items-center">
              <span className="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
              Entlang der Friesener Straße
            </li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default Location;