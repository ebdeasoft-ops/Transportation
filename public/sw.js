/* Service Worker - إشعارات AHL
 * بيفضل متسجل في المتصفح وبيستقبل الـ Push حتى لو مفيش تاب مفتوح من البرنامج */

self.addEventListener('install', function () { self.skipWaiting(); });
self.addEventListener('activate', function (event) { event.waitUntil(self.clients.claim()); });

// وصل Push من السيرفر
self.addEventListener('push', function (event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch (e) { data = { title: event.data ? event.data.text() : '' }; }

    event.waitUntil(
        self.registration.showNotification(data.title || 'إشعار جديد', {
            body: data.body || '',
            icon: data.icon || '/assets/img/brand/favicon.png',
            badge: '/assets/img/brand/favicon.png',
            tag: data.tag || undefined,          // نفس الـ tag = بيستبدل القديم بدل التكرار
            renotify: !!data.tag,
            dir: 'rtl',
            lang: 'ar',
            data: { url: data.url || '/' }
        })
    );
});

// الضغط على الإشعار: لو البرنامج مفتوح نروح له، ولو لأ نفتح تاب جديد
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var url = (event.notification.data && event.notification.data.url) || '/';
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            for (var i = 0; i < list.length; i++) {
                if ('focus' in list[i] && 'navigate' in list[i]) {
                    return list[i].focus().then(function (c) { return c.navigate(url); });
                }
            }
            return self.clients.openWindow(url);
        })
    );
});
