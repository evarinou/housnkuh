import React from 'react';
import { Award, ArrowRight, Calendar } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const VendorContestHighlight = () => {
  const navigate = useNavigate();

  return (
    <section className="max-w-4xl mx-auto px-4 py-8 my-12">
      <div className="bg-gradient-to-br from-[#09122c] to-[#192254] text-white rounded-lg shadow-xl p-6 md:p-8 transform transition-all duration-300 hover:scale-105">
        <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
          <div className="flex-shrink-0 bg-[#e17564]/20 rounded-full p-4">
            <Award className="w-12 h-12 text-[#e17564] animate-pulse" />
          </div>
          
          <div className="flex-grow">
            <h2 className="text-2xl md:text-3xl font-bold mb-3">Direktvermarkter-Wettbewerb</h2>
            <p className="text-lg mb-3">
              Erraten Sie, welche drei lokalen Produzenten bei unserer Eröffnung dabei sein werden!
            </p>
            <div className="flex items-center text-[#e17564] mb-4">
              <Calendar className="w-5 h-5 mr-2" />
              <span className="font-medium">Teilnahmeschluss: 01.06.2025</span>
            </div>
          </div>
          
          <div className="mt-4 md:mt-0">
            <button
              onClick={() => navigate('/vendors')}
              className="whitespace-nowrap bg-[#e17564] text-white px-6 py-3 rounded-lg hover:bg-opacity-90 transition-all duration-200 font-medium inline-flex items-center gap-2"
            >
              <span>Jetzt teilnehmen</span>
              <ArrowRight className="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default VendorContestHighlight;