import React, { useState, useEffect } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '../components/ui/card';
import { Badge } from '../components/ui/badge';
import { Store, Box, PackageSearch, ArrowUp, Info, Mail } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import RentalRequestForm from '../components/RentalRequestForm';

// ScrollToTop Komponente
const ScrollToTop = () => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const toggleVisibility = () => {
      setIsVisible(window.pageYOffset > 300);
    };
    window.addEventListener('scroll', toggleVisibility);
    return () => window.removeEventListener('scroll', toggleVisibility);
  }, []);

  return isVisible ? (
    <button
      onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
      className="fixed bottom-8 right-8 bg-[#e17564] text-white p-3 rounded-full shadow-lg hover:bg-opacity-90 transition-all duration-200"
      aria-label="Nach oben scrollen"
    >
      <ArrowUp className="w-6 h-6" />
    </button>
  ) : null;
};

// PricingCard Komponente
const PricingCard = ({ title, price, features, icon: Icon, highlight, description, minDuration, spaceType, onRentClick }) => (
  <Card className={`w-full transition-all duration-200 hover:shadow-lg ${
    highlight ? 'border-[#e17564] border-2 relative overflow-visible' : ''
  }`}>
    {highlight && (
      <span className="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-[#e17564] text-white px-4 py-1 rounded-full text-sm font-medium">
        Empfehlung
      </span>
    )}
    <CardHeader className="space-y-2">
      <div className="flex items-center gap-4">
        <Icon className="w-8 h-8 text-[#e17564]" />
        <div>
          <CardTitle className="text-xl font-bold text-[#09122c]">{title}</CardTitle>
          <p className="text-sm text-gray-600 font-medium">{description}</p>
        </div>
      </div>
      <div className="mt-4">
        <span className="text-3xl font-bold text-[#09122c]">{price}</span>
        <span className="text-gray-500 ml-1 font-medium">/Monat</span>
        {minDuration && (
          <p className="text-sm text-gray-500 mt-1 font-medium">Mindestlaufzeit: {minDuration}</p>
        )}
      </div>
    </CardHeader>
    <CardContent>
      <div className="space-y-4">
        {features.map((feature, index) => (
          <div key={index} className="flex items-start gap-2">
            <div className="mt-1 rounded-full p-1 bg-[#e17564] bg-opacity-10">
              <svg className="w-3 h-3 text-[#e17564]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span className="text-gray-600 font-medium">{feature}</span>
          </div>
        ))}
      </div>
      <button 
        onClick={() => onRentClick(spaceType)}
        className={`w-full mt-6 py-3 px-4 rounded-lg transition-all duration-200 font-semibold ${
          highlight 
            ? 'bg-[#e17564] text-white hover:bg-opacity-90 hover:transform hover:scale-105' 
            : 'border-2 border-[#e17564] text-[#e17564] hover:bg-[#e17564] hover:text-white'
        }`}
      >
        Jetzt mieten
      </button>
    </CardContent>
  </Card>
);

// PricingSection Komponente
const PricingSection = () => {
//  const navigate = useNavigate();
  const [showRentalForm, setShowRentalForm] = useState(false);
  const [selectedSpaceType, setSelectedSpaceType] = useState('regal-a');

  const handleRentClick = (spaceType) => {
    setSelectedSpaceType(spaceType);
    setShowRentalForm(true);
  };

  const packages = [
    {
      title: "Verkaufsblock A-Lage",
      price: "35€",
      description: "Premium-Position mit maximaler Sichtbarkeit",
      features: [
        "Premium Position auf Augenhöhe",
        "80x39x67cm Verkaufsfläche",
        "2 Ebenen verfügbar",
        "Beste Sichtbarkeit",
        "Individuelle Gestaltung",
        "Herstellerpräsentation inklusive"
      ],
      icon: Store,
      highlight: true,
      minDuration: "3 Monate",
      spaceType: "regal-a"
    },
    {
      title: "Verkaufsblock B-Lage",
      price: "15€",
      description: "Idealer Einstieg für neue Direktvermarkter",
      features: [
        "Standard Position",
        "80x39x33,5cm Verkaufsfläche",
        "1 Ebene verfügbar",
        "Gute Sichtbarkeit",
        "Individuelle Gestaltung",
        "Herstellerpräsentation inklusive"
      ],
      icon: Box,
      minDuration: "3 Monate",
      spaceType: "regal-b"
    },
    {
      title: "Service-Paket Plus",
      price: "ab 20€",
      description: "Zusätzliche Services für maximalen Erfolg",
      features: [
        "Lagerservice (20€/Monat)",
        "Schaufenster-Präsentation",
        "Social Media Promotion",
        "Automatische Nachfüllung",
        "Bestandsmanagement",
        "Verkaufsanalysen"
      ],
      icon: PackageSearch,
      minDuration: "1 Monat",
      spaceType: "service"
    }
  ];

  return (
    <div className="max-w-7xl mx-auto px-4 py-16">
      <div className="text-center mb-12">
        <h1 className="text-4xl font-bold text-[#09122c] mb-4 leading-tight">
          Verkaufsflächen Mieten
        </h1>
        <p className="text-xl text-gray-600 mb-8 font-medium">
          Präsentieren Sie Ihre Produkte dort, wo Ihre Kunden sie finden
        </p>
        <div className="flex flex-wrap justify-center gap-2">
          <Badge variant="secondary" className="bg-[#e17564] text-white font-medium">
            5% Rabatt bei 6 Monaten
          </Badge>
          <Badge variant="secondary" className="bg-[#e17564] text-white font-medium">
            10% Rabatt bei 12 Monaten
          </Badge>
          <Badge variant="secondary" className="bg-[#e17564] text-white font-medium">
            Nur 5% Provision vom Umsatz
          </Badge>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        {packages.map((pkg, index) => (
          <PricingCard 
            key={index} 
            {...pkg}
            onRentClick={handleRentClick}
          />
        ))}
      </div>

      <div className="bg-gray-50 border-l-4 border-[#e17564] p-4 rounded-r-lg mb-8">
        <div className="flex items-start">
          <Info className="w-5 h-5 text-[#e17564] mt-0.5 mr-3 flex-shrink-0" />
          <p className="text-gray-600">
            Alle Preise verstehen sich zzgl. 5% Provision vom Umsatz. Die Provision deckt Kartenzahlungsgebühren und Versicherung ab.
          </p>
        </div>
      </div>

      <div className="text-center">
        <h2 className="text-2xl font-semibold text-[#09122c] mb-4">
          Haben Sie Fragen?
        </h2>
        <p className="text-gray-600 mb-6">
          Wir beraten Sie gerne bei der Auswahl der optimalen Verkaufsfläche
        </p>
        <Link to="/contact">
          <button className="bg-[#e17564] text-white px-8 py-3 rounded-lg hover:bg-opacity-90 transition-colors duration-200 font-semibold flex items-center justify-center gap-2 mx-auto">
            <Mail className="w-5 h-5" />
            <span>Kontakt aufnehmen</span>
          </button>
        </Link>
      </div>

      {showRentalForm && (
        <RentalRequestForm 
          spaceType={selectedSpaceType}
          onClose={() => setShowRentalForm(false)}
        />
      )}

      <ScrollToTop />
    </div>
  );
};

export default PricingSection;