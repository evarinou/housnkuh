import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Menu, X } from 'lucide-react';
import logo from '../assets/logo.svg'; // Pfad anpassen, je nachdem, wo deine logo.svg liegt

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

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/vendors" className="text-[var(--primary)] hover:text-[var(--secondary)]">
              Direktvermarkter
            </Link>
            <Link to="/location" className="text-[var(--primary)] hover:text-[var(--secondary)]">
              Standort
            </Link>
            <Link to="/rent" className="text-[var(--primary)] hover:text-[var(--secondary)]">
              Verkaufsfläche mieten
            </Link>
          </div>

          {/* Mobile Menu Button */}
          <div className="flex items-center md:hidden">
            <button onClick={() => setIsOpen(!isOpen)} className="focus:outline-none">
              {isOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </div>
      </div>
      
      {/* Mobile Navigation */}
      {isOpen && (
        <div className="md:hidden">
          <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <Link
              to="/vendors"
              className="block px-3 py-2 text-[var(--primary)] hover:text-[var(--secondary)]"
              onClick={() => setIsOpen(false)}
            >
              Direktvermarkter
            </Link>
            <Link
              to="/location"
              className="block px-3 py-2 text-[var(--primary)] hover:text-[var(--secondary)]"
              onClick={() => setIsOpen(false)}
            >
              Standort
            </Link>
            <Link
              to="/rent"
              className="block px-3 py-2 text-[var(--primary)] hover:text-[var(--secondary)]"
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