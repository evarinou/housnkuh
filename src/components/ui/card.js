//// filepath: /c:/Users/evams/github-Projekte/housnkuh/src/components/ui/card.js

import React from 'react';

export const Card = ({ children, className = '' }) => {
  return <div className={`bg-white rounded shadow p-4 ${className}`}>{children}</div>;
};

export const CardHeader = ({ children, className = '' }) => {
  return <div className={`border-b pb-2 mb-2 ${className}`}>{children}</div>;
};

export const CardTitle = ({ children, className = '' }) => {
  return <h3 className={`text-lg font-bold ${className}`}>{children}</h3>;
};

export const CardContent = ({ children, className = '' }) => {
  return <div className={className}>{children}</div>;
};