import React, { useState, useEffect } from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '../components/ui/card';
import { Badge } from '../components/ui/badge';
import { Store, ShoppingBag, PackageSearch, Box, Check, ArrowUp } from 'lucide-react';

const ScrollToTop = () => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const toggleVisibility = () => {
      if (window.pageYOffset > 300) {
        setIsVisible(true);
      } else {
        setIsVisible(false);
      }
    };

    window.addEventListener('scroll', toggleVisibility);
    return () => window.removeEventListener('scroll', toggleVisibility);
  }, []);

  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });
  };

  return isVisible ? (
    <button
      onClick={scrollToTop}
      className="fixed bottom-8 right-8 bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-200"
    >
      <ArrowUp className="w-6 h-6" />
    </button>
  ) : null;
};

const SubNavigation = ({ sections }) => {
  const scrollToSection = (id) => {
    const element = document.getElementById(id);
    if (element) {
      const offset = element.offsetTop - 150; // Account for sticky header
      window.scrollTo({
        top: offset,
        behavior: 'smooth',
      });
    }
  };

  return (
    <div className="sticky top-16 bg-white shadow-sm z-10">
      <div className="max-w-6xl mx-auto">
        <div className="flex gap-8 p-4 overflow-x-auto">
          {sections.map((section) => (
            <button
              key={section.id}
              onClick={() => scrollToSection(section.id)}
              className="text-gray-600 hover:text-blue-500 whitespace-nowrap transition-colors"
            >
              {section.title}
            </button>
          ))}
        </div>
      </div>
    </div>
  );
};

const PricingCard = ({ title, price, features, icon: Icon, highlight }) => (
  <Card className={`w-full transition-all duration-200 hover:shadow-lg ${highlight ? 'border-blue-500 border-2' : ''}`}>
    <CardHeader>
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Icon className="w-8 h-8 text-blue-500" />
          <div>
            <CardTitle className="text-xl">{title}</CardTitle>
            <div className="mt-2">
              <span className="text-3xl font-bold">{price}</span>
              <span className="text-gray-500 ml-1">/Monat</span>
            </div>
          </div>
        </div>
      </div>
    </CardHeader>
    <CardContent>
      <div className="grid grid-cols-2 gap-4">
        {features.map((feature, index) => (
          <div key={index} className="flex items-center gap-2">
            <Check className="w-4 h-4 text-green-500" />
            <span className="text-gray-600">{feature}</span>
          </div>
        ))}
      </div>
    </CardContent>
  </Card>
);

const PricingSection = () => {
  const sections = [
    { id: 'basis-pakete', title: 'Basis-Pakete' },
    { id: 'zusatzservices', title: 'Zusätzliche Services' },
    { id: 'kontakt', title: 'Kontakt' },
  ];

  const packages = [
    {
      title: "Verkaufsblock A-Lage",
      price: "35€",
      features: [
        "Premium Position auf Augenhöhe",
        "80x39x67cm Verkaufsfläche",
        "2 Ebenen verfügbar",
        "Beste Sichtbarkeit",
        "Individuelle Gestaltung",
        "Herstellerpräsentation inklusive"
      ],
      icon: Store,
      highlight: true
    },
    {
      title: "Verkaufsblock B-Lage",
      price: "15€",
      features: [
        "Standard Position",
        "80x39x33,5cm Verkaufsfläche",
        "1 Ebene verfügbar",
        "Gute Sichtbarkeit",
        "Individuelle Gestaltung",
        "Herstellerpräsentation inklusive"
      ],
      icon: ShoppingBag
    },
    {
      title: "Kühlbereich",
      price: "50€",
      features: [
        "Temperaturkontrollierte Fläche",
        "Konstante Kühlung",
        "Digitale Temperaturüberwachung",
        "Automatische Warnmeldungen",
        "Optimale Produktpräsentation",
        "Tägliche Qualitätskontrolle"
      ],
      icon: Box
    }
  ];

  const additionalServices = [
    {
      title: "Service-Pakete",
      price: "ab 20€",
      features: [
        "Lagerservice (20€/Monat)",
        "Schaufenster klein (30€/Monat)",
        "Schaufenster groß (60€/Monat)",
        "Social Media Spotlight (20€/Woche)",
        "Automatische Nachfüllung",
        "Bestandsmanagement"
      ],
      icon: PackageSearch
    }
  ];

  return (
    <>
      <SubNavigation sections={sections} />
      <div className="max-w-6xl mx-auto p-6 pt-24">
        <div className="mb-12">
          <h2 className="text-3xl font-bold text-center mb-4">Verkaufsflächen Mieten</h2>
          <div className="flex justify-center gap-2 mb-8 flex-wrap">
            <Badge variant="secondary" className="text-sm">5% Rabatt bei 6 Monaten</Badge>
            <Badge variant="secondary" className="text-sm">10% Rabatt bei 12 Monaten</Badge>
            <Badge variant="secondary" className="text-sm">Nur 5% Provision vom Umsatz</Badge>
          </div>
        </div>

        <div id="basis-pakete" className="space-y-6">
          <h3 className="text-2xl font-semibold mb-6">Basis-Pakete</h3>
          {packages.map((pkg, index) => (
            <PricingCard key={index} {...pkg} />
          ))}
        </div>

        <div id="zusatzservices" className="mt-16">
          <h3 className="text-2xl font-semibold mb-6">Zusätzliche Services</h3>
          <div className="space-y-6">
            {additionalServices.map((service, index) => (
              <PricingCard key={index} {...service} />
            ))}
          </div>
        </div>

        <div id="kontakt" className="mt-16 text-center bg-gray-50 p-6 rounded-lg">
          <h3 className="text-lg font-semibold mb-2">Interesse? Kontaktieren Sie uns!</h3>
          <p className="text-gray-600">
            Wir beraten Sie gerne bei der Auswahl der optimalen Verkaufsfläche
          </p>
        </div>
      </div>
      <ScrollToTop />
    </>
  );
};

export default PricingSection;