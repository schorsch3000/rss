(function (e) {
    function t() {
        var t = "prototype", n = "scroll", r = Array[t].slice, i, s, o, u = e.setTimeout, a = [],
            f = function (e, t, n) {
                this.element = e, this.threshold = t, this.callback = function () {
                    n(e)
                }
            }, l = function () {
                var t = a.length, r;
                if (t) {
                    i = e.scrollY || e.pageYOffset, s = i + e.innerHeight;
                    while (t--) r = a[t], r.visible() && (u(r.callback, 0), a.splice(t, 1))
                } else e.removeEventListener(n, l)
            };
        return f[t].visible = function () {
            var e = this.element, t = this.threshold, n = e.offsetTop - t, r = n + e.offsetHeight + t;
            return s >= n && i <= r
        }, function (t) {
            if (t) {
                var i = t.on, s = t.threshold !== o ? t.threshold : 100, u = t.callback, c;
                if (!i || !u) throw"elements or callback missing";
                if (i.length !== o) {
                    i = r.call(i), c = i.length;
                    while (c--) a.push(new f(i[c], s, u))
                } else a.push(new f(i, s, u));
                l(), a.length && e.addEventListener(n, l)
            }
        }
    }

    e.define && e.define.amd ? e.define("sloth", t) : e.sloth = t()
})(this)
