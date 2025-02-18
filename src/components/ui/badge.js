import React from 'react';

export const Badge = ({ children, className = '' }) => {
  return (
    <span className={`inline-block bg-blue-100 text-blue-800 rounded px-2 py-1 text-xs font-semibold ${className}`}>
      {children}
    </span>
  );
};