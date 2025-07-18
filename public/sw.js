// Service Worker para funcionamento offline
const CACHE_NAME = 'tickets-board-v1';
const urlsToCache = [
  '/tickets-board',
  '/tickets-board/tv',
  '/css/app.css',
  '/js/app.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // Cache hit - return response
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});

// Background sync para quando voltar online
self.addEventListener('sync', function(event) {
  if (event.tag === 'ticket-sync') {
    event.waitUntil(syncTickets());
  }
});

function syncTickets() {
  // Sincronizar dados quando voltar online
  return fetch('/api/tickets/sync')
    .then(response => response.json())
    .then(data => {
      // Atualizar cache local
      return caches.open(CACHE_NAME)
        .then(cache => {
          return cache.put('/api/tickets/all', new Response(JSON.stringify(data)));
        });
    });
}
