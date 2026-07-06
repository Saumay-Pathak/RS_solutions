"use client";

import Link from "next/link";
import Image from "next/image";
import { usePathname, useRouter } from "next/navigation";
import { useState, useEffect, useRef, useCallback } from "react";
import axiosClient from "@/services/axiosClient";
import { baseUri } from "@/services/constant";
import ProductsMegaMenu from "./ProductsMegaMenu";
import SoftwareMegaMenu from "./SoftwareMegaMenu";
import SolutionsMegaMenu from "./SolutionsMegaMenu";
import FormModalPopUp from "../common/FormModalPopUp";

type ChildItem = {
  children?: ChildItem[];
  title: string | null;
  url: string;
  slug?: string;
};

type NavItem = {
  title: string | null;
  url: string;
  type: "dropdown" | "single";
  children?: ChildItem[];
};

type Branding = {
  site_title: string;
  site_tagline: string;
  logo_url: string;
  favicon_url?: string;
};

type Product = {
  id: string;
  title: string;
  slug: string;
  description: string;
  images: string[];
  category?: {
    name: string;
    slug: string;
    parent?: {
      name: string;
      slug: string;
    };
  };
};

type SearchResponse = {
  success: boolean;
  data: Product[];
};

interface HeaderProps {
  setIsMegaMenuOpen: (open: boolean) => void;
}

type HeaderData = {
  // Optional status to control FormModalPopUp visibility
  status?: {
    custom_activity_tracker?: boolean;
  };
  settings?: {
    show_search_in_header?: boolean;
  };
  branding: Branding;
  navigation: NavItem[];
  custom_css?: string;
  scripts?: {
    header_scripts?: string;
  };
};

let headerDataCache: HeaderData | null = null;
type HeaderFooterApps = {
  smart_app_link?: string;
  attendance_app_link?: string;
};

// Helper function to check if children array has valid items
const hasValidChildren = (children?: ChildItem[]): boolean => {
  if (!children || !Array.isArray(children) || children.length === 0) {
    return false;
  }
  return children.some((child) => child?.title && child.title.trim() !== "");
};

// Helper function to render child items safely
const renderChildItems = (children: ChildItem[], closeMenu?: () => void) => {
  if (!hasValidChildren(children)) {
    return null;
  }

  return children
    .flatMap((child, childIndex) => {
      if (!child?.title || child.title.trim() === "") {
        return null;
      }

      if (hasValidChildren(child.children)) {
        return (child.children as ChildItem[])
          .map((subChild, subIndex) => {
            if (!subChild?.title || subChild.title.trim() === "") {
              return null;
            }

            return (
              <Link
                key={`${childIndex}-${subIndex}`}
                href={subChild.url || "#"}
                onClick={closeMenu}
                className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-orange-600">
                {subChild.title}
              </Link>
            );
          })
          .filter(Boolean);
      }

      return (
        <Link
          key={childIndex}
          href={child.url || "#"}
          onClick={closeMenu}
          className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-orange-600">
          {child.title}
        </Link>
      );
    })
    .filter(Boolean);
};

// Responsive Mega Menu Wrapper Component - DESKTOP ONLY
const ResponsiveMegaMenu = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="hidden lg:block absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
      <div className="w-[90vw] max-w-[1100px] transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300 ease-out">
        {children}
      </div>
    </div>
  );
};

// Mobile Dropdown Component - SIMPLE DROPDOWN
const MobileDropdown = ({
  children,
  title,
}: {
  children: React.ReactNode;
  title?: string;
}) => {
  return (
    <div className="lg:hidden bg-white border border-gray-200 rounded-lg mx-4 my-2">
      <div className="text-gray-800 font-medium p-3 border-b border-gray-200 text-sm bg-gray-50 rounded-t-lg">
        {title}
      </div>
      <div className="py-2">{children}</div>
    </div>
  );
};

const Header = ({ setIsMegaMenuOpen }: HeaderProps) => {
  const pathname = usePathname();
  const router = useRouter();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState<Product[]>([]);
  const [showSearchDropdown, setShowSearchDropdown] = useState(false);
  const [isSearching, setIsSearching] = useState(false);
  const [activeDropdown, setActiveDropdown] = useState<string | null>(null);
  const [activeMegaMenu, setActiveMegaMenu] = useState<string | null>(null);
  // NEW: State for nested dropdowns
  const [openNestedDropdowns, setOpenNestedDropdowns] = useState<Set<string>>(new Set());
  const [headerData, setHeaderData] = useState<HeaderData | null>(
    headerDataCache
  );
  const [loading, setLoading] = useState(!headerDataCache)
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const [apps, setApps] = useState<HeaderFooterApps>({});

  const dropdownRef = useRef<HTMLDivElement>(null);
  const searchRef = useRef<HTMLDivElement>(null);
  const mobileMenuRef = useRef<HTMLDivElement>(null);
  const searchTimeoutRef = useRef<NodeJS.Timeout | null>(null);
  const lastScrollY = useRef(0);
  const lastToggleY = useRef(0);
  const ticking = useRef(false);
  const [isSecondaryBarHidden, setIsSecondaryBarHidden] = useState(false);
  const [isHeaderShrunk, setIsHeaderShrunk] = useState(false);
  const [isSearchFocused, setIsSearchFocused] = useState(false);

  useEffect(() => {
    const SHRINK_SCROLL_Y = 80;
    const SCROLL_HIDE_DISTANCE = 50; // distance to hide when scrolling down
    const SCROLL_SHOW_DISTANCE = 20; // distance to show when scrolling up

    const onScroll = () => {
      const currentY = window.scrollY;

      if (!ticking.current) {
        ticking.current = true;
        window.requestAnimationFrame(() => {
          const delta = currentY - lastScrollY.current;
          setIsHeaderShrunk(currentY > SHRINK_SCROLL_Y);
          // Always show when near top
          if (currentY < 10) {
            if (isSecondaryBarHidden) setIsSecondaryBarHidden(false);
            lastToggleY.current = currentY;
          } else if (!isSecondaryBarHidden) {
            // Currently shown: hide after sustained downward scroll
            if (delta > 0 && currentY - lastToggleY.current > SCROLL_HIDE_DISTANCE) {
              setIsSecondaryBarHidden(true);
              lastToggleY.current = currentY;
            }
          } else {
            // Currently hidden: show after sustained upward scroll
            if (delta < 0 && lastToggleY.current - currentY > SCROLL_SHOW_DISTANCE) {
              setIsSecondaryBarHidden(false);
              lastToggleY.current = currentY;
            }
          }

          lastScrollY.current = currentY;
          ticking.current = false;
        });
      }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    return () => {
      window.removeEventListener("scroll", onScroll);
    };
  }, [isSecondaryBarHidden, setIsHeaderShrunk]);

  // Update document meta tags and favicon
  const updateDocumentMetadata = useCallback((data: HeaderData) => {
    if (data?.branding) {
      const { site_title, site_tagline, favicon_url } = data.branding;

      if (site_title) {
        const currentTitle = document.title;
        if (!currentTitle.includes(site_title)) {
          document.title =
            site_title + (site_tagline ? ` | ${site_tagline}` : "");
        }
      }

      if (favicon_url) {
        let link = document.querySelector(
          "link[rel*='icon']"
        ) as HTMLLinkElement | null;

        if (!link) {
          link = document.createElement("link");
          link.rel = "icon";
          document.head.appendChild(link);
        }

        link.href = favicon_url;
      }

      if (site_tagline) {
        let metaDescription = document.querySelector(
          'meta[name="description"]'
        );

        if (!metaDescription) {
          metaDescription = document.createElement("meta");
          metaDescription.setAttribute("name", "description");
          document.head.appendChild(metaDescription);
        }

        metaDescription.setAttribute("content", site_tagline);
      }

      let ogTitle = document.querySelector('meta[property="og:title"]');
      if (!ogTitle) {
        ogTitle = document.createElement("meta");
        ogTitle.setAttribute("property", "og:title");
        document.head.appendChild(ogTitle);
      }
      ogTitle.setAttribute("content", site_title || "RealTime Biometrics");

      let ogDescription = document.querySelector(
        'meta[property="og:description"]'
      );
      if (!ogDescription) {
        ogDescription = document.createElement("meta");
        ogDescription.setAttribute("property", "og:description");
        document.head.appendChild(ogDescription);
      }
      ogDescription.setAttribute(
        "content",
        site_tagline || "Advanced Biometric Solutions"
      );
    }
  }, []);

  // Fetch header data
  useEffect(() => {
    if (headerDataCache) {
      setHeaderData(headerDataCache);
      updateDocumentMetadata(headerDataCache);
      setLoading(false);
      return;
    }

    const fetchHeader = async () => {
      try {
        const response = await axiosClient.get("/site/header");
        const data = response.data.data;
        headerDataCache = data;
        setHeaderData(data);
        updateDocumentMetadata(data);
      } catch (err) {
        console.error("Error fetching header:", err);
        const fallbackData: HeaderData = {
          branding: {
            site_title: "RealTime Biometrics",
            site_tagline: "Advanced Biometric Solutions",
            logo_url: "/logo.png",
            favicon_url: "/favicon.ico",
          },
          navigation: [],
          settings: { show_search_in_header: true },
        };
        headerDataCache = fallbackData;
        setHeaderData(fallbackData);
        updateDocumentMetadata(fallbackData);
      } finally {
        setLoading(false);
      }
    };

    fetchHeader();
  }, [updateDocumentMetadata]);

  // Fetch header-footer apps for dynamic app links
  useEffect(() => {
    let cancelled = false;
    const fetchHeaderFooter = async () => {
      try {
        const response = await axiosClient.get("/site/header-footer");
        const data = response.data?.data;
        const appsData: HeaderFooterApps = data?.apps || {};
        if (!cancelled) setApps(appsData);
      } catch (err) {
        console.warn("Error fetching header-footer apps:", err);
      }
    };
    fetchHeaderFooter();
    return () => {
      cancelled = true;
    };
  }, []);

  // Search function
  const performSearch = useCallback(async (query: string) => {
    if (!query.trim()) {
      setSearchResults([]);
      setShowSearchDropdown(false);
      return;
    }

    setIsSearching(true);
    try {
      const response = await axiosClient.get<SearchResponse>(
        `/content/products?search=${encodeURIComponent(query)}`
      );
      if (response.data.success) {
        setSearchResults(response.data.data);
        setShowSearchDropdown(response.data.data.length > 0);
      }
    } catch (err) {
      console.error("Search error:", err);
      setSearchResults([]);
    } finally {
      setIsSearching(false);
    }
  }, []);

  // Debounce search
  useEffect(() => {
    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current);
    }

    if (searchQuery.trim()) {
      searchTimeoutRef.current = setTimeout(() => {
        performSearch(searchQuery);
      }, 300);
    } else {
      setSearchResults([]);
      setShowSearchDropdown(false);
    }

    return () => {
      if (searchTimeoutRef.current) {
        clearTimeout(searchTimeoutRef.current);
      }
    };
  }, [searchQuery, performSearch]);

  // Click outside search container to collapse it
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (searchRef.current && !searchRef.current.contains(event.target as Node)) {
        setIsSearchFocused(false);
        setShowSearchDropdown(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  // NEW: Remove outside click handler completely
  // Handle body scroll
  useEffect(() => {
    if (mobileMenuOpen) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.removeProperty("overflow");
    }
    return () => {
      document.body.style.removeProperty("overflow");
    };
  }, [mobileMenuOpen]);

  // Handle search
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      setShowSearchDropdown(false);
      setIsSearchFocused(false);
      router.push(`/search?q=${encodeURIComponent(searchQuery)}`);
    }
  };

  const handleProductSelect = (product: Product) => {
    setMobileMenuOpen(false);
    setShowSearchDropdown(false);
    setSearchQuery("");
    setSearchResults([]);
    setIsSearchFocused(false);
    router.push(`/products/${product.slug}`);
  };

  const toggleDropdown = (title: string) => {
    setActiveDropdown(activeDropdown === title ? null : title);
    setActiveMegaMenu(null);
  };

  const toggleMegaMenu = (title: string) => {
    setActiveMegaMenu(activeMegaMenu === title ? null : title);
    setActiveDropdown(null);
  };

  // NEW: Toggle nested dropdowns
  const toggleNestedDropdown = (key: string) => {
    setOpenNestedDropdowns(prev => {
      const newSet = new Set(prev);
      if (newSet.has(key)) {
        newSet.delete(key);
      } else {
        newSet.add(key);
      }
      return newSet;
    });
  };

  const closeMobileMenu = () => {
    setMobileMenuOpen(false);
    setActiveDropdown(null);
    setActiveMegaMenu(null);
    setOpenNestedDropdowns(new Set());
    setSearchQuery("");
    setSearchResults([]);
    setShowSearchDropdown(false);
  };

  // NEW: Recursive function to render nested children with state management
  const renderMobileNestedChildren = (
    children: ChildItem[],
    parentKey: string = "",
    level: number = 0
  ) => {
    if (!hasValidChildren(children)) return null;

    return (
      <div className="divide-y divide-gray-200">
        {children.map((child, i) => {
          if (!child?.title || child.title.trim() === "") return null;

          const hasNestedChildren = hasValidChildren(child.children);
          const childKey = `${parentKey}-${i}`;
          const isNestedOpen = openNestedDropdowns.has(childKey);

          return (
            <div key={i} className="w-full">
              <div className="flex justify-between items-center">
                <Link
                  href={child.url || "#"}
                  onClick={(e) => {
                    if (!hasNestedChildren) {
                      closeMobileMenu();
                    } else {
                      e.preventDefault();
                      toggleNestedDropdown(childKey);
                    }
                  }}
                  className="flex-1 block py-3 px-4 text-sm text-gray-700 hover:bg-gray-100 hover:text-orange-600 transition-colors"
                >
                  {child.title}
                </Link>

                {hasNestedChildren && (
                  <button
                    onClick={() => toggleNestedDropdown(childKey)}
                    className="p-3 text-gray-400 hover:text-orange-500 transition"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className={`h-4 w-4 transition-transform ${isNestedOpen ? 'rotate-180' : ''}`}
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 9l-7 7-7-7"
                      />
                    </svg>
                  </button>
                )}
              </div>

              {hasNestedChildren && isNestedOpen && (
                <div className="pl-4 border-l-2 border-orange-500 bg-gray-50">
                  {renderMobileNestedChildren(child.children!, childKey, level + 1)}
                </div>
              )}
            </div>
          );
        })}
      </div>
    );
  };

  // UPDATED: Render mobile dropdown for regular items with state management
  const renderMobileDropdown = (children: ChildItem[], parentTitle: string) => {
    if (!hasValidChildren(children)) return null;

    return (
      <div className="divide-y divide-gray-200">
        {children.map((child, index) => {
          if (!child?.title || child.title.trim() === "") return null;

          const hasNestedChildren = hasValidChildren(child.children);
          const childKey = `${parentTitle}-${index}`;
          const isNestedOpen = openNestedDropdowns.has(childKey);

          return (
            <div key={index} className="w-full">
              <div className="flex justify-between items-center">
                <Link
                  href={child.url || "#"}
                  onClick={(e) => {
                    if (!hasNestedChildren) {
                      closeMobileMenu();
                    } else {
                      e.preventDefault();
                      toggleNestedDropdown(childKey);
                    }
                  }}
                  className="flex-1 block py-3 px-4 text-sm text-gray-700 hover:bg-gray-100 hover:text-orange-600 transition-colors"
                >
                  {child.title}
                </Link>

                {hasNestedChildren && (
                  <button
                    onClick={() => toggleNestedDropdown(childKey)}
                    className="p-3 text-gray-500 hover:text-orange-600 transition"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      className={`h-4 w-4 transition-transform ${isNestedOpen ? 'rotate-180' : ''}`}
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 9l-7 7-7-7"
                      />
                    </svg>
                  </button>
                )}
              </div>

              {hasNestedChildren && isNestedOpen && (
                <div className="pl-4 border-l-2 border-orange-500 bg-gray-50">
                  {renderMobileNestedChildren(child.children!, childKey, 1)}
                </div>
              )}
            </div>
          );
        })}
      </div>
    );
  };

  // UPDATED: Render mobile content for mega menu items with state management
  const renderMobileMegaMenu = (item: NavItem) => {
    if (!hasValidChildren(item.children)) {
      return (
        <MobileDropdown title={item.title || ""}>
          <Link
            href={item.url || "#"}
            onClick={closeMobileMenu}
            className="block py-3 px-4 text-sm text-orange-600 hover:bg-gray-100 font-medium">
            Explore {item.title} →
          </Link>
        </MobileDropdown>
      );
    }

    return (
      <MobileDropdown title={item.title?.toLowerCase() === "software" ? "Download" : (item.title || "") }>
        <div className="space-y-0">
          <Link
            href={item.url || "#"}
            onClick={closeMobileMenu}
            className="block py-3 px-4 text-sm text-orange-600 hover:bg-gray-100 font-medium border-b border-gray-200 bg-gray-50">
            All {item.title?.toLowerCase() === "software" ? "Download" : item.title} →
          </Link>

          {item.children!.map((child, index) => {
            const hasNestedChildren = hasValidChildren(child.children);
            const childKey = `${item.title}-${index}`;
            const isNestedOpen = openNestedDropdowns.has(childKey);

            return (
              <div key={index}>
                <div className="flex justify-between items-center hover:bg-gray-100 transition-colors">
                  <Link
                    href={child.url || "#"}
                    onClick={(e) => {
                      if (!hasNestedChildren) {
                        closeMobileMenu();
                      } else {
                        e.preventDefault();
                        toggleNestedDropdown(childKey);
                      }
                    }}
                    className="flex-1 py-3 px-4 text-sm text-gray-700 hover:text-orange-600 border-b border-gray-200 transition-colors">
                    {child.title}
                  </Link>

                  {hasNestedChildren && (
                    <button
                      onClick={() => toggleNestedDropdown(childKey)}
                      className="px-4 text-gray-500 hover:text-orange-600"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className={`h-4 w-4 transition-transform ${isNestedOpen ? 'rotate-180' : ''}`}
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2}
                          d="M19 9l-7 7-7-7"
                        />
                      </svg>
                    </button>
                  )}
                </div>

                {hasNestedChildren && isNestedOpen && (
                  <div className="bg-gray-50 border-l-2 border-orange-500 ml-4">
                    {renderMobileNestedChildren(child.children!, childKey, 1)}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </MobileDropdown>
    );
  };

  if (loading) {
    // Show a skeleton header while data loads; global preloader overlay handles
    // the initial page load animation. This avoids showing "Loading..." text.
    return (
      <header className="w-full bg-white text-gray-800 z-50 sticky top-0 border-b border-gray-200">
        <div className="container mx-auto px-6 lg:px-8 py-6 flex items-center justify-between">
          {/* Logo skeleton */}
          <div className="h-8 w-[140px] lg:w-[150px] xl:w-[170px] rounded bg-gray-200 animate-pulse" />

          {/* Nav skeleton (desktop) */}
          <div className="hidden lg:flex items-center space-x-6">
            <div className="h-4 w-20 rounded bg-gray-200 animate-pulse" />
            <div className="h-4 w-24 rounded bg-gray-200 animate-pulse" />
            <div className="h-4 w-16 rounded bg-gray-200 animate-pulse" />
            <div className="h-4 w-20 rounded bg-gray-200 animate-pulse" />
          </div>
        </div>
      </header>
    );
  }

  if (!headerData) {
    return (
      <div className="bg-white text-gray-800 text-center py-4">
        Failed to load header
      </div>
    );
  }

  const { branding, navigation, settings } = headerData;
  // Filter out Blog from navigation (both desktop and mobile)
  const filteredNavigation: NavItem[] = Array.isArray(navigation)
    ? navigation.filter((item) => {
        const title = (item.title || "").toLowerCase();
        const url = (item.url || "").toLowerCase();
        return title !== "blog" && !url.includes("/blog");
      })
    : [];

  const sortOrder: Record<string, number> = {
    "solutions": 1,
    "products": 2,
    "software": 3,
    "about": 4,
    "support": 5,
    "contact": 1000,
  };

  const normalizeTitle = (t?: string | null) => {
    const s = String(t || "").toLowerCase().trim();
    if (s === "about") return "about";
    if (s.includes("about")) return "about";
    return s;
  };

  const hasContact = filteredNavigation.some(
    (item) => normalizeTitle(item.title) === "contact"
  );

  const orderedNavigation: NavItem[] = [
    ...filteredNavigation,
    ...(hasContact ? [] : [{ title: "Contact", url: "/contact", type: "single" } as NavItem]),
  ].sort((a, b) => {
    const aKey = normalizeTitle(a.title);
    const bKey = normalizeTitle(b.title);
    const aOrder = sortOrder[aKey] ?? 100;
    const bOrder = sortOrder[bKey] ?? 100;
    return aOrder - bOrder;
  });

  const navLinkTextClass = isSearchFocused
    ? "text-[13px] xl:text-[14px]"
    : "text-[15px] xl:text-[16px]";

  const navSpacingClass = isSearchFocused
    ? "space-x-2 xl:space-x-4"
    : "space-x-5 xl:space-x-9";

  return (
    <>
    <header className="w-full bg-white/95 backdrop-blur-md text-gray-800 z-50 sticky top-0 shadow-sm border-b border-gray-100/60 transition-all duration-300">
      {/* Sticky Top Bar */}
      <div className="sticky top-0 bg-transparent">
        <div className="container mx-auto px-4 lg:px-6 flex items-center justify-between relative py-4">
          <Link
            href="/"
            className="flex items-center transition-transform duration-300 hover:scale-105 z-50">
            <Image
              src={branding?.logo_url || "/logo.png"}
              alt={branding?.site_title || "Logo"}
              width={220}
              height={60}
              loading="lazy"
              sizes="(min-width: 1280px) 220px,
                    (min-width: 1024px) 190px,
                    (min-width: 768px) 160px,
                    140px"
              className="h-auto w-[140px] md:w-[160px] lg:w-[190px] xl:w-[220px] object-contain"
            />
          </Link>

          {/* Desktop Nav */}
          <nav
            className={`hidden lg:flex items-center flex-nowrap transition-all duration-300 ${navSpacingClass}`}
            ref={dropdownRef}>
            <Link
              href="/"
              className={`relative font-[600] ${navLinkTextClass} hover:text-orange-600 transition-colors whitespace-nowrap py-2.5 group ${
                pathname === "/" ? "text-orange-600" : "text-gray-800"
              }`}>
              Home
              <span className={`absolute bottom-0 left-0 w-full h-[2px] bg-orange-500 transform origin-left transition-transform duration-300 ${
                pathname === "/" ? "scale-x-100" : "scale-x-0 group-hover:scale-x-100"
              }`} />
            </Link>
            {orderedNavigation.map((item, index) => {
              if (!item?.title || item.title.trim() === "") {
                return null;
              }

              if (item.type === "single") {
                return (
                  <Link
                    key={index}
                    href={item.url || "#"}
                    className={`relative font-[600] ${navLinkTextClass} hover:text-orange-600 transition-colors whitespace-nowrap py-2.5 group ${
                      pathname === item.url ? "text-orange-600" : "text-gray-800"
                    }`}>
                    {item.title?.toLowerCase() === "software" ? "Download" : item.title}
                    <span className={`absolute bottom-0 left-0 w-full h-[2px] bg-orange-500 transform origin-left transition-transform duration-300 ${
                      pathname === item.url ? "scale-x-100" : "scale-x-0 group-hover:scale-x-100"
                    }`} />
                  </Link>
                );
              }

              if (item.type === "dropdown") {
                const isProductsDropdown =
                  item.title?.toLowerCase() === "products";
                const isSolutionsDropdown =
                  item.title?.toLowerCase() === "solutions";
                const isSoftwareDropdown =
                  item.title?.toLowerCase() === "software";

                return (
                  <div
                    key={index}
                    className={`${isProductsDropdown || isSolutionsDropdown || isSoftwareDropdown ? "group" : "relative group"}` }
                    onMouseEnter={() => setIsMegaMenuOpen(true)}
                    onMouseLeave={() => setIsMegaMenuOpen(false)}
                  >
                    <Link
                      href={item.url || "#"}
                      className={`relative flex ${navLinkTextClass} items-center font-[600] hover:text-orange-600 whitespace-nowrap py-2.5 group ${
                        pathname === item.url ? "text-orange-600" : "text-gray-800"
                      }`}>
                      {item.title?.toLowerCase() === "software" ? "Download" : item.title}
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="h-4 w-4 ml-1 transition-transform group-hover:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2}
                          d="M19 9l-7 7-7-7"
                        />
                      </svg>
                      <span className={`absolute bottom-0 left-0 w-[calc(100%-20px)] h-[2px] bg-orange-500 transform origin-left transition-transform duration-300 ${
                        pathname === item.url ? "scale-x-100" : "scale-x-0 group-hover:scale-x-100"
                      }`} />
                    </Link>

                    {hasValidChildren(item.children) &&
                      !isProductsDropdown &&
                      !isSolutionsDropdown &&
                      !isSoftwareDropdown && (
                        <div className="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                          {renderChildItems(item.children!)}
                        </div>
                      )}

                    {isProductsDropdown && (
                      <ResponsiveMegaMenu>
                        <ProductsMegaMenu />
                      </ResponsiveMegaMenu>
                    )}

                    {isSolutionsDropdown && (
                      <ResponsiveMegaMenu>
                        <SolutionsMegaMenu />
                      </ResponsiveMegaMenu>
                    )}

                    {isSoftwareDropdown && (
                      <ResponsiveMegaMenu>
                        <SoftwareMegaMenu />
                      </ResponsiveMegaMenu>
                    )}
                  </div>
                );
              }
              return null;
            })}
          </nav>

          {/* Desktop Search Bar */}
          <div
            ref={searchRef}
            className={`hidden lg:block relative transition-all duration-300 ease-in-out ${
              isSearchFocused
                ? "w-56 xl:w-80"
                : "w-36 xl:w-48"
            } ${
              settings?.show_search_in_header ? "" : "hidden"
            }`}>
            <form onSubmit={handleSearch}>
              <input
                type="text"
                placeholder="Search Products..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                onFocus={() => {
                  setIsSearchFocused(true);
                  if (searchQuery.trim()) {
                    setShowSearchDropdown(true);
                  }
                }}
                className="w-full py-2 pl-4 pr-10 rounded-full bg-gray-50 border border-gray-100 text-gray-800 text-xs focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/20 focus:bg-white transition-all shadow-inner"
              />
              <button
                type="submit"
                className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-orange-500">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </button>
            </form>

            {/* Search Dropdown */}
            {showSearchDropdown && (
              <div className="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-80 overflow-y-auto z-50">
                {isSearching ? (
                  <div className="p-4 text-center text-gray-500">
                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-orange-500 mx-auto"></div>
                    <p className="mt-2 text-xs">Searching...</p>
                  </div>
                ) : searchResults.length > 0 ? (
                  <div className="py-2">
                    {searchResults.map((product) => (
                      <button
                        key={product.id}
                        onClick={() => handleProductSelect(product)}
                        className="w-full text-left px-4 py-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                        <div className="flex items-center space-x-3">
                          {product.images?.[0] ? (
                            <Image
                              src={`${baseUri}${product.images[0]}`}
                              alt={product.title}
                              className="w-8 h-8 object-cover rounded"
                              width={20}
                              height={20}
                            />
                          ) : (
                            <div className="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path
                                  strokeLinecap="round"
                                  strokeLinejoin="round"
                                  strokeWidth={2}
                                  d="M4 6h16M4 12h16M4 18h16"
                                />
                              </svg>
                            </div>
                          )}
                          <div className="flex-1 min-w-0">
                            <p className="text-xs font-medium text-gray-900 truncate">
                              {product.title}
                            </p>
                            {product.category && (
                              <p className="text-[10px] text-gray-500 truncate">
                                {product.category.parent?.name &&
                                  `${product.category.parent.name} › `}
                                {product.category.name}
                              </p>
                            )}
                          </div>
                        </div>
                      </button>
                    ))}
                  </div>
                ) : searchQuery.trim() ? (
                  <div className="p-4 text-center text-xs text-gray-500">
                    No products found for &quot;{searchQuery}&quot;
                  </div>
                ) : null}
              </div>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            aria-label="Mobile menu"
            className="lg:hidden focus:outline-none text-gray-900 z-50 p-2"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}>
            {mobileMenuOpen ? (
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-6 w-6 transition-transform"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            ) : (
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-6 w-6 transition-transform"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            )}
          </button>

          
        </div>
      </div>

      {/* Mobile Menu */}
      <div
        ref={mobileMenuRef}
        className={`lg:hidden fixed top-0 left-0 w-full h-screen bg-white z-40 transform transition-transform duration-300 ${
          mobileMenuOpen ? "translate-x-0" : "-translate-x-full"
        }`}
        style={{ paddingTop: "110px" }}>
        <div className="h-full overflow-y-auto pb-32">
          {/* Search Bar - TOP */}
          <div className={`px-4 py-4 border-b border-gray-200 ${settings?.show_search_in_header ? "" : "hidden"}`}>
            <div className="relative w-full">
              <form onSubmit={handleSearch}>
                <input
                  type="text"
                  placeholder="Search Products..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onFocus={() =>
                    searchQuery.trim() && setShowSearchDropdown(true)
                  }
                  className="w-full py-3 pl-4 pr-10 rounded-lg bg-gray-100 border border-gray-300 text-gray-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                />
                <button
                  type="submit"
                  className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-orange-500">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    className="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                  </svg>
                </button>
              </form>

              {showSearchDropdown && (
                <div className="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto z-50">
                  {isSearching ? (
                    <div className="p-4 text-center text-gray-500">
                      <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500 mx-auto"></div>
                      <p className="mt-2">Searching...</p>
                    </div>
                  ) : searchResults.length > 0 ? (
                    <div className="py-2">
                      {searchResults.map((product) => (
                        <button
                          key={product.id}
                          onClick={() => handleProductSelect(product)}
                          className="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                          <div className="flex items-center space-x-3">
                            {product.images?.[0] ? (
                              <Image
                                src={`${baseUri}${product.images[0]}`}
                                alt={product.title}
                                className="w-10 h-10 object-cover rounded"
                                width={25}
                                height={25}
                              />
                            ) : (
                              <div className="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  className="h-5 w-5 text-gray-400"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke="currentColor">
                                  <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M4 6h16M4 12h16M4 18h16"
                                  />
                                </svg>
                              </div>
                            )}
                            <div className="flex-1 min-w-0">
                              <p className="text-sm font-medium text-gray-900 truncate">
                                {product.title}
                              </p>
                              {product.category && (
                                <p className="text-xs text-gray-500 truncate">
                                  {product.category.parent?.name &&
                                    `${product.category.parent.name} › `}
                                  {product.category.name}
                                </p>
                              )}
                            </div>
                          </div>
                        </button>
                      ))}
                    </div>
                  ) : searchQuery.trim() ? (
                    <div className="p-4 text-center text-gray-500">
                      No products found for &quot;{searchQuery}&quot;
                    </div>
                  ) : null}
                </div>
              )}
            </div>
          </div>

          {/* Navigation Links - STATE BASED DROPDOWNS */}
          <nav className="py-2">
            <Link
              href="/"
              onClick={closeMobileMenu}
              className={`block text-gray-800 py-4 px-4 font-medium border-b border-gray-200 transition-colors ${
                pathname === "/" ? "text-orange-600 bg-gray-50" : "hover:text-orange-600 hover:bg-gray-50"
              }`}>
              Home
            </Link>
            {orderedNavigation.map((item, index) => {
              if (!item?.title || item.title.trim() === "") {
                return null;
              }

              if (item.type === "single") {
                return (
                  <Link
                    key={index}
                    href={item.url || "#"}
                    onClick={closeMobileMenu}
                    className={`block text-gray-800 py-4 px-4 font-medium border-b border-gray-200 transition-colors ${
                      pathname === item.url
                        ? "text-orange-600 bg-gray-50"
                        : "hover:text-orange-600 hover:bg-gray-50"
                    }`}>
                    {item.title}
                  </Link>
                );
              }

              if (item.type === "dropdown") {
                const hasChildren = hasValidChildren(item.children);
                const isMegaMenu =
                  item.title?.toLowerCase() === "products" ||
                  item.title?.toLowerCase() === "solutions" ||
                  item.title?.toLowerCase() === "software";

                const isActive = activeDropdown === item.title || activeMegaMenu === item.title;

                return (
                  <div key={index} className="border-b border-gray-200">
                    <div className="flex justify-between items-center py-4 px-4 hover:bg-gray-50 transition-colors">
                      <Link
                        href={item.url || ""}
                        onClick={closeMobileMenu}
                        className="text-gray-800 font-medium flex-1 hover:text-orange-600 transition-colors">
                        {item.title?.toLowerCase() === "software" ? "Download" : item.title}
                      </Link>

                      {(hasChildren || isMegaMenu) && (
                        <button
                          onClick={() => {
                            if (isMegaMenu) {
                              toggleMegaMenu(item.title || "");
                            } else {
                              toggleDropdown(item.title || "");
                            }
                          }}
                          className="text-gray-500 hover:text-orange-600 transition-colors p-1">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className={`h-5 w-5 transition-transform duration-300 ${
                              isActive ? "rotate-180 text-orange-500" : ""
                            }`}
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth={2}
                              d="M19 9l-7 7-7-7"
                            />
                          </svg>
                        </button>
                      )}
                    </div>

                    {/* Regular Dropdown for non-mega menu items */}
                    {isActive && activeDropdown === item.title && hasChildren && !isMegaMenu && (
                      <div className="bg-white border-t border-gray-200">
                        {renderMobileDropdown(item.children!, item.title || "")}
                      </div>
                    )}

                    {/* Mega Menu Content */}
                    {isActive && activeMegaMenu === item.title && isMegaMenu && (
                      <div className="bg-white border-t border-gray-200">
                        {renderMobileMegaMenu(item)}
                      </div>
                    )}
                  </div>
                );
              }
              return null;
            })}
          </nav>

          {/* Action Buttons */}
          <div className="px-4 py-6 border-t border-gray-200 space-y-3">
            <Link
              href="/partner"
              onClick={closeMobileMenu}
              className="block bg-orange-500 border border-orange-500 text-white py-3 px-5 rounded-lg hover:bg-orange-600 transform hover:scale-105 transition-all text-sm font-medium text-center">
              BECOME A PARTNER
            </Link>
            <Link
              href="https://partner.markvisitor.com/"
              onClick={closeMobileMenu}
              className="block border border-orange-500 text-orange-500 text-center py-3 px-5 rounded-lg hover:bg-orange-50 transform hover:scale-105 transition-all text-sm font-medium">
              PARTNER LOG IN
            </Link>
            <Link
              href="/pay"
              onClick={closeMobileMenu}
              className="block bg-yellow-500 border border-yellow-500 text-black text-center py-3 px-5 rounded-lg hover:bg-yellow-400 transform hover:scale-105 transition-all text-sm font-medium">
              PAY ONLINE
            </Link>
          </div>

          
        </div>
      </div>

      {/* Search and Action Bar - REMOVED */}

      {/* Overlay */}
      {mobileMenuOpen && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden transition-opacity duration-300"
          onClick={closeMobileMenu}
        />
      )}
    </header>
    <FormModalPopUp headerData={headerData}/>
    </>
  );
};

export default Header;
