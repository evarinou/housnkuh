import React from 'react';
import { Store, Users, Clock, ShoppingBag, ArrowRight } from 'lucide-react';

const ConceptGraphic = () => {
  return (
    <div className="max-w-4xl mx-auto py-10 px-4">
      <h2 className="text-2xl md:text-3xl text-center font-bold text-[#09122c] mb-8">
        So funktioniert housnkuh
      </h2>
      
      <div className="relative">
        {/* Desktop flow (hidden on mobile) */}
        <div className="hidden md:block">
          <div className="flex justify-between items-center">
            {/* Producer */}
            <div className="w-1/4 text-center">
              <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
                <Users className="w-10 h-10 text-[#e17564]" />
              </div>
              <h3 className="font-semibold text-lg mb-2">Direktvermarkter</h3>
              <p className="text-sm text-gray-600">mieten Verkaufsflächen im Laden</p>
            </div>
            
            {/* Arrow */}
            <div className="w-1/6 flex justify-center">
              <ArrowRight className="w-10 h-10 text-[#e17564]" />
            </div>
            
            {/* Store */}
            <div className="w-1/4 text-center">
              <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
                <Store className="w-10 h-10 text-[#e17564]" />
              </div>
              <h3 className="font-semibold text-lg mb-2">housnkuh Marktplatz</h3>
              <p className="text-sm text-gray-600">Selbstbedienungsladen mit regionalen Produkten</p>
            </div>
            
            {/* Arrow */}
            <div className="w-1/6 flex justify-center">
              <ArrowRight className="w-10 h-10 text-[#e17564]" />
            </div>
            
            {/* Customer */}
            <div className="w-1/4 text-center">
              <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
                <ShoppingBag className="w-10 h-10 text-[#e17564]" />
              </div>
              <h3 className="font-semibold text-lg mb-2">Kunden</h3>
              <p className="text-sm text-gray-600">kaufen rund um die Uhr regionale Produkte</p>
            </div>
          </div>
          
          {/* Special Feature */}
          <div className="mt-8 mx-auto max-w-xs text-center bg-[#09122c] rounded-lg py-3 px-4 text-white flex items-center justify-center gap-3">
            <Clock className="w-5 h-5" />
            <span className="font-medium">Fast 24/7 geöffnet mit Kartenzugang</span>
          </div>
        </div>
        
        {/* Mobile version (shown only on mobile) */}
        <div className="md:hidden space-y-8">
          {/* Producer */}
          <div className="text-center">
            <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
              <Users className="w-10 h-10 text-[#e17564]" />
            </div>
            <h3 className="font-semibold text-lg mb-2">Direktvermarkter</h3>
            <p className="text-sm text-gray-600">mieten Verkaufsflächen im Laden</p>
            <div className="my-3 flex justify-center">
              <ArrowRight className="w-6 h-6 text-[#e17564] transform rotate-90" />
            </div>
          </div>
          
          {/* Store */}
          <div className="text-center">
            <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
              <Store className="w-10 h-10 text-[#e17564]" />
            </div>
            <h3 className="font-semibold text-lg mb-2">housnkuh Marktplatz</h3>
            <p className="text-sm text-gray-600">Selbstbedienungsladen mit regionalen Produkten</p>
            <div className="my-3 flex justify-center">
              <ArrowRight className="w-6 h-6 text-[#e17564] transform rotate-90" />
            </div>
          </div>
          
          {/* Customer */}
          <div className="text-center">
            <div className="mx-auto bg-[#e17564] bg-opacity-10 p-4 rounded-full w-20 h-20 flex items-center justify-center mb-4">
              <ShoppingBag className="w-10 h-10 text-[#e17564]" />
            </div>
            <h3 className="font-semibold text-lg mb-2">Kunden</h3>
            <p className="text-sm text-gray-600">kaufen rund um die Uhr regionale Produkte</p>
          </div>
          
          {/* Special Feature */}
          <div className="mx-auto max-w-xs text-center bg-[#09122c] rounded-lg py-3 px-4 text-white flex items-center justify-center gap-3">
            <Clock className="w-5 h-5" />
            <span className="font-medium">Fast 24/7 geöffnet mit Kartenzugang</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ConceptGraphic;