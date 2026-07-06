"use client";

import { useEffect } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export const useStackingScroll = (
  sectionRef: React.RefObject<HTMLElement>,
  headerRef: React.RefObject<HTMLElement>,
  cardRefs: React.MutableRefObject<React.RefObject<HTMLDivElement>[]>
) => {
  useEffect(() => {
    const section = sectionRef.current;
    const headerElement = headerRef.current;
    const cardElements = cardRefs.current.map(ref => ref.current);

    if (!section || !headerElement || cardElements.some(el => !el)) return;

    const ctx = gsap.context(() => {
      // Set the first card as sticky
      gsap.set(cardElements[0], {
        position: "sticky",
        top: "25vh",
        zIndex: 1
      });

      const lastCardIndex = cardElements.length - 1;

      ScrollTrigger.create({
        trigger: cardElements[lastCardIndex],
        start: "top center",
        end: "bottom top",
        onEnter: () => {
          gsap.to(headerElement, {
            opacity: 0,
            y: -50,
            duration: 0.5,
            ease: "power2.out",
            onComplete: () => {
              gsap.set(headerElement, { position: "unset" });
            }
          });
        },
        onLeaveBack: () => {
          gsap.set(headerElement, {
            display: "block",
            position: "sticky",
            top: "20px",
            zIndex: 10,
            opacity: 0
          });
          gsap.to(headerElement, {
            opacity: 1,
            y: 0,
            duration: 0.5,
            ease: "power2.out"
          });
        }
      });

      for (let i = 1; i < cardElements.length; i++) {
        const current = cardElements[i];
        const zIndex = i + 1;
        const offsetPixels = i * 10;
        const isLast = i === lastCardIndex;

        ScrollTrigger.create({
          trigger: current,
          start: "top bottom",
          end: "top center",
          scrub: 0.3,
          onEnter: () => {
            gsap.fromTo(current,
              { y: "50%", opacity: 0 },
              { y: 0, opacity: 1, duration: 0.3, ease: "power3.out" }
            );

            if (isLast) {
              gsap.set(current, { visibility: "visible", opacity: 1 });
            }
          },
          onEnterBack: () => {
            gsap.to(current, {
              y: "80%",
              opacity: 0,
              position: "relative",
              duration: 0.5,
              ease: "power2.in"
            });
          },
          onLeave: () => {
            gsap.to(current, {
              position: "sticky",
              top: `calc(25vh + ${offsetPixels}px)`,
              y: 0,
              zIndex: zIndex,
              duration: 0.5,
              onComplete: () => {
                if (isLast) {
                  gsap.delayedCall(0.2, () => {
                    gsap.to(headerElement, {
                      opacity: 0,
                      y: -50,
                      duration: 0.5,
                      ease: "power2.out",
                      onComplete: () => {
                        gsap.set(headerElement, { position: "unset" });
                      }
                    });
                  });
                }
              }
            });
          }
        });
      }
    }, section);

    return () => {
      ctx.revert();
      ScrollTrigger.getAll().forEach(st => st.kill());
    };
  }, [sectionRef, headerRef, cardRefs]);
};
