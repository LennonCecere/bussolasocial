const CACHE_NAME = 'pwa-cache-v1';
const OFFLINE_URL = '/offline.html';

// Instalando o Service Worker e cacheando os arquivos
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Instalando...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                '/home',
                OFFLINE_URL,
                '/manifest.json',
                '/css/app.css',
                '/js/app.js',
                '/images/logo_sem_nome_192x192.png',
                '/images/logo_sem_nome_256x256.png',
                '/images/logo_sem_nome_384x384.png',
                '/images/logo_sem_nome_512x512.png'
            ]);
        })
    );
    self.skipWaiting();
});

// Ativando o Service Worker e limpando caches antigos
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Ativando...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log('[Service Worker] Removendo cache antigo:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Interceptando as requisições e servindo do cache
self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(OFFLINE_URL))
        );
    } else {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request);
            })
        );
    }
});
