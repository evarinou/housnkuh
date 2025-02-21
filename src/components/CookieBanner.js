import React, { useState, useEffect } from 'react';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from "./ui/alert-dialog";

const CookieBanner = () => {
  const [showBanner, setShowBanner] = useState(false);

  useEffect(() => {
    // Prüfe, ob Cookie-Einwilligung bereits existiert
    const consent = localStorage.getItem('cookie-consent');
    if (!consent) {
      setShowBanner(true);
    }
  }, []);

  const handleAccept = () => {
    localStorage.setItem('cookie-consent', 'accepted');
    setShowBanner(false);
  };

  const handleDecline = () => {
    localStorage.setItem('cookie-consent', 'declined');
    setShowBanner(false);
  };

  return (
    <AlertDialog open={showBanner}>
      <AlertDialogContent className="sm:max-w-[425px]">
        <AlertDialogHeader>
          <AlertDialogTitle className="text-[var(--secondary)]">Cookie-Einstellungen</AlertDialogTitle>
          <AlertDialogDescription className="text-gray-600">
            Wir verwenden Cookies, um Ihnen die bestmögliche Erfahrung auf unserer Website zu bieten. 
            Diese helfen uns zu verstehen, wie Sie unsere Website nutzen und ermöglichen grundlegende Funktionen.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter className="sm:justify-start">
          <AlertDialogAction
            onClick={handleAccept}
            className="bg-[var(--primary)] hover:bg-[var(--primary)]/90"
          >
            Alle akzeptieren
          </AlertDialogAction>
          <AlertDialogCancel
            onClick={handleDecline}
            className="text-gray-500 hover:text-gray-700 hover:bg-gray-100"
          >
            Nur notwendige Cookies
          </AlertDialogCancel>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
};

export default CookieBanner;