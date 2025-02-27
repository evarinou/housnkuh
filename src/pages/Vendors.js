import React from 'react';
import VendorContest from '../components/VendorContest';

const Vendors = () => { /* const vendors = [
    {
      name: "Platzhalter Metzger",
      category: "Fleisch & Wurst",
      description: "Regionale Fleisch- und Wurstspezialitäten aus eigener Herstellung.",
      image: "/placeholder.jpg"
    },
    {
      name: "Imkerei Platzhalter",
      category: "Honig",
      description: "Lokaler Honig aus nachhaltiger Imkerei.",
      image: "/placeholder.jpg"
    },
    {
      name: "Platzhalter Kaffee",
      category: "Kaffee",
      description: "Frisch gerösteter Kaffee aus der Region.",
      image: "/placeholder.jpg"
    }
    // Weitere Direktvermarkter hier
  ];*/

  return (
    <div className="py-12 bg-gray-50">
      <div className="max-w-6xl mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-8">Die Direktvermarkter</h2>
        
        {/* Informationsbox mit Animation */}
        <div className="bg-white rounded-lg shadow-md p-8 mb-12 transform transition-all duration-300 hover:shadow-lg">
          <h3 className="text-2xl font-semibold text-[var(--secondary)] mb-4">Wer kommt zu housnkuh?</h3>
          <p className="text-gray-600 mb-4">
            Die Housnkuh ist gerade dabei, ein vielfältiges Netzwerk regionaler Direktvermarkter aufzubauen. 
            Ab Sommer 2025 werden Sie hier eine große Auswahl an Anbietern finden, die ihre hochwertigen 
            Produkte in unserem Marktplatz präsentieren.
          </p>
          <p className="text-gray-600">
            Haben Sie einen Favoriten aus der Region, den Sie gerne bei uns sehen würden? 
            Nehmen Sie an unserem Wettbewerb teil und erraten Sie, welche Direktvermarkter bei 
            unserer Eröffnung dabei sein werden!
          </p>
        </div>
        
        {/* Wettbewerbskomponente einfügen */}
        <VendorContest />
        {/*
        <h3 className="text-2xl font-semibold text-center mb-8 mt-16">Beispiel-Direktvermarkter</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {vendors.map((vendor, index) => (
            <div key={index} className="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
              <div className="aspect-w-16 aspect-h-9">
                <img 
                  src={vendor.image} 
                  alt={vendor.name}
                  className="w-full h-48 object-cover" 
                />
              </div>
              <div className="p-6">
                <span className="text-sm text-[var(--primary)] font-semibold">{vendor.category}</span>
                <h3 className="text-xl font-bold mt-2 mb-3 text-[var(--secondary)]">{vendor.name}</h3>
                <p className="text-gray-600">{vendor.description}</p>
              </div>
            </div>
          ))}
        </div>*/}
      </div>
    </div>
  );
};

export default Vendors;