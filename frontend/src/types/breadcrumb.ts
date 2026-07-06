// types/breadcrumb.ts
export interface BreadcrumbItem {
  label: string;
  href?: string; // Optional - agar href nahi hai toh current page
  icon?: React.ReactNode; // Optional icon
}

export interface BreadcrumbProps {
  items: BreadcrumbItem[];
  separator?: React.ReactNode; // Custom separator
  className?: string;
}
