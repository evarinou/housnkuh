import React from 'react';
import { Calendar, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import logo from '../assets/logo.svg';

const Hero = () => {
  return (
    <section className="relative pt-32 pb-20 overflow-hidden">
      {/* Hintergrundgrafik */}
      <div className="absolute inset-0 -z-10 bg-gradient-to-b from-white to-gray-50"></div>
      <div className="absolute -top-10 right-0 -z-10 transform translate-x-1/3">
        <svg width="404" height="404" fill="none" viewBox="0 0 404 404" aria-hidden="true">
          <defs>
            <pattern id="pattern-circles" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="1.5" fill="var(--primary)" opacity="0.1" />
            </pattern>
          </defs>
          <rect width="404" height="404" fill="url(#pattern-circles)" />
        </svg>
      </div>
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="lg:flex lg:items-center lg:justify-between">
          <div className="lg:w-7/12 xl:w-6/12">
            <div className="mb-8 animate-fadeIn">
              <img 
                src={logo} 
                alt="housnkuh Logo" 
                className="h-20 w-auto mb-6 animate-slideUp" 
              />
              <h1 className="text-4xl md:text-5xl font-bold text-[var(--secondary)] mb-6 leading-tight animate-slideDown">
                Willkommen bei housnkuh
              </h1>
              <p className="text-xl md:text-2xl text-[var(--primary)] font-semibold mb-4 animate-slideUp">
                Ihr innovativer Marktplatz für regionale Produkte in Kronach
              </p>
              <div className="flex items-center text-gray-600 animate-fadeIn mt-6">
                <Calendar className="w-5 h-5 mr-2 text-[var(--primary)]" />
                <p className="text-lg">Eröffnung im Frühsommer 2025</p>
              </div>
            </div>
            
            <div className="flex flex-col sm:flex-row gap-4 mt-8 animate-fadeIn">
              <Link 
                to="/vendors" 
                className="bg-[var(--primary)] text-white px-6 py-3 rounded-lg hover:bg-[var(--primary)]/90 
                        transition-all duration-200 text-center font-medium shadow-md"
              >
                Direktvermarkter entdecken
              </Link>
              <Link 
                to="/pricing" 
                className="border-2 border-[var(--secondary)] text-[var(--secondary)] px-6 py-3 rounded-lg 
                        hover:bg-[var(--secondary)] hover:text-white transition-all duration-200 
                        flex items-center justify-center gap-2 font-medium"
              >
                <span>Verkaufsfläche mieten</span>
                <ArrowRight className="w-5 h-5" />
              </Link>
            </div>
          </div>
          
          <div className="hidden lg:block lg:w-5/12 xl:w-6/12 mt-12 lg:mt-0">
            <div className="relative">
              {/* Hauptbild des Ladens (Platzhalter) */}
              <div className="rounded-lg shadow-2xl overflow-hidden transform rotate-2 hover:rotate-0 transition-transform duration-500">
                <img 
                  src="/api/placeholder/600/400" 
                  alt="housnkuh Marktplatz Konzept" 
                  className="w-full h-auto" 
                />
              </div>
              
              {/* Dekorative Elemente */}
              <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-[var(--primary)]/10 rounded-full"></div>
              <div className="absolute -top-8 -right-8 w-24 h-24 bg-[var(--secondary)]/10 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;