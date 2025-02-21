import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="bg-[var(--secondary)] text-white mt-16">
      <div className="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Kontakt Sektion */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Kontakt</h3>
            <div className="space-y-2">
              <p>Strauer Str. 15</p>
              <p>96317 Kronach</p>
              <p>Tel: 0157/35711257</p>
              <p>E-Mail: eva-maria.schaller@housnkuh.de</p>
            </div>
          </div>

          {/* Öffnungszeiten Sektion */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Öffnungszeiten</h3>
            <div className="space-y-2">
              <p>Erweiterte Öffnungszeiten</p>
              <p>7 Tage die Woche</p>
              <p className="text-sm">(Zugang mit EC- oder Kreditkarte)</p>
            </div>
          </div>

          {/* Rechtliches Sektion */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Rechtliches</h3>
            <nav className="space-y-2">
              <p>
                <Link 
                  to="/impressum" 
                  className="hover:text-[var(--primary)] transition-colors duration-200"
                >
                  Impressum
                </Link>
              </p>
              <p>
                <Link 
                  to="/datenschutz" 
                  className="hover:text-[var(--primary)] transition-colors duration-200"
                >
                  Datenschutz
                </Link>
              </p>
              <p>
                <Link 
                  to="/agb" 
                  className="hover:text-[var(--primary)] transition-colors duration-200"
                >
                  AGB
                </Link>
              </p>
            </nav>
          </div>

          {/* Social Media Sektion */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Social Media</h3>
            <nav className="space-y-2">
              <p>Folgen Sie uns bald auf:</p>
              <p>Instagram</p>
              <p>Facebook</p>
              <p>TikTok</p>
            </nav>
          </div>
        </div>
        
        
      </div>
    </footer>
  );
};

export default Footer;