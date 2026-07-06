import Image from 'next/image';
import Link from 'next/link';

export default function Navbar() {
  return (
    <header className="w-full">
      {/* Main Navigation */}
      <div className="container mx-auto px-4 py-4 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center">
          <Image 
            src="/logo.png" 
            alt="Realtime Logo" 
            width={200} 
            height={100}
            priority
          />
        </Link>

        {/* Navigation Links */}
        <nav className="hidden md:flex items-center space-x-8">
          <Link href="/" className="text-orange-500 font-medium">
            Home
          </Link>
          <div className="relative group">
            <button className="flex items-center text-gray-800 font-medium">
              Solutions
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
          <div className="relative group">
            <button className="flex items-center text-gray-800 font-medium">
              Products
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
          <div className="relative group">
            <button className="flex items-center text-gray-800 font-medium">
              Software
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
          <Link href="/about" className="text-gray-800 font-medium">
            About
          </Link>
          <Link href="/blog" className="text-gray-800 font-medium">
            Blog
          </Link>
          <Link href="/support" className="text-gray-800 font-medium">
            Support
          </Link>
        </nav>

        {/* Mobile Menu Button - Hidden on desktop */}
        <button className="md:hidden">
          <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        {/* Mobile App Links */}
        <div className="hidden md:flex items-center space-x-4">
          <Link href="/smart-app" className="flex items-center bg-gray-900 text-white text-xs px-3 py-2 rounded">
            <Image src="/public/images/logo-white.svg" alt="App Icon" width={20} height={20} />
            <div className="ml-2">
              <div className="text-[10px]">REALTIME MOBILE</div>
              <div className="font-bold">SMART APP</div>
            </div>
          </Link>
          <Link href="/attendance-app" className="flex items-center bg-gray-900 text-white text-xs px-3 py-2 rounded">
            <Image src="/public/images/logo-white.svg" alt="App Icon" width={20} height={20} />
            <div className="ml-2">
              <div className="text-[10px]">REALTIME MOBILE</div>
              <div className="font-bold text-orange-500">ATTENDANCE APP</div>
            </div>
          </Link>
        </div>
      </div>

      {/* Search and Action Bar */}
      <div className="bg-gray-100 py-3">
        <div className="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
          {/* Search Bar */}
          <div className="relative w-full md:w-96 mb-4 md:mb-0">
            <input
              type="text"
              placeholder="Search Products"
              className="w-full py-2 pl-4 pr-10 rounded bg-white border border-gray-300 focus:outline-none"
            />
            <button className="absolute right-3 top-1/2 transform -translate-y-1/2">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>

          {/* Action Buttons */}
          <div className="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
            <Link href="/partner" className="bg-orange-500 text-white text-center py-2 px-6 rounded">
              BECOME A PARTNER
            </Link>
            <Link href="/login" className="border border-orange-500 text-orange-500 text-center py-2 px-6 rounded">
              PARTNER LOG IN
            </Link>
            <Link href="/pay" className="bg-yellow-500 text-black text-center py-2 px-6 rounded">
              PAY ONLINE
            </Link>
          </div>
        </div>
      </div>
    </header>
  );
}