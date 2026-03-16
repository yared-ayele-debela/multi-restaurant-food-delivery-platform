import { createContext, useContext, useEffect, useMemo, useState } from "react";
import { fetchSiteSettings } from "../api/catalogApi";

const SiteSettingsContext = createContext(null);

const defaultSettings = {
  site_name: "Food Delivery",
  site_description: "",
  logo_url: "",
  favicon_url: "",
  contact_email: "",
  contact_phone: "",
  contact_address: "",
  facebook_url: "",
  twitter_url: "",
  instagram_url: "",
  linkedin_url: "",
  youtube_url: "",
  whatsapp_number: "",
  footer_text: "",
  currency_symbol: "$",
  currency_code: "USD",
  meta_keywords: [],
};

function applyDocumentBranding(settings) {
  if (typeof document === "undefined") {
    return;
  }

  document.title = settings.site_name || "Food Delivery";

  if (Array.isArray(settings.meta_keywords) && settings.meta_keywords.length > 0) {
    const value = settings.meta_keywords.join(", ");
    let keywordsTag = document.querySelector("meta[name='keywords']");
    if (!keywordsTag) {
      keywordsTag = document.createElement("meta");
      keywordsTag.setAttribute("name", "keywords");
      document.head.appendChild(keywordsTag);
    }
    keywordsTag.setAttribute("content", value);
  }

  if (!settings.favicon_url) {
    return;
  }

  let faviconLink = document.querySelector("link[rel='icon']");
  if (!faviconLink) {
    faviconLink = document.createElement("link");
    faviconLink.setAttribute("rel", "icon");
    document.head.appendChild(faviconLink);
  }
  faviconLink.setAttribute("href", settings.favicon_url);
}

export function SiteSettingsProvider({ children }) {
  const [settings, setSettings] = useState(defaultSettings);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    let mounted = true;

    async function loadSettings() {
      try {
        setLoading(true);
        setError("");
        const data = await fetchSiteSettings();
        if (!mounted) {
          return;
        }
        const next = { ...defaultSettings, ...data };
        setSettings(next);
        applyDocumentBranding(next);
      } catch {
        if (!mounted) {
          return;
        }
        setError("Failed to load site settings.");
        applyDocumentBranding(defaultSettings);
      } finally {
        if (mounted) {
          setLoading(false);
        }
      }
    }

    loadSettings();
    return () => {
      mounted = false;
    };
  }, []);

  const value = useMemo(
    () => ({
      settings,
      loading,
      error,
    }),
    [settings, loading, error],
  );

  return <SiteSettingsContext.Provider value={value}>{children}</SiteSettingsContext.Provider>;
}

export function useSiteSettings() {
  const context = useContext(SiteSettingsContext);
  if (!context) {
    throw new Error("useSiteSettings must be used inside SiteSettingsProvider");
  }
  return context;
}
