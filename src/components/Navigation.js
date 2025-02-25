import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Menu, X } from 'lucide-react';
import logo from '../assets/logo.svg';

const Navigation = () => {
  const [isOpen, setIsOpen] = useState(false);

  return (
    <nav className="fixed top-0 w-full bg-white shadow-lg z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-32 items-center">
          {/* Logo */}
          <div className="flex-shrink-0 flex items-center">
            <Link to="/">
              <img className="h-24 w-auto" src={logo} alt="Logo" />
            </Link>
          </div>

          {/* Desktop Navigation - Optimiert für Quicksand */}
          <div className="hidden md:flex items-center space-x-8">
            <Link 
              to="/vendors" 
              className="text-primary font-medium hover:text-secondary transition-colors duration-200"
            >
              Direktvermarkter
            </Link>
            <Link 
              to="/location" 
              className="text-primary font-medium hover:text-secondary transition-colors duration-200"
            >
              Standort
            </Link>
            <Link 
              to="/pricing"
              className="text-primary font-medium hover:text-secondary transition-colors duration-200"
            >
              Verkaufsfläche mieten
            </Link>
          </div>

          {/* Mobile Menu Button */}
          <div className="flex items-center md:hidden">
            <button 
              onClick={() => setIsOpen(!isOpen)} 
              className="focus:outline-none text-primary hover:text-secondary transition-colors duration-200"
            >
              {isOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </div>
      </div>
      
      {/* Mobile Navigation - Optimiert für Quicksand */}
      {isOpen && (
        <div className="md:hidden bg-white">
          <div className="px-2 pt-2 pb-3 space-y-1">
            <Link
              to="/vendors"
              className="block px-3 py-2 text-primary font-medium hover:text-secondary transition-colors duration-200"
              onClick={() => setIsOpen(false)}
            >
              Direktvermarkter
            </Link>
            <Link
              to="/location"
              className="block px-3 py-2 text-primary font-medium hover:text-secondary transition-colors duration-200"
              onClick={() => setIsOpen(false)}
            >
              Standort
            </Link>
            <Link
              to="/pricing"
              className="block px-3 py-2 text-primary font-medium hover:text-secondary transition-colors duration-200"
              onClick={() => setIsOpen(false)}
            >
              Verkaufsfläche mieten
            </Link>
          </div>
        </div>
      )}
    </nav>
  );
};

export default Navigation;