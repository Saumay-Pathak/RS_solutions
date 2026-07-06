import React, { ReactNode, MouseEventHandler } from 'react';

type CTAButtonProps = {
  children: ReactNode;
  variant?: 'primary' | 'yellow' | 'ghost';
  onClick?: MouseEventHandler<HTMLButtonElement | HTMLAnchorElement>;
  href?: string;
};

const CTAButton: React.FC<CTAButtonProps> = ({ children, variant = 'primary', onClick, href }) => {
  const base = 'inline-flex items-center gap-2 text-[12px] lg:text-[16px] px-2 lg:px-4 py-2 rounded-md font-[400] shadow-sm transition cursor-pointer';
  const variants: Record<'primary' | 'yellow' | 'ghost', string> = {
    primary: 'bg-[#EA5921] text-white hover:bg-orange-600',
    yellow: 'bg-yellow-500 text-black hover:bg-yellow-600',
    ghost: 'bg-white ring-1 ring-gray-200 text-gray-700 hover:bg-gray-50',
  };
  
  const cls = `${base} ${variants[variant] ?? variants.primary}`;

  if (href) {
    return (
      <a href={href} className={cls} onClick={onClick}>
        {children}
      </a>
    );
  }

  return (
    <button className={cls} onClick={onClick}>
      {children}
    </button>
  );
};

export default CTAButton;
