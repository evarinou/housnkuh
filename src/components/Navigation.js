import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Menu, X } from 'lucide-react';

const Navigation = () => {
  const [isOpen, setIsOpen] = useState(false);

  return (
    <nav className="fixed top-0 w-full bg-white shadow-lg z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16 items-center">
          <Link to="/" className="font-bold text-2xl text-blue-600">
            Housnkuh
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/vendors" className="text-gray-600 hover:text-blue-600">
              Direktvermarkter
            </Link>
            <Link to="/location" className="text-gray-600 hover:text-blue-600">
              Standort
            </Link>
            <Link to="/rent" className="text-gray-600 hover:text-blue-600">
              Verkaufsfläche mieten
            </Link>
          </div>

          {/* Mobile menu button */}
          <div className="md:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="text-gray-600 hover:text-blue-600"
            >
              {isOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        {isOpen && (
          <div className="md:hidden">
            <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
              <Link
                to="/vendors"
                className="block px-3 py-2 text-gray-600 hover:text-blue-600"
                onClick={() => setIsOpen(false)}
              >
                Direktvermarkter
              </Link>
              <Link
                to="/location"
                className="block px-3 py-2 text-gray-600 hover:text-blue-600"
                onClick={() => setIsOpen(false)}
              >
                Standort
              </Link>
              <Link
                to="/rent"
                className="block px-3 py-2 text-gray-600 hover:text-blue-600"
                onClick={() => setIsOpen(false)}
              >
                Verkaufsfläche mieten
              </Link>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navigation;