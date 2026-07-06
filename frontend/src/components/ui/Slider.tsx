"use client";
import React, { useState, useEffect, ReactNode } from "react";

interface SliderProps {
  children: ReactNode[];
  autoPlay?: boolean;
  autoPlayInterval?: number;
  showDots?: boolean;
  showArrows?: boolean;
  className?: string;
  slidesToShow?: number;
  responsive?: {
    breakpoint: number;
    slidesToShow: number;
    showDots: boolean;
  }[];
  dotStyle?: {
    size?: number;
    activeSize?: number;
    color?: string;
    activeColor?: string;
    position?: "inside" | "outside";
    containerClass?: string;
    offsetBottom?: number;
  };
}

const Slider: React.FC<SliderProps> = ({
  children,
  autoPlay = false,
  autoPlayInterval = 5000,
  showDots = true,
  showArrows = true,
  className = "",
  slidesToShow = 1,
  responsive = [],
  dotStyle = {
    size: 12,
    activeSize: 15,
    color: "#fff",
    activeColor: "#EA5921",
    position: "inside",
    containerClass: "bg-black/20 px-4 py-[5px] border rounded-full",
  },
}) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isAutoPlaying, setIsAutoPlaying] = useState(autoPlay);
  const [currentSlidesToShow, setCurrentSlidesToShow] = useState(slidesToShow);
  const [currentShowDots, setCurrentShowDots] = useState(showDots);
  const [startX, setStartX] = useState(0);
  const [currentX, setCurrentX] = useState(0);
  const [isSwiping, setIsSwiping] = useState(false);
  const [isMouseDown, setIsMouseDown] = useState(false);
  const [slideWidth, setSlideWidth] = useState(0);
  const containerRef = React.useRef<HTMLDivElement>(null);
  const totalSlides = React.Children.count(children);

  // Handle responsive behavior
  useEffect(() => {
    const handleResize = () => {
      if (responsive.length === 0) return;

      const sortedBreakpoints = [...responsive].sort(
        (a, b) => a.breakpoint - b.breakpoint
      );
      const width = window.innerWidth;
      const matchedBreakpoint = sortedBreakpoints.find(
        (item) => width <= item.breakpoint
      );

      setCurrentSlidesToShow(
        matchedBreakpoint ? matchedBreakpoint.slidesToShow : slidesToShow
      );
      if (matchedBreakpoint && "showDots" in matchedBreakpoint) {
        setCurrentShowDots(matchedBreakpoint.showDots);
      } else {
        setCurrentShowDots(showDots);
      }
    };

    handleResize();
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, [responsive, slidesToShow, showDots]);

  // Calculate slide width
  useEffect(() => {
    if (!containerRef) return;
    const updateSlideWidth = () => {
      if (!containerRef.current) return;
      const containerWidth = containerRef.current.offsetWidth;
      setSlideWidth(containerWidth / currentSlidesToShow);
    };
    updateSlideWidth();
    window.addEventListener("resize", updateSlideWidth);
    return () => window.removeEventListener("resize", updateSlideWidth);
  }, [containerRef, currentSlidesToShow]);

  // Auto-play
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (isAutoPlaying && totalSlides > 1) {
      interval = setInterval(() => {
        setCurrentSlide((prev) => {
          const groups = Math.ceil(totalSlides / currentSlidesToShow);
          const maxStart = Math.max(0, (groups - 1) * currentSlidesToShow);
          const next = prev + currentSlidesToShow;
          return next > maxStart ? 0 : next;
        });
      }, autoPlayInterval);
    }
    return () => interval && clearInterval(interval);
  }, [isAutoPlaying, totalSlides, autoPlayInterval, currentSlidesToShow]);

  // Navigation
  const goToNextSlide = () => {
    setCurrentSlide((prev) => {
      const groups = Math.ceil(totalSlides / currentSlidesToShow);
      const maxStart = Math.max(0, (groups - 1) * currentSlidesToShow);
      const next = prev + currentSlidesToShow;
      return next > maxStart ? 0 : next;
    });
  };

  const goToPrevSlide = () => {
    setCurrentSlide((prev) => {
      const groups = Math.ceil(totalSlides / currentSlidesToShow);
      const maxStart = Math.max(0, (groups - 1) * currentSlidesToShow);
      const next = prev - currentSlidesToShow;
      return prev === 0 ? maxStart : Math.max(0, next);
    });
  };

  const goToSlide = (index: number) => {
    const groups = Math.ceil(totalSlides / currentSlidesToShow);
    const maxStart = Math.max(0, (groups - 1) * currentSlidesToShow);
    setCurrentSlide(Math.min(index, maxStart));
  };

  // Pause/resume auto-play on hover
  const handleMouseEnter = () => autoPlay && setIsAutoPlaying(false);
  const handleMouseLeave = () => autoPlay && setIsAutoPlaying(true);

  // Mouse swipe
  const handleMouseDown = (e: React.MouseEvent) => {
    setStartX(e.clientX);
    setCurrentX(e.clientX);
    setIsMouseDown(true);
    setIsSwiping(true);
    if (autoPlay) setIsAutoPlaying(false);
  };
  const handleMouseMove = (e: React.MouseEvent) => {
    if (!isMouseDown) return;
    setCurrentX(e.clientX);
    e.preventDefault();
  };
  const handleMouseUp = (): void => {
    if (!isMouseDown) {
      return;
    }

    const diff = startX - currentX;
    const swipeThreshold = slideWidth * 0.2;

    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        goToNextSlide();
      } else {
        goToPrevSlide();
      }
    }

    setIsMouseDown(false);
    setIsSwiping(false);

    if (autoPlay) {
      setIsAutoPlaying(true);
    }
  };
  // Touch swipe
  const handleTouchStart = (e: React.TouchEvent) => {
    setStartX(e.touches[0].clientX);
    setCurrentX(e.touches[0].clientX);
    setIsSwiping(true);
    if (autoPlay) setIsAutoPlaying(false);
  };
  const handleTouchMove = (e: React.TouchEvent) => {
    if (!isSwiping) return;
    setCurrentX(e.touches[0].clientX);
    if (Math.abs(startX - currentX) > 10) e.preventDefault();
  };
  const handleTouchEnd = () => {
    if (!isSwiping) return;
    const diff = startX - currentX;
    const swipeThreshold = slideWidth * 0.2;
    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        goToNextSlide();
      } else {
        goToPrevSlide();
      }
    }
    setIsSwiping(false);
    if (autoPlay) setIsAutoPlaying(true);
  };

  // Transform value
  const getTransformValue = () => {
    const base = -currentSlide * (100 / currentSlidesToShow);
    if (!isSwiping || slideWidth === 0) return `${base}%`;
    const diff = startX - currentX;
    const maxSwipe = slideWidth;
    const clamped = Math.max(-maxSwipe, Math.min(diff, maxSwipe));
    const percentage = (clamped / slideWidth) * (100 / currentSlidesToShow);
    return `${base - percentage}%`;
  };

  return (
    <div
      className={`relative overflow-hidden ${className}`}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      ref={containerRef}
    >
      <div
        className={`flex ${
          !isSwiping
            ? "transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
            : ""
        } will-change-transform`}
        style={{ transform: `translateX(${getTransformValue()})` }}
        onMouseDown={handleMouseDown}
        onMouseMove={isMouseDown ? handleMouseMove : undefined}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseUp}
        onTouchStart={handleTouchStart}
        onTouchMove={handleTouchMove}
        onTouchEnd={handleTouchEnd}
      >
        {React.Children.map(children, (child, index) => (
          <div
            key={index}
            className="flex-shrink-0"
            style={{ width: `${100 / currentSlidesToShow}%` }}
          >
            {child}
          </div>
        ))}
      </div>

      {/* Arrows */}
      {showArrows && totalSlides > 1 && (
        <>
          <button
            onClick={goToPrevSlide}
            className="absolute left-2 top-[40%] sm:top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-md z-10 transition-all duration-300 sm:p-3 sm:h-10 sm:w-10 h-8 w-8"
            aria-label="Previous slide"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <button
            onClick={goToNextSlide}
            className="absolute right-2 top-[40%] sm:top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow-md z-10 transition-all duration-300 sm:p-3 sm:h-10 sm:w-10 h-8 w-8"
            aria-label="Next slide"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </>
      )}

      {/* Dots */}
      {currentShowDots && totalSlides > 1 && (
        <div
          className={`absolute left-0 right-0 flex justify-center ${
            dotStyle.position === "outside" ? "static mt-4" : ""
          }`}
        >
          <div
            className={`${
              dotStyle.containerClass ?? "flex gap-2 items-center"
            } flex gap-1`}
            style={{
              bottom: dotStyle.offsetBottom ?? 15,
              height: dotStyle.activeSize,
            }}
          >
            {Array.from({
              length: Math.ceil(totalSlides / currentSlidesToShow),
            }).map((_, index) => (
              <button
                key={index}
                onClick={() => goToSlide(index * currentSlidesToShow)}
                className="rounded-full transition-transform duration-300"
                style={{
                  width: dotStyle.activeSize,
                  height: dotStyle.activeSize,
                  backgroundColor:
                    Math.floor(currentSlide / currentSlidesToShow) === index
                      ? dotStyle.activeColor
                      : dotStyle.color,
                  transform:
                    Math.floor(currentSlide / currentSlidesToShow) === index
                      ? "scale(1)"
                      : "scale(0.6)",
                }}
                aria-label={`Go to slide group ${index + 1}`}
              />
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default Slider;
