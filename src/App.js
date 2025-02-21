import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navigation from './components/Navigation';
import Home from './pages/Home';
import Pricing from './pages/Pricing'; 
import Impressum from './pages/Impressum';// Achte auf die Kleinschreibung wegen deiner filename
import Datenschutz from './pages/Datenschutz';
import Footer from './components/Footer';
import CookieBanner from './components/CookieBanner';
import Location from './pages/Location';

function App() {
  return (
    <Router>
      <CookieBanner />
      <div className="min-h-screen flex flex-col">
        <Navigation />
        <main className="flex-grow pt-20">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/pricing" element={<Pricing />} />
            <Route path="/impressum" element={<Impressum />} />
            <Route path="/datenschutz" element={<Datenschutz />} />
            <Route path="/location" element={<Location />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;