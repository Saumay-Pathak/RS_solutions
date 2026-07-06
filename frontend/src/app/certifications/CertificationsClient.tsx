"use client";

import { useEffect, useState, useRef } from "react";
import Image from "next/image";
import Link from "next/link";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import { baseUri } from "@/services/constant";

type PdfjsViewport = { width: number; height: number } & Record<string, unknown>;
type PdfjsPage = {
  getViewport: (opts: { scale: number }) => PdfjsViewport;
  render: (args: { canvasContext: CanvasRenderingContext2D; viewport: PdfjsViewport }) => { promise: Promise<void> };
};
type PdfjsDocument = { getPage: (n: number) => Promise<PdfjsPage> };
type PdfjsGetDocumentResult = { promise: Promise<PdfjsDocument> };
type PdfjsLib = {
  GlobalWorkerOptions: { workerSrc: string };
  getDocument: (params: { url: string }) => PdfjsGetDocumentResult;
};
type PdfjsWindow = { pdfjsLib?: PdfjsLib };

type Certification = {
  id: string;
  name: string;
  authority_logo?: string | null;
  certificate_file?: string | null;
  authority_logo_url?: string | null;
  certificate_url?: string | null;
  sort_order?: number;
  status?: boolean | number | string;
};

function cleanUrl(u: string | null | undefined): string {
  const s = String(u || "").trim().replace(/^`|`$/g, "").replace(/^['"]|['"]$/g, "");
  return s;
}

export default function CertificationsClient() {
  const [items, setItems] = useState<Certification[]>([]);
  const [loading, setLoading] = useState(true);
  const [previews, setPreviews] = useState<Record<string, string>>({});
  const [pdfReady, setPdfReady] = useState(false);
  const [visibleIds, setVisibleIds] = useState<Set<string>>(new Set());
  const cardRefs = useRef<Record<string, HTMLDivElement | null>>({});

  useEffect(() => {
    const load = async () => {
      try {
        setLoading(true);
        const res = await fetch("https://app.realtimebiometrics.net/api/content/certifications", {
          cache: "no-store",
          headers: { "Accept": "application/json" },
        });
        const json = await res.json();
        const list: Certification[] = Array.isArray(json?.data) ? json.data : [];
        const active = list.filter((c) => {
          const s = c.status;
          if (typeof s === "boolean") return s;
          if (typeof s === "number") return s === 1;
          if (typeof s === "string") return ["1", "true", "active"].includes(s.toLowerCase());
          return true;
        });
        const ordered = active.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        setItems(ordered);
      } catch (err) {
        console.error("Certifications fetch error", err);
        setItems([]);
      } finally {
        setLoading(false);
      }
    };
    load();
  }, []);

  useEffect(() => {
    if (typeof IntersectionObserver === "undefined") return;
    const obs = new IntersectionObserver(
      (entries) => {
        setVisibleIds((prev) => {
          const next = new Set(prev);
          for (const e of entries) {
            const id = e.target.getAttribute("data-cert-id") || "";
            if (!id) continue;
            if (e.isIntersecting) next.add(id);
          }
          return next;
        });
      },
      { root: null, rootMargin: "200px 0px", threshold: 0.01 }
    );
    items.forEach((c) => {
      const el = cardRefs.current[c.id];
      if (el) obs.observe(el);
    });
    return () => {
      obs.disconnect();
    };
  }, [items]);

  useEffect(() => {
    const w = typeof window !== "undefined" ? (window as unknown as PdfjsWindow) : undefined;
    if (!w) return;
    if (w.pdfjsLib) {
      try {
        w.pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
      } catch {}
      setPdfReady(true);
      return;
    }
    const s = document.createElement("script");
    s.src = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js";
    s.async = true;
    s.onload = () => {
      try {
        const ww = window as unknown as PdfjsWindow;
        if (ww.pdfjsLib) {
          ww.pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
          setPdfReady(true);
        }
      } catch {}
    };
    document.head.appendChild(s);
  }, []);

  useEffect(() => {
    if (!pdfReady || !items || items.length === 0) return;
    const w = window as unknown as PdfjsWindow;
    const lib = w.pdfjsLib;
    if (!lib) return;
    const pdfs = items
      .map((c) => getCertificateUrl(c))
      .filter((u) => u && isPdf(u) && !previews[u]);
    if (pdfs.length === 0) return;
    const run = async (url: string) => {
      try {
        const doc = await lib.getDocument({ url }).promise;
        const page = await doc.getPage(1);
        const viewport = page.getViewport({ scale: 1 });
        const targetW = 800;
        const scale = Math.max(0.1, Math.min(2, targetW / viewport.width));
        const vp = page.getViewport({ scale });
        const canvas = document.createElement("canvas");
        canvas.width = Math.floor(vp.width);
        canvas.height = Math.floor(vp.height);
        const ctx = canvas.getContext("2d");
        if (!ctx) return;
        await page.render({ canvasContext: ctx, viewport: vp }).promise;
        const data = canvas.toDataURL("image/png");
        setPreviews((p) => ({ ...p, [url]: data }));
      } catch {}
    };
    pdfs.forEach((u) => run(u));
  }, [pdfReady, items, previews]);

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Certifications", href: "/certifications" },
  ];

  const getCertificateUrl = (c: Certification) => {
    const direct = cleanUrl(c.certificate_url);
    if (direct.startsWith("http")) return direct;
    const file = cleanUrl(c.certificate_file);
    return file ? `${baseUri}${file}` : "";
  };

  const isPdf = (u: string) => /\.pdf(\?|$)/i.test(u);
  const getDocParam = (c: Certification) => {
    const file = cleanUrl(c.certificate_file);
    if (file) return file.replace(/^\/+/, "");
    const link = cleanUrl(c.certificate_url);
    if (link.startsWith(baseUri)) return link.substring(baseUri.length).replace(/^\/+/, "");
    return "";
  };
  const getProxyUrlForDoc = (doc: string) => (doc ? `/api/catalogue?doc=${encodeURIComponent(doc)}` : "");

  useEffect(() => {
    if (!pdfReady || !items || items.length === 0) return;
    const w = window as unknown as PdfjsWindow;
    const lib = w.pdfjsLib;
    if (!lib) return;
    const candidates = items
      .map((c) => {
        const orig = getCertificateUrl(c);
        const doc = getDocParam(c);
        const prox = doc ? getProxyUrlForDoc(doc) : orig;
        return { orig, prox };
      })
      .filter(({ orig }) => orig && isPdf(orig) && !previews[orig])
      .filter(({ orig }) => {
        const id = (items.find((cc) => getCertificateUrl(cc) === orig) || { id: "" }).id;
        return id ? visibleIds.has(id) : true;
      });
    if (candidates.length === 0) return;
    const MAX_CONCURRENT = 2;
    const DELAY_MS = 250;
    const queue = [...candidates];
    let running = 0;
    let cancelled = false;

    const wait = (ms: number) => new Promise((r) => setTimeout(r, ms));
    const runOne = async (orig: string, prox: string) => {
      try {
        const source = prox || orig;
        const d = await lib.getDocument({ url: source }).promise;
        const page = await d.getPage(1);
        const viewport = page.getViewport({ scale: 1 });
        const targetW = 800;
        const scale = Math.max(0.1, Math.min(2, targetW / viewport.width));
        const vp = page.getViewport({ scale });
        const canvas = document.createElement("canvas");
        canvas.width = Math.floor(vp.width);
        canvas.height = Math.floor(vp.height);
        const ctx = canvas.getContext("2d");
        if (!ctx) return;
        await page.render({ canvasContext: ctx, viewport: vp }).promise;
        const data = canvas.toDataURL("image/png");
        setPreviews((p) => ({ ...p, [orig]: data }));
      } catch (err) {
        console.error("PDF preview failed for", orig, err);
      } finally {
        running--;
      }
    };

    (async () => {
      while (!cancelled && queue.length > 0) {
        while (!cancelled && running < MAX_CONCURRENT && queue.length > 0) {
          const { orig, prox } = queue.shift()!;
          running++;
          runOne(orig, prox);
          await wait(DELAY_MS);
        }
        if (running > 0) await wait(DELAY_MS);
      }
    })();

    return () => {
      cancelled = true;
    };
  }, [pdfReady, items, previews, visibleIds]);

  return (
    <>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <section className="py-10 bg-white mb-10">
        <div className="container mx-auto px-4">
        <div className="text-center mb-8">
          <h1 className="section-title text-3xl font-bold">Certifications</h1>
          <p className="section-subtitle text-sm">Validated by recognized authorities</p>
        </div>

          {loading && (
          <div className="flex justify-center py-6">
            <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500"></div>
          </div>
        )}

          {!loading && items.length === 0 ? (
          <div className="text-center text-gray-600 py-10">No certifications available.</div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {items.map((c) => {
              const fileUrl = getCertificateUrl(c);
              return (
                <div
                  key={c.id}
                  className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col gap-4"
                  data-cert-id={c.id}
                  ref={(el) => {
                    cardRefs.current[c.id] = el;
                  }}
                >
                  <div className="text-center min-h-[1.75rem]">
                    <h3 className="text-lg font-semibold text-[#1E1410] truncate" title={c.name}>{c.name}</h3>
                  </div>
                  {fileUrl && (
                    <div className="mt-2">
                      {isPdf(fileUrl) ? (
                        previews[fileUrl] ? (
                          <Image src={previews[fileUrl]} alt={`${c.name} certificate`} width={800} height={600} sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw" className="w-full h-auto object-contain rounded-lg border border-gray-200" />
                        ) : (
                          <Link href={fileUrl} target="_blank" rel="noopener noreferrer" className="block group">
                            <div className="relative aspect-[4/3] rounded-lg overflow-hidden border border-gray-200 bg-gray-50 grid place-items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-14 h-14 text-gray-400 group-hover:text-orange-600 transition-colors">
                                <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0013.586 2H6zm6 1.586a1 1 0 011.707-.707l4.414 4.414a1 1 0 01.293.707V20a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1h6z"/>
                                <text x="50%" y="60%" textAnchor="middle" fontSize="6" fill="currentColor">PDF</text>
                              </svg>
                              <span className="absolute bottom-2 right-2 text-xs bg-black/50 text-white px-2 py-1 rounded">PDF</span>
                            </div>
                          </Link>
                        )
                      ) : (
                        <Image src={fileUrl} alt={`${c.name} certificate`} width={800} height={600} sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw" className="w-full h-auto object-contain rounded-lg border border-gray-200" />
                      )}
                      
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
        </div>
      </section>
    </>
  );
}
