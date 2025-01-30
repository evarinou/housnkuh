import React from 'react';

const Vendors = () => {
  const vendors = [
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
  ];

  return (
    <div className="py-12 bg-gray-50">
      <div className="max-w-6xl mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-12">Unsere Direktvermarkter</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {vendors.map((vendor, index) => (
            <div key={index} className="bg-white rounded-lg shadow-md overflow-hidden">
              <div className="aspect-w-16 aspect-h-9">
                <img 
                  src={vendor.image} 
                  alt={vendor.name}
                  className="w-full h-48 object-cover" 
                />
              </div>
              <div className="p-6">
                <span className="text-sm text-blue-600 font-semibold">{vendor.category}</span>
                <h3 className="text-xl font-bold mt-2 mb-3">{vendor.name}</h3>
                <p className="text-gray-600">{vendor.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Vendors;