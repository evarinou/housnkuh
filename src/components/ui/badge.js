//// filepath: /c:/Users/evams/github-Projekte/housnkuh/src/components/ui/badge.js

import React from 'react';

export const Badge = ({ children, variant, className = '' }) => {
  let bgColor = 'bg-gray-200';
  let textColor = 'text-gray-800';

  if (variant === 'secondary') {
    bgColor = 'bg-blue-100';
    textColor = 'text-blue-800';
  }

  return (
    <span className={`inline-block ${bgColor} ${textColor} rounded px-2 py-1 text-xs font-semibold ${className}`}>
      {children}
    </span>
  );
};