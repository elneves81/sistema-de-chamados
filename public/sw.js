// Service Worker para PWA do Painel TV Repaginado
const CACHE_NAME = 'pwa-painel-tv-v6';
const CORE_ASSETS = [
  '/',
  '/painel-tv-repaginado',
  '/manifest.json',
  '/offline.html',
  // Sons
  '/sounds/notification.mp3',
  '/sounds/notification.ogg',
  '/sounds/notification.wav',
  // CDNs (podem falhar no cache inicial, serão tratadas em runtime)
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css',
  'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap'
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(CORE_ASSETS.map((url) => new Request(url, { mode: 'no-cors' }))).catch(() => {
        // Ignora falhas de CDN no install; será tratado com runtime cache
        return Promise.resolve();
      });
    })
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys
      .filter((k) => k !== CACHE_NAME)
      .map((k) => caches.delete(k))
    )).then(() => self.clients.claim())
  );
});

// Estratégias:
// - Navegação (HTML): network-first com fallback para offline.html
// - API /api/tickets/realtime: network-first com cache fallback
// - Outros GET (CSS/JS/Font/CDN): stale-while-revalidate
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const { request } = event;
  const url = new URL(request.url);

  // Navegação
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((resp) => {
          const copy = resp.clone();
          caches.open(CACHE_NAME).then((c) => c.put(request, copy)).catch(() => {});
          return resp;
        })
        .catch(async () => {
          const cached = await caches.match(request);
          return cached || caches.match('/offline.html');
        })
    );
    return;
  }

  // API tickets realtime
  if (url.pathname.startsWith('/api/tickets/realtime')) {
    event.respondWith(
      fetch(request)
        .then((resp) => {
          const copy = resp.clone();
          caches.open(CACHE_NAME).then((c) => c.put(request, copy)).catch(() => {});
          return resp;
        })
        .catch(() => caches.match(request))
    );
    return;
  }

  // Stale-While-Revalidate para estáticos e CDNs
  event.respondWith(
    caches.match(request).then((cached) => {
      const fetchPromise = fetch(request)
        .then((resp) => {
          const copy = resp.clone();
          caches.open(CACHE_NAME).then((c) => c.put(request, copy)).catch(() => {});
          return resp;
        })
        .catch(() => cached);
      return cached || fetchPromise;
    })
  );
});

// Background Sync (exemplo)
self.addEventListener('sync', (event) => {
  if (event.tag === 'ticket-sync') {
    event.waitUntil(syncTickets());
  }
});

async function syncTickets() {
  try {
    const resp = await fetch('/api/tickets/realtime');
    const data = await resp.clone();
    const cache = await caches.open(CACHE_NAME);
    await cache.put('/api/tickets/realtime', data);
  } catch (_) {
    // Silencioso
  }
}
