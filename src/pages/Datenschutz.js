import React from 'react';

const Datenschutz = () => {
  return (
    <div className="py-12 bg-gray-50">
      <div className="max-w-3xl mx-auto px-4">
        <h1 className="text-3xl font-bold text-[var(--secondary)] mb-8 text-center">Datenschutzerklärung</h1>
        
        <div className="bg-white rounded-lg shadow-md p-8 space-y-8">
          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">1. Datenschutz auf einen Blick</h2>
            
            <h3 className="text-lg font-medium text-[var(--secondary)] mt-4 mb-2">Allgemeine Hinweise</h3>
            <p className="text-gray-700">
              Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten 
              passiert, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie 
              persönlich identifiziert werden können.
            </p>

            <h3 className="text-lg font-medium text-[var(--secondary)] mt-4 mb-2">Datenerfassung auf dieser Website</h3>
            <p className="text-gray-700">
              Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Die Kontaktdaten 
              können Sie dem Impressum dieser Website entnehmen.
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">2. Hosting</h2>
            <p className="text-gray-700">
              Unser Hoster erhebt in sog. Logfiles folgende Daten, die Ihr Browser übermittelt:
            </p>
            <ul className="list-disc list-inside text-gray-700 mt-2">
              <li>IP-Adresse</li>
              <li>Die Adresse der vorher besuchten Website (Referer Anfrage-Header)</li>
              <li>Datum und Uhrzeit der Anfrage</li>
              <li>Zeitzonendifferenz zur Greenwich Mean Time</li>
              <li>Inhalt der Anforderung</li>
              <li>HTTP-Statuscode</li>
              <li>Übertragene Datenmenge</li>
              <li>Website, von der die Anforderung kommt</li>
              <li>Informationen zu Browser und Betriebssystem</li>
            </ul>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">3. Datenerfassung im Laden</h2>
            <h3 className="text-lg font-medium text-[var(--secondary)] mt-4 mb-2">Videoüberwachung</h3>
            <p className="text-gray-700">
              In unserem Selbstbedienungsladen setzen wir Videoüberwachung ein. Die Überwachung dient der 
              Wahrung des Hausrechts, der Sicherheit unserer Kunden und der Verhinderung und Aufklärung von 
              Straftaten. Die Videoaufnahmen werden nach 72 Stunden automatisch gelöscht, sofern keine Vorfälle 
              eine längere Speicherung erforderlich machen.
            </p>

            <h3 className="text-lg font-medium text-[var(--secondary)] mt-4 mb-2">Zugangssystem</h3>
            <p className="text-gray-700">
              Für den Zugang zu unserem Laden außerhalb der üblichen Geschäftszeiten nutzen wir ein 
              Zugangssystem mit EC- oder Kreditkarte. Hierbei werden nur die für die Zugangskontrolle 
              notwendigen Daten erfasst und verarbeitet.
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">4. Newsletter</h2>
            <p className="text-gray-700">
              Wenn Sie den auf der Website angebotenen Newsletter beziehen möchten, benötigen wir von Ihnen eine 
              E-Mail-Adresse sowie Informationen, welche uns die Überprüfung gestatten, dass Sie der Inhaber der 
              angegebenen E-Mail-Adresse sind (Double-Opt-In). Weitere Daten werden nicht erhoben. Diese Daten 
              verwenden wir ausschließlich für den Versand der angeforderten Informationen und geben sie nicht 
              an Dritte weiter.
            </p>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">5. Ihre Rechte</h2>
            <p className="text-gray-700">
              Sie haben jederzeit das Recht:
            </p>
            <ul className="list-disc list-inside text-gray-700 mt-2">
              <li>Auskunft über Ihre gespeicherten personenbezogenen Daten zu erhalten</li>
              <li>Die Berichtigung unrichtiger personenbezogener Daten zu verlangen</li>
              <li>Die Löschung Ihrer bei uns gespeicherten personenbezogenen Daten zu verlangen</li>
              <li>Die Einschränkung der Datenverarbeitung zu verlangen</li>
              <li>Der Datenverarbeitung zu widersprechen</li>
              <li>Die Übertragung Ihrer Daten zu verlangen</li>
            </ul>
          </section>

          <section>
            <h2 className="text-xl font-semibold text-[var(--secondary)] mb-4">6. Kontakt</h2>
            <p className="text-gray-700">
              Wenn Sie Fragen zum Datenschutz haben, schreiben Sie mir bitte eine E-Mail an: eva-maria.schaller@housnkuh.de
            </p>
          </section>
        </div>
      </div>
    </div>
  );
};

export default Datenschutz;