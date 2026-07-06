import { NextRequest, NextResponse } from "next/server";
import { imageLink } from "@/services/heroServices";

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  let file = (searchParams.get("file") || "").trim();
  file = file.replace(/^`|`$/g, "").replace(/^['"]|['"]$/g, "");
  const storagePrefix = imageLink;
  if (file.startsWith(storagePrefix)) {
    file = file.substring(storagePrefix.length);
  }

  if (!file) {
    return new NextResponse("Missing 'file' query param", { status: 400 });
  }

  // Allow only hero-slides/html path to avoid arbitrary fetching
  if (!/^hero-slides\/html\/[A-Za-z0-9_.\-]+\.html$/i.test(file)) {
    return new NextResponse("Invalid file path", { status: 400 });
  }

  const url = `${imageLink}${file}`;

  try {
    const upstream = await fetch(url, {
      method: "GET",
      headers: {
        // Some storage/CDN setups block non-browser user agents; mimic a modern browser
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language": "en-US,en;q=0.9",
        "Referer": "https://app.realtimebiometrics.net/",
      },
    });
    if (!upstream.ok) {
      return new NextResponse(`Upstream error (${upstream.status})`, { status: 502 });
    }
    const html = await upstream.text();

    // Inject Montserrat font and helper script to auto-size iframe height
    const fontInject = `
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <style>
        html, body, * {
          font-family: 'Montserrat', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif !important;
        }
        html, body {
          margin: 0 !important;
          padding: 0 !important;
          box-sizing: border-box;
        }
        body {
          display: flow-root; /* prevent margin collapse */
          /* Avoid coupling content height to viewport to prevent growth loops */
          padding-bottom: 16px; /* modest bottom space */
        }
        /* Make media responsive so large images don’t overflow or crop oddly */
        img, video, svg, canvas {
          max-width: 100%;
          height: auto;
        }
        /* Mobile overrides: ensure feature chips are visible */
        @media (max-width: 767px) {
          body { padding-bottom: 12px; }
          /* Unhide commonly used hidden rows inside feature containers */
          [class*="feature"] .hidden,
          [class*="chip"] .hidden,
          [class*="badge"] .hidden,
          [class*="tag"] .hidden {
            display: inline-flex !important;
          }

          /* Force rows that look like chip groups to display on mobile */
          [class*="feature"] [class*="chips"],
          [class*="feature"] [class*="chip"],
          [class*="features"],
          [class*="chip-row"] {
            display: flex !important;
            flex-wrap: wrap;
            gap: 8px;
          }

          /* Avoid clipping */
          [class*="feature"] {
            overflow: visible !important;
          }
        }
      </style>
      <script>
        (function() {
          var last = -1;
          function computeHeight(){
            var b = document.body;
            var e = document.documentElement;
            // Only use content-driven heights to avoid viewport feedback loops
            return Math.max(
              (e && e.scrollHeight) || 0,
              (b && b.scrollHeight) || 0,
              (b && b.offsetHeight) || 0
            );
          }
          function send(){
            try {
              var h = computeHeight();
              if (h <= 0) return;
              // Only increase height when content grows meaningfully
              if (last >= 0 && (h <= last || Math.abs(h - last) < 8)) return;
              last = h;
              var file = (function(){
                try { return new URL(window.location.href).searchParams.get('file') || ''; } catch(e) { return ''; }
              })();
              parent.postMessage({ type: 'hero-content-height', height: h, file: file }, window.location.origin);
            } catch (e) {}
          }
          window.addEventListener('load', function(){ setTimeout(send, 50); setTimeout(send, 200); setTimeout(send, 600); setTimeout(send, 2500); setTimeout(send, 4000); });
          // Avoid reacting to viewport height changes (which occur when parent resizes iframe)
          // window.addEventListener('resize', send);
          try {
            var obs = new MutationObserver(function() { send(); });
            obs.observe(document.body, { childList: true, subtree: true });
          } catch (e) {}
          try {
            var ro = new ResizeObserver(function(){ send(); });
            ro.observe(document.body);
          } catch (e) {}
          try {
            (document.images || []).forEach ? (document.images || []).forEach(function(img){ img.addEventListener('load', send); }) : Array.prototype.forEach.call(document.images || [], function(img){ img.addEventListener('load', send); });
          } catch (e) {}
          setTimeout(send, 300);
          setTimeout(send, 1200);
        })();
      </script>
    `;

    // Clean up stray backticks inside attribute values that break resource URLs
    const fixAttrBackticks = (s: string) =>
      s
        .replace(/="\s*`([^`]+)`\s*"/g, '="$1"')
        .replace(/='\s*`([^`]+)`\s*'/g, "='$1'");

    let transformed = fixAttrBackticks(html);
    if (/<head[^>]*>/i.test(html)) {
      transformed = html.replace(/<head[^>]*>/i, (m) => `${m}\n${fontInject}`);
    } else {
      // If there's no <head>, prepend the inject at the start to be safe
      transformed = `${fontInject}\n${html}`;
    }

    return new NextResponse(transformed, {
      status: 200,
      headers: {
        "Content-Type": "text/html; charset=utf-8",
        "Cache-Control": "public, max-age=60", // small cache
      },
    });
  } catch {
    return new NextResponse("Failed to fetch upstream content", { status: 500 });
  }
}
