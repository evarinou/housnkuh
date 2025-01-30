import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="bg-gray-800 text-white mt-16">
      <div className="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-lg font-semibold mb-4">Kontakt</h3>
            <div className="space-y-2">
              <p>Strauer Str. 15</p>
              <p>96317 Kronach</p>
              <p>Tel: 0157/35711257</p>
              <p>E-Mail: evam.schaller@gmail.com</p>
            </div>
          </div>

          <div>
            <h3 className="text-lg font-semibold mb-4">Öffnungszeiten</h3>
            <div className="space-y-2">
              <p>Information folgt</p>
              <p>7 Tage die Woche</p>
            </div>
          </div>

          <div>
            <h3 className="text-lg font-semibold mb-4">Rechtliches</h3>
            <div className="space-y-2">
              <p><Link to="/impressum" className="hover:text-blue-400">Impressum</Link></p>
              <p><Link to="/datenschutz" className="hover:text-blue-400">Datenschutz</Link></p>
              <p><Link to="/agb" className="hover:text-blue-400">AGB</Link></p>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;