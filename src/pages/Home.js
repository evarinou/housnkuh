import React from 'react';
import { ShoppingBag, MapPin, Users } from 'lucide-react';

const Home = () => {
  return (
    <div className="space-y-16">
      {/* Hero Section */}
      <section className="bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl">
              Regionale Produkte.
              <span className="block text-blue-600">Rund um die Uhr.</span>
            </h1>
            <p className="mt-6 text-xl text-gray-600">
              Entdecken Sie die Vielfalt regionaler Produkte aus Kronach und Umgebung.
            </p>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-white rounded-lg shadow-lg p-6">
            <div className="text-blue-600 mb-4">
              <ShoppingBag size={32} />
            </div>
            <h2 className="text-xl font-semibold mb-2">Jeden Tag Einkaufen</h2>
            <p className="text-gray-600">Kaufen Sie ein, wann es Ihnen passt.</p>
          </div>

          <div className="bg-white rounded-lg shadow-lg p-6">
            <div className="text-blue-600 mb-4">
              <MapPin size={32} />
            </div>
            <h2 className="text-xl font-semibold mb-2">100% Regional</h2>
            <p className="text-gray-600">Alle Produkte aus Ihrer Region.</p>
          </div>

          <div className="bg-white rounded-lg shadow-lg p-6">
            <div className="text-blue-600 mb-4">
              <Users size={32} />
            </div>
            <h2 className="text-xl font-semibold mb-2">Direkt vom Erzeuger</h2>
            <p className="text-gray-600">Unterstützen Sie lokale Produzenten.</p>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;