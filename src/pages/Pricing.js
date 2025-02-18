import React from 'react';
import { Card, CardHeader, CardTitle, CardContent } from '../components/ui/card';
import { Badge } from '../components/ui/badge';
import { Store, ShoppingBag, PackageSearch, Box, Check } from 'lucide-react';

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
    <div className="max-w-6xl mx-auto p-6">
      <div className="mb-12">
        <h2 className="text-3xl font-bold text-center mb-4">Verkaufsflächen Mieten</h2>
        <div className="flex justify-center gap-2 mb-8">
          <Badge variant="secondary" className="text-sm">5% Rabatt bei 6 Monaten</Badge>
          <Badge variant="secondary" className="text-sm">10% Rabatt bei 12 Monaten</Badge>
          <Badge variant="secondary" className="text-sm">Nur 5% Provision vom Umsatz</Badge>
        </div>
      </div>

      <div className="space-y-6">
        {packages.map((pkg, index) => (
          <PricingCard key={index} {...pkg} />
        ))}
      </div>

      <div className="mt-8">
        <h3 className="text-xl font-semibold mb-6">Zusätzliche Services</h3>
        <div className="space-y-6">
          {additionalServices.map((service, index) => (
            <PricingCard key={index} {...service} />
          ))}
        </div>
      </div>

      <div className="mt-12 text-center bg-gray-50 p-6 rounded-lg">
        <h3 className="text-lg font-semibold mb-2">Interesse? Kontaktieren Sie uns!</h3>
        <p className="text-gray-600">
          Wir beraten Sie gerne bei der Auswahl der optimalen Verkaufsfläche
        </p>
      </div>
    </div>
  );
};

export default PricingSection;