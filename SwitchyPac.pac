function regExpMatch(url, pattern) {    try { return new RegExp(pattern).test(url); } catch(ex) { return false; }    }
    function FindProxyForURL(url, host) {
	if (shExpMatch(url, "*://*.cyanogenmod.com/*") || shExpMatch(url, "*://cyanogenmod.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://get.cm/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.google.com/*") || shExpMatch(url, "*://google.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.google.com.*/*") || shExpMatch(url, "*://google.com.*/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.googlecode.com/*") || shExpMatch(url, "*://googlecode.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.googleadservices.com/*") || shExpMatch(url, "*://googleadservices.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.google-analytics.com/*") || shExpMatch(url, "*://google-analytics.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.googleapis.com/*") || shExpMatch(url, "*://googleapis.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.googleLabs.com/*") || shExpMatch(url, "*://googleLabs.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.googleusercontent.com/*") || shExpMatch(url, "*://googleusercontent.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.gstatic.com/*") || shExpMatch(url, "*://gstatic.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*.h5gal.com/*") || shExpMatch(url, "*://h5gal.com/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://msdn.itellyou.cn/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://goo.gl/*")) return 'PROXY 127.0.0.1:477';
	if (shExpMatch(url, "*://*wikipedia.org/*")) return 'PROXY 127.0.0.1:477';
	return 'DIRECT';
}