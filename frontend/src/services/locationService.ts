// API service for fetching countries, states, and cities

export interface Country {
  name: string;
  code: string; // ISO 3166-1 alpha-2
}

export interface State {
  name: string;
  code: string;
  countryCode: string;
}


// Local countries.json record structure
type LocalCountryRecord = {
  code2?: string;
  code3?: string;
  name?: string;
  capital?: string;
  region?: string;
  subregion?: string;
  states?: Array<{ code?: string; name?: string; subdivision?: unknown }>;
};

// Fetch all countries
let localCache: LocalCountryRecord[] | null = null;

async function loadLocalCountries(): Promise<LocalCountryRecord[]> {
  if (localCache) return localCache;
  const res = await fetch("/countries.json", { cache: "no-store" });
  const text = await res.text();
  const cleaned = text
    .replace(/\uFEFF/g, "")
    .replace(/`/g, "")
    .replace(/\/\/.*$/gm, "")
    .replace(/\/\*[\s\S]*?\*\//g, "")
    .replace(/,\s*([\]\}])/g, "$1");
  try {
    const parsed = JSON.parse(cleaned);
    localCache = Array.isArray(parsed) ? parsed : [];
  } catch (err) {
    console.error("countries.json parse error:", err);
    localCache = [];
  }
  return localCache;
}

export const fetchCountries = async (): Promise<Country[]> => {
  try {
    const raw = await loadLocalCountries();
    return raw
      .filter((c) => !!c.name && !!c.code2)
      .map((c) => ({ name: String(c.name), code: String(c.code2).toUpperCase() }));
  } catch (error) {
    console.error("Error fetching countries from /countries.json:", error);
    return [];
  }
};

// Fetch states by country using local countries.json
export const fetchStatesByCountry = async (countryCode: string): Promise<State[]> => {
  try {
    const raw = await loadLocalCountries();
    const cc = String(countryCode).toUpperCase();
    const match = raw.find((c) => String(c.code2 || "").toUpperCase() === cc || String(c.name || "").toUpperCase() === cc);
    const states = match?.states || [];
    return states
      .filter((s) => !!s.name)
      .map((s) => ({
        name: String(s.name),
        code: String(s.code || s.name?.substring(0, 2) || "").toUpperCase(),
        countryCode: cc,
      }));
  } catch (error) {
    console.error("Error fetching states from local countries.json:", error);
    return [];
  }
};

const locationService = {
  fetchCountries,
  fetchStatesByCountry,
};

export default locationService;
