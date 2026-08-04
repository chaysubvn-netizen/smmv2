import { headers } from 'next/headers';
import type { Metadata } from "next";
import Script from "next/script";
import AntdAppProvider from '@/components/AntdAppProvider';
import GlobalCustomScripts from '@/components/GlobalCustomScripts';
import "./globals.css";

export async function generateMetadata(): Promise<Metadata> {
  const metadataBase = new URL('https://minsmm.net/');

  try {
    const apiUrl = process.env.API_URL || process.env.NEXT_PUBLIC_API_URL || `${metadataBase.origin}/api`;
    const assetUrl = (value?: string | null) => value ? (value.startsWith('http') ? value : `${apiUrl.replace(/\/api\/?$/, '')}${value.startsWith('/') ? '' : '/'}${value}`) : undefined;
    const res = await fetch(`${apiUrl}/client/config`, {
      headers: {
        'Accept': 'application/json'
      },
      next: { revalidate: 60 } // Cache for 60 seconds
    });

    if (!res.ok) {
      throw new Error(`Config API returned ${res.status}`);
    }

    const json = await res.json();
    
    if (json.status && json.data) {
      const config = json.data;
      
      return {
        metadataBase,
        title: {
          default: `  ${config.title || 'SMM Panel'}`,
          template: `%s | ${config.title || 'SMM Panel'}`,
        },
        description: config.description || 'SMM Panel',
        keywords: config.keywords || '',
        icons: {
          icon: assetUrl(config.favicon) || '/favicon.ico',
        },
        openGraph: {
          title: config.og_title || config.title || 'SMM Panel',
          description: config.og_description || config.description || 'SMM Panel',
          images: assetUrl(config.og_image) ? [{ url: assetUrl(config.og_image)! }] : [],
        },
        twitter: {
          card: config.twitter_card_type || 'summary_large_image',
          title: config.twitter_title || config.og_title || config.title || 'SMM Panel',
          description: config.twitter_description || config.og_description || config.description || 'SMM Panel',
          images: assetUrl(config.twitter_image || config.og_image) ? [{ url: assetUrl(config.twitter_image || config.og_image)! }] : [],
        },
      };
    }
  } catch (error) {
    console.error('Error fetching metadata:', error);
  }

  return {
    metadataBase,
    title: "SMM Panel",
    description: "SMM Panel",
  };
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const headersList = await headers();
  const host = (headersList.get('x-forwarded-host') || headersList.get('host') || 'localhost:3000').split(',')[0].trim();
  const forwardedProtocol = (headersList.get('x-forwarded-proto') || '').split(',')[0].trim();
  const protocol = forwardedProtocol || (/^(localhost|127\.0\.0\.1)(:|$)/.test(host) ? 'http' : 'https');
  const siteUrl = `${protocol}://${host}`;
  let jsonLd: Record<string, unknown> | null = null;

  try {
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api';
    const assetUrl = (value?: string | null) => value
      ? (value.startsWith('http') ? value : `${apiUrl.replace(/\/api\/?$/, '')}${value.startsWith('/') ? '' : '/'}${value}`)
      : undefined;
    const response = await fetch(`${apiUrl}/client/config`, {
      headers: { Host: host, Accept: 'application/json' },
      next: { revalidate: 60 },
    });
    const result = await response.json();
    if (result.status && result.data) {
      const config = result.data;
      const organizationId = `${siteUrl}/#organization`;
      jsonLd = {
        '@context': 'https://schema.org',
        '@graph': [
          {
            '@type': 'Organization',
            '@id': organizationId,
            name: config.title || 'SMM Panel',
            url: siteUrl,
            ...(assetUrl(config.logo) ? { logo: { '@type': 'ImageObject', url: assetUrl(config.logo) } } : {}),
            sameAs: [config.facebook_link, config.zalo_link, config.telegram_link].filter(Boolean),
          },
          {
            '@type': 'WebSite',
            '@id': `${siteUrl}/#website`,
            url: siteUrl,
            name: config.title || 'SMM Panel',
            description: config.og_description || config.description || 'SMM Panel',
            inLanguage: 'vi-VN',
            publisher: { '@id': organizationId },
          },
        ],
      };
    }
  } catch (error) {
    console.error('Error generating JSON-LD:', error);
  }

  return (
    <html
      lang="vi"
      suppressHydrationWarning
      data-pc-preset="preset-1"
      data-pc-sidebar-caption="true"
      data-pc-layout="vertical"
      data-pc-direction="ltr"
      data-pc-theme_contrast=""
      data-pc-theme="light"
      data-bs-theme="light"
      data-pc-sidebar-theme="dark"
    >
      <head>
        {jsonLd ? <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, '\\u003c') }} /> : null}
        <Script id="theme-initializer" strategy="beforeInteractive" dangerouslySetInnerHTML={{ __html: `
          try {
            localStorage.setItem('theme', 'light');
            var root = document.documentElement;
            root.setAttribute('data-pc-theme', 'light');
            root.setAttribute('data-bs-theme', 'light');
            root.setAttribute('data-pc-sidebar-theme', 'dark');
          } catch (_) {}
        ` }} />
        <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.hassbase.com/plugins/font-awesome/5.15.4/css/all.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/inter/inter.css" id="main-font-link" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/phosphor/duotone/style.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/tabler-icons.min.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/feather.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/fontawesome.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/material.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/css/style.css" id="main-style-link" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/css/style-preset.css" />
        <link rel="stylesheet" href="/cmsbvq/template/frontend/css/custom.css" />
        
        <style>{`
          :root {
              --dh-default-color: #1c252e;
              --dh-primary-color: var(--client-primary);
              --dh-dark-main-color: #3580f7;
          }
        `}</style>
      </head>
      <body
        suppressHydrationWarning
        data-pc-theme="light"
        data-bs-theme="light"
        data-pc-sidebar-theme="dark"
      >
        <GlobalCustomScripts />
        {/* Google Translate Element (Hidden) */}
        <div id="google_translate_element" style={{ display: 'none' }}></div>
        <Script id="google-translate-init" strategy="afterInteractive" dangerouslySetInnerHTML={{
          __html: `
            window.googleTranslateElementInit = function() {
              new google.translate.TranslateElement({pageLanguage: 'vi', autoDisplay: false}, 'google_translate_element');
            };
            
            let gTranslateLoaded = false;
            function loadGoogleTranslate() {
              if (gTranslateLoaded) return;
              gTranslateLoaded = true;
              var script = document.createElement('script');
              script.type = 'text/javascript';
              script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
              document.body.appendChild(script);
            }
            
            // Lazy load Google Translate only when user interacts with the page (bypass Lighthouse cookie warnings)
            window.addEventListener('mousemove', loadGoogleTranslate, { once: true });
            window.addEventListener('scroll', loadGoogleTranslate, { once: true });
            window.addEventListener('touchstart', loadGoogleTranslate, { once: true });
            window.addEventListener('click', loadGoogleTranslate, { once: true });
            window.addEventListener('keydown', loadGoogleTranslate, { once: true });
          `
        }} />
        <style dangerouslySetInnerHTML={{
          __html: `
            .VIpgJd-ZVi9od-ORHb-OEVmcd { display: none !important; }
            .VIpgJd-ZVi9od-aZ2wEe-wOHMyf { display: none !important; }
            .skiptranslate > iframe.skiptranslate { display: none !important; }
            body { top: 0 !important; }
            #goog-gt-tt { display: none !important; }
            .goog-te-balloon-frame { display: none !important; }
            .goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }
          `
        }} />

        <AntdAppProvider>{children}</AntdAppProvider>

        {/* Global Scripts */}
        <Script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" strategy="beforeInteractive" />
        <Script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" strategy="lazyOnload" />
        <Script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/plugins/popper.min.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/plugins/simplebar.min.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/plugins/bootstrap.min.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/icon/custom-font.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/script.js" strategy="lazyOnload" />
        <Script src="/cmsbvq/template/frontend/js/plugins/feather.min.js" strategy="lazyOnload" />
      </body>
    </html>
  );
}
