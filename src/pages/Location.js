import React from 'react';
import { MapPin, Clock, Phone, Mail, Car, Bus } from 'lucide-react';
import LocationMap from './LocationMap';

const LocationFeature = ({ icon: Icon, title, children }) => (
  <div className="flex items-start">
    <Icon className="w-6 h-6 text-[var(--primary)] mr-3 mt-1 flex-shrink-0" />
    <div>
      <h3 className="font-semibold mb-2">{title}</h3>
      {children}
    </div>
  </div>
);

const ParkingSpot = ({ location }) => (
  <li className="flex items-center space-x-2">
    <span className="w-2 h-2 bg-[var(--primary)] rounded-full" />
    <span className="text-gray-600">{location}</span>
  </li>
);

const HistorySection = () => (
  <div className="bg-gradient-to-br from-gray-50 to-white rounded-lg p-8 mt-12">
    <h3 className="text-xl font-semibold mb-4">Geschichte des Standorts</h3>
    <p className="text-gray-600 mb-4">
      Die Immobilie in der Strauer Straße 15 blickt auf eine lange Tradition im Einzelhandel zurück. 
      Ursprünglich beherbergte sie die Cammerer-Drogerie und später die Buchhandlung LeseZeichen.
    </p>
    <p className="text-gray-600">
      Ab Sommer 2025 wird sie als innovativer Marktplatz für regionale Produkte eine neue Ära einläuten.
    </p>
  </div>
);

const Location = () => {
  return (
    <div className="py-12 bg-gradient-to-b from-gray-50 to-white">
      <div className="max-w-6xl mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-[var(--secondary)] mb-4">Unser Standort</h1>
          <p className="text-xl text-gray-600">
            Zentral in Kronach – Ihre neue Anlaufstelle für regionale Produkte
          </p>
        </div>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Map Section */}
          <LocationMap />
          
          {/* Info Section */}
          <div className="bg-white rounded-lg shadow-lg p-8 space-y-6">
            <LocationFeature icon={MapPin} title="Adresse">
              <p className="text-gray-600">Strauer Str. 15</p>
              <p className="text-gray-600">96317 Kronach</p>
              <p className="text-sm text-gray-500 mt-2">
                Hinweis: Der Laden ist über eine Treppe erreichbar
              </p>
            </LocationFeature>
            
            <LocationFeature icon={Clock} title="Öffnungszeiten">
              <p className="text-gray-600">24 Stunden täglich</p>
              <p className="text-gray-600">7 Tage die Woche</p>
              <p className="text-sm text-gray-500 mt-1">
                Zugang mit EC- oder Kreditkarte
              </p>
            </LocationFeature>
            
            <LocationFeature icon={Phone} title="Telefon">
              <p className="text-gray-600">0157 35711257</p>
            </LocationFeature>
            
            <LocationFeature icon={Mail} title="E-Mail">
              <p className="text-gray-600">evam.schaller@gmail.com</p>
            </LocationFeature>

            <LocationFeature icon={Bus} title="Öffentliche Verkehrsmittel">
              <p className="text-gray-600">Bushaltestelle "Strauer Straße" direkt vor dem Laden</p>
            </LocationFeature>
          </div>
        </div>
        
        {/* Parking Section */}
        <div className="mt-12 bg-white rounded-lg shadow-lg p-8 transform transition-all duration-300 hover:shadow-xl">
          <div className="flex items-center mb-6">
            <Car className="w-6 h-6 text-[var(--primary)] mr-2" />
            <h3 className="text-xl font-semibold">Parkmöglichkeiten</h3>
          </div>
          <ul className="space-y-3">
            <ParkingSpot location="Direkt vor dem Laden (evangelische Kirche)" />
            <ParkingSpot location="Andreas Limmer Straße (50m entfernt)" />
            <ParkingSpot location="Entlang der Strauer Straße" />
            <ParkingSpot location="Entlang der Friesener Straße" />
          </ul>
        </div>

        <HistorySection />
      </div>
    </div>
  );
};

export default Location;