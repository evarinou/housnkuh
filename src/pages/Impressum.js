import React from 'react';

const Impressum = () => {
  return (
    <div className="py-12 bg-gray-50">
      <div className="max-w-3xl mx-auto px-4">
        <h1 className="text-3xl font-bold text-[var(--secondary)] mb-8 text-center">Impressum</h1>
        
        <div className="bg-white rounded-lg shadow-md p-8 space-y-6">
          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-3">Angaben gemäß § 5 TMG</h2>
            <p className="text-gray-700">
              Eva-Maria Schaller<br />
              Strauer Str. 15<br />
              96317 Kronach
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-3">Kontakt</h2>
            <p className="text-gray-700">
              Telefon: 0157 35711257<br />
              E-Mail: eva-maria.schaller@housnkuh.de
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-3">Umsatzsteuer-ID</h2>
            <p className="text-gray-700">
              Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br />
              folgt!
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-3">Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
            <p className="text-gray-700">
              Eva-Maria Schaller<br />
              Strauer Str. 15<br />
              96317 Kronach
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-3">Streitschlichtung</h2>
            <p className="text-gray-700">
              Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: 
              https://ec.europa.eu/consumers/odr.<br />
              Unsere E-Mail-Adresse finden Sie oben im Impressum.
            </p>
            <p className="text-gray-700 mt-3">
              Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer 
              Verbraucherschlichtungsstelle teilzunehmen.
            </p>
          </section>
        </div>
      </div>
    </div>
  );
};

export default Impressum;