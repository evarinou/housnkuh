// In src/App.js
import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navigation from './components/Navigation';
import Home from './pages/Home';
import Pricing from './pages/Pricing'; 
import Impressum from './pages/Impressum';
import Datenschutz from './pages/Datenschutz';
import Footer from './components/Footer';
import CookieBanner from './components/CookieBanner';
import Location from './pages/Location';
import Contact from './pages/Contact';
import Vendors from './pages/Vendors';
import ContestBanner from './components/ContestBanner'; // Neue Komponente importieren

function App() {
  return (
    <Router>
      <CookieBanner />
      <ContestBanner /> {/* Hier wird die neue Contest-Banner Komponente eingebunden */}
      <div className="min-h-screen flex flex-col">
        <Navigation />
        <main className="flex-grow pt-20">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/pricing" element={<Pricing />} />
            <Route path="/impressum" element={<Impressum />} />
            <Route path="/datenschutz" element={<Datenschutz />} />
            <Route path="/location" element={<Location />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/vendors" element={<Vendors />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;