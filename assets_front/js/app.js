/* =========================================================
   Abu Eid Electrical — storefront logic (vanilla JS)
   routing · catalog · cart · checkout · motion

   All content comes from the server (window.STOREFRONT), already
   resolved to the current language. Interface strings live in
   resources/lang/{locale}/front.php.
   ========================================================= */
(() => {
"use strict";

const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];

const D = window.STOREFRONT;
const S = D.store, I18N = D.i18n;
const P = D.products, C = D.categories, B = D.brands, BR = D.branches, ASSURANCES = D.assurances;
const ACC = D.account;
let me = ACC.state;          // signed-in customer state, or null for guests
let returnTo = null;         // view to go back to after signing in (e.g. checkout)

const byId = id => P.find(p => p.id === id);
const catById = id => C.find(c => c.id === id);
const brandById = id => B.find(b => b.id === id);
const imgOf = p => p.images[0] || S.placeholder || "";

/* ---------- safe storage ---------- */
const store = {
  get(k, d) { try { const v = localStorage.getItem("abueid:" + k); return v ? JSON.parse(v) : d; } catch { return d; } },
  set(k, v) { try { localStorage.setItem("abueid:" + k, JSON.stringify(v)); } catch {} }
};

/* ---------- i18n ----------
   ":name" placeholders are replaced from params; "one|many" strings are picked by params.n */
function t(key, params = {}) {
  let s = I18N[key];
  if (typeof s !== "string") return key;
  if (s.includes("|") && "n" in params) { const [one, many] = s.split("|"); s = +params.n === 1 ? one : many; }
  return s.replace(/:(\w+)/g, (m, k) => (k in params ? params[k] : m));
}
const fmt = n => (+(+n).toFixed(2)).toLocaleString("en-US", { maximumFractionDigits: 2 });
const money = n => `${fmt(n)} <small>${esc(S.currency)}</small>`;
const moneyTxt = n => `${fmt(n)} ${S.currency}`;
const payLabel = method => (I18N.payment_methods || {})[method] || method;

/* ---------- server API (JSON, same-origin session) ----------
   Laravel rotates the CSRF token on sign-in/out and always keeps the XSRF-TOKEN cookie
   current, so it is read on every request (falling back to the page's meta token). */
function csrfHeaders() {
  const cookie = document.cookie.split("; ").find(c => c.startsWith("XSRF-TOKEN="));
  return cookie
    ? { "X-XSRF-TOKEN": decodeURIComponent(cookie.slice(11)) }
    : { "X-CSRF-TOKEN": $('meta[name="csrf-token"]')?.content || "" };
}
async function api(method, url, body) {
  const res = await fetch(url, {
    method,
    headers: { "Content-Type": "application/json", "Accept": "application/json", "X-Requested-With": "XMLHttpRequest", ...csrfHeaders() },
    body: body === undefined ? undefined : JSON.stringify(body)
  });
  const data = await res.json().catch(() => null);
  if (!res.ok) {
    const message = res.status === 429 ? t("too_many")
      : res.status === 419 ? t("session_expired")
      : (res.status === 422 || res.status === 409) && data?.message ? data.message
      : t("order_failed");
    throw Object.assign(new Error(message), { status: res.status, errors: data?.errors || {} });
  }
  return data;
}

/* ---------- state ---------- */
let cart = store.get("cart", []).filter(i => byId(i.id));     // [{id, qty}]
let favs = new Set([...store.get("favs", []), ...(me?.favourites || [])].filter(byId));
const saveCart = () => { store.set("cart", cart); paintCounts(); };
const saveFavs = () => {
  store.set("favs", [...favs]); paintCounts();
  if (me) api("PUT", ACC.urls.favourites, { ids: [...favs] }).then(s => { me = s; }).catch(() => {});
};
const cartQty = () => cart.reduce((a, i) => a + i.qty, 0);
const cartSub = () => cart.reduce((a, i) => a + byId(i.id).price * i.qty, 0);
const freeDelivery = sub => S.freeDeliveryFrom > 0 && sub >= S.freeDeliveryFrom;

/* ---------- shared fragments ---------- */
const heart = `<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 17s-6.5-3.9-6.5-8.6A3.6 3.6 0 0 1 10 6.3a3.6 3.6 0 0 1 6.5 2.1C16.5 13.1 10 17 10 17z"/></svg>`;
function stockBadge(p) {
  if (p.stock <= 0) return `<span class="stock stock--out">${t("out_stock")}</span>`;
  if (p.stock <= 5) return `<span class="stock stock--low">${t("low_stock", { n: p.stock })}</span>`;
  return `<span class="stock">${t("in_stock")}</span>`;
}
function card(p, i = 0) {
  return `<article class="card reveal" style="--i:${i % 4}" data-id="${esc(p.id)}">
    <a class="card__media" href="#p-${esc(p.id)}" aria-label="${esc(p.name)}">
      <span class="card__sku">${esc(p.sku)}</span>
      <img src="${esc(imgOf(p))}" alt="${esc(p.name)}" loading="lazy" width="900" height="900">
    </a>
    <button class="fav ${favs.has(p.id) ? "is-on" : ""}" type="button" data-fav="${esc(p.id)}" aria-label="favourite">${heart}</button>
    <div class="card__body">
      <span class="card__series">${esc(p.series || "")}</span>
      <h3 class="card__name"><a href="#p-${esc(p.id)}">${esc(p.name)}</a></h3>
      <p class="card__short">${esc(p.short || "")}</p>
      ${stockBadge(p)}
      <div class="card__foot">
        <span class="price">${money(p.price)}</span>
        <button class="add" type="button" data-add="${esc(p.id)}" ${p.stock <= 0 ? "disabled" : ""}>${t("add")}</button>
      </div>
    </div>
  </article>`;
}

/* ---------- home ---------- */
function renderHome() {
  renderCategories();
  renderTicker();
  buildHero();
  renderFeatured();
  renderBrands();
  renderAbout();
  renderBranches();
}

function renderCategories() {
  const grid = $("#catGrid"); if (!grid) return;
  grid.innerHTML = C.map((c, i) => {
    const items = P.filter(p => p.cat === c.id), n = items.length, prod = items[0]?.images[0];
    return `<a class="cat ${prod ? "" : "cat--type"} reveal" style="--i:${i % 3}" href="#cat-${esc(c.id)}" data-catimg="${esc(c.img || "")}">
      <span class="cat__no">${String(i + 1).padStart(2, "0")}</span>
      <h3>${esc(c.name)}</h3><p>${esc(c.blurb || "")}</p>
      ${prod ? "" : `<span class="cat__subs">${c.subs.map(x => `<span>${esc(x)}</span>`).join("")}</span>`}
      ${prod ? "" : `<span class="cat__ghost">${esc(c.ghost)}</span>`}
      <span class="cat__n">${n ? `${n} ${t("items")} →` : t("ask_avail")}</span>
      ${prod ? `<img class="cat__prod" src="${esc(prod)}" alt="" loading="lazy">` : ""}
    </a>`;
  }).join("");
  $$("[data-catimg]", grid).forEach(tile => {
    const src = tile.dataset.catimg; if (!src) return;
    const im = new Image();
    im.onload = () => { tile.classList.add("cat--photo"); tile.classList.remove("cat--type"); tile.insertAdjacentHTML("afterbegin", `<img class="cat__photo" src="${esc(src)}" alt="">`); };
    im.src = src;
  });
}

function renderTicker() {
  const ticker = $("#ticker"); if (!ticker) return;
  const words = C.flatMap(c => [c.name, ...c.subs.slice(0, 2)]);
  const track = words.map(w => `<span>${esc(w)}</span>`).join("");
  ticker.innerHTML = track + track;
}

function renderFeatured() {
  const grid = $("#featGrid"); if (!grid) return;
  const limit = +grid.dataset.limit || 4;
  grid.innerHTML = P.slice().sort((a, b) => b.stock - a.stock).slice(0, limit).map(card).join("");
}

/* ---------- hero carousel ---------- */
let hi = 0, htimer;
const heroSlides = () => P.filter(p => p.stock > 0 && p.images.length);
function buildHero() {
  const obj = $("#heroObj"), sl = heroSlides();
  if (!obj || !sl.length) return;
  obj.innerHTML = sl.map(p => `<img src="${esc(p.images[0])}" alt="${esc(p.name)}" width="900" height="900">`).join("");
  $("#heroDots").innerHTML = sl.map((p, i) => `<button type="button" data-hero="${i}" aria-label="${esc(p.sku)}"><i></i></button>`).join("");
  setHero(hi);
}
function setHero(i) {
  const sl = heroSlides(); if (!sl.length || !$("#heroObj")) return;
  const prev = hi; hi = (i + sl.length) % sl.length; const p = sl[hi];
  $$("#heroObj img").forEach((im, k) => { im.classList.toggle("is-out", k === prev && k !== hi); im.classList.toggle("is-on", k === hi); });
  $$("#heroDots button").forEach((b, k) => { b.classList.remove("is-on"); if (k === hi) { void b.offsetWidth; b.classList.add("is-on"); } });
  const heroCard = $("#heroCard"); heroCard.classList.remove("swap"); void heroCard.offsetWidth; heroCard.classList.add("swap");
  heroCard.href = `#p-${p.id}`;
  $("#heroCat").textContent = (catById(p.cat)?.name || "") + (p.sub ? " · " + p.sub : "");
  $("#heroName").textContent = p.name;
  $("#heroPrice").innerHTML = money(p.price);
  $("#heroModel").textContent = p.sku;
  clearTimeout(htimer); htimer = setTimeout(() => setHero(hi + 1), 4500);
}

/* ---------- brands ---------- */
const brandCount = b => P.filter(p => p.brand === b.id).length;
const logoBox = (b, cls = "") => `<span class="logo ${cls}">${b.logo ? `<img src="${esc(b.logo)}" alt="${esc(b.name)}" loading="lazy">` : ""}<span class="wordmark">${esc(b.name)}</span></span>`;
function wireLogos(root) {
  if (!root) return;
  $$(".logo img", root).forEach(img => {
    const ok = () => img.closest(".logo")?.classList.add("has");
    const bad = () => img.remove();
    if (img.complete) { img.naturalWidth ? ok() : bad(); } else { img.addEventListener("load", ok, { once: true }); img.addEventListener("error", bad, { once: true }); }
  });
}
function renderBrands() {
  const grid = $("#brandGrid"); if (!grid) return;
  grid.innerHTML = B.map((b, i) => {
    const n = brandCount(b);
    const href = n ? `#brand-${b.id}` : waUrl(t("wa_ask_brand", { name: b.name }));
    const meta = [b.origin, b.line].filter(Boolean).map(esc).join(" · ");
    return `<a class="brand-t reveal" style="--i:${i % 3}" href="${esc(href)}" ${n ? "" : 'target="_blank" rel="noopener"'}>
      ${logoBox(b)}
      <span class="brand-t__meta"><b>${esc(b.name)}</b><small>${meta}</small></span>
      <span class="brand-t__n ${n ? "" : "is-soon"}">${n ? t("brand_n", { n }) : t("brand_ask")}</span>
    </a>`;
  }).join("");
  const band = B.map(b => logoBox(b, "logo--band")).join("");
  $("#logoBand").innerHTML = band + band + band;
  wireLogos($("#brands"));
}

/* ---------- about ---------- */
function renderAbout() {
  const since = $("#aboutSince"), stats = $("#aboutStats");
  if (!since || !stats) return;
  const years = S.since ? new Date().getFullYear() - S.since : null;
  since.innerHTML = S.since ? `<small>${t("since")}</small>${S.since}` : "";
  stats.innerHTML = [
    years ? [years + "+", t("stat_years")] : null,
    BR.length ? [BR.length, t("stat_branches")] : null,
    B.length ? [B.length, t("stat_brands")] : null,
    P.length ? [P.length + "+", t("stat_products")] : null
  ].filter(Boolean).map(([v, l]) => `<div><dt>${v}</dt><dd>${l}</dd></div>`).join("");
}

/* ---------- branches ---------- */
function renderBranches() {
  const grid = $("#branchGrid"); if (!grid) return;
  grid.innerHTML = BR.map((b, i) => `<article class="branch reveal" style="--i:${i}">
    <div class="branch__media">
      <span class="branch__city">${esc(b.city)}</span>
      ${b.photo ? `<img src="${esc(b.photo)}" alt="${esc(b.name)}" loading="lazy">` : ""}
      ${b.main ? `<span class="branch__tag">${t("main_branch")}</span>` : ""}
      <span class="branch__no">${String(i + 1).padStart(2, "0")}</span>
    </div>
    <div class="branch__body">
      <h3>${esc(b.name)}</h3>
      <p class="branch__addr">${esc(b.address)}</p>
      <dl class="branch__info">
        ${b.hours ? `<div><dt>${t("hours_l")}</dt><dd>${esc(b.hours)}</dd></div>` : ""}
        ${b.phone ? `<div><dt>${t("phone_l")}</dt><dd><a href="tel:${esc(b.phone.replace(/\s/g, ""))}" dir="ltr">${esc(b.phone)}</a></dd></div>` : ""}
      </dl>
      <div class="branch__act">
        ${b.map ? `<a class="btn btn--dark btn--sm" href="${esc(b.map)}" target="_blank" rel="noopener">${t("directions")}</a>` : ""}
        <a class="btn btn--line btn--sm" href="${esc(waUrl(t("wa_ask_branch", { name: b.name })))}" target="_blank" rel="noopener">WhatsApp</a>
      </div>
    </div>
  </article>`).join("");
  $$(".branch__media img", grid).forEach(img => {
    const ok = () => img.closest(".branch__media").classList.add("has-photo");
    if (img.complete) { img.naturalWidth ? ok() : img.remove(); } else { img.addEventListener("load", ok, { once: true }); img.addEventListener("error", () => img.remove(), { once: true }); }
  });
}

/* ---------- shop ---------- */
const F = { cats: new Set(), brands: new Set(), price: 0, stock: false, fav: false, q: "", sort: "feat" };
const prices = () => P.map(p => p.price);
const maxPrice = () => P.length ? Math.ceil(Math.max(...prices())) : 0;
const minPrice = () => P.length ? Math.floor(Math.min(...prices())) : 0;
function renderFilters() {
  $("#fCats").innerHTML = C.map(c => {
    const count = P.filter(p => p.cat === c.id).length, im = P.find(p => p.cat === c.id)?.images[0];
    const thumb = im ? `<img src="${esc(im)}" alt="">` : `<span class="fcat__ph">${esc(c.name.replace(/^ال/, "").charAt(0))}</span>`;
    return `<button type="button" class="fcat ${F.cats.has(c.id) ? "is-on" : ""}" data-fcat="${esc(c.id)}">${thumb}<span>${esc(c.name)}</span><small>${count}</small></button>`;
  }).join("");
  $("#fBrands").innerHTML = B.filter(brandCount).map(b => `<button type="button" class="fcat fcat--brand ${F.brands.has(b.id) ? "is-on" : ""}" data-fbrand="${esc(b.id)}">${logoBox(b, "logo--sm")}<span>${esc(b.name)}</span><small>${brandCount(b)}</small></button>`).join("");
  wireLogos($("#fBrands"));
  const r = $("#fPrice"); r.min = minPrice(); r.max = maxPrice(); r.value = F.price;
  $("#fPriceOut").innerHTML = money(+r.value);
  $("#fStock").checked = F.stock; $("#fFav").checked = F.fav; $("#sort").value = F.sort; $("#shopQ").value = F.q;
}
function renderShop() {
  const q = F.q.trim().toLowerCase();
  const list = P.filter(p =>
    (!F.cats.size || F.cats.has(p.cat)) && (!F.brands.size || F.brands.has(p.brand)) && p.price <= F.price &&
    (!F.stock || p.stock > 0) && (!F.fav || favs.has(p.id)) && (!q || p.search.includes(q)));
  const sorters = { low: (a, b) => a.price - b.price, high: (a, b) => b.price - a.price, feat: (a, b) => b.stock - a.stock };
  list.sort(sorters[F.sort] || sorters.feat);
  $("#shopGrid").innerHTML = list.map(card).join("");
  $("#shopEmpty").hidden = list.length > 0;
  const oneCat = F.cats.size === 1 && !F.q && !F.fav ? catById([...F.cats][0]) : null;
  const catEmpty = oneCat && !P.some(p => p.cat === oneCat.id);
  $("#shopEmpty h3").textContent = catEmpty ? t("empty_cat_t", { name: oneCat.name }) : t("empty_t");
  $("#shopEmpty p").textContent = catEmpty ? t("empty_cat_p") : t("empty_p");
  $("#emptyWa").hidden = !catEmpty;
  if (catEmpty) $("#emptyWa").href = waUrl(t("wa_ask_category", { name: oneCat.name }));
  $("#shopCount").textContent = t("results_n", { n: list.length });
  $("#shopTitle").textContent = F.fav ? t("favourites")
    : q ? t("search_for", { q: F.q })
    : F.cats.size === 1 ? (catById([...F.cats][0])?.name || t("all_products"))
    : F.brands.size === 1 ? (brandById([...F.brands][0])?.name || t("all_products"))
    : t("all_products");
  const chips = [];
  F.cats.forEach(id => chips.push(`<button class="chip" data-chip="cat:${esc(id)}">${esc(catById(id)?.name || id)}</button>`));
  F.brands.forEach(id => chips.push(`<button class="chip" data-chip="brand:${esc(id)}">${esc(brandById(id)?.name || id)}</button>`));
  if (F.q) chips.push(`<button class="chip" data-chip="q">“${esc(F.q)}”</button>`);
  if (F.price < maxPrice()) chips.push(`<button class="chip" data-chip="price">≤ ${esc(moneyTxt(F.price))}</button>`);
  if (F.stock) chips.push(`<button class="chip" data-chip="stock">${t("in_stock_only")}</button>`);
  if (F.fav) chips.push(`<button class="chip" data-chip="fav">${t("fav_only")}</button>`);
  $("#activeChips").innerHTML = chips.join("");
  observe();
}
function resetFilters() { F.cats.clear(); F.brands.clear(); F.price = maxPrice(); F.stock = F.fav = false; F.q = ""; renderFilters(); renderShop(); }

/* ---------- product page ---------- */
const isCutout = src => /\.png($|\?)/i.test(src);
function renderPDP(p) {
  const specs = [
    [t("sku"), p.sku, true],
    p.brandName ? [t("brand"), p.brandName, true] : null,
    ...p.specs.map(s => [s.label, s.value, s.ltr])
  ].filter(Boolean);
  const pairs = p.pairs.map(byId).filter(Boolean);
  const related = P.filter(x => x.id !== p.id && (x.cat === p.cat || pairs.includes(x))).slice(0, 4);
  const images = p.images.length ? p.images : [imgOf(p)];
  const series = [p.brandName, p.series].filter(Boolean).map(esc).join(" · ");
  $("#pdp").innerHTML = `
  <div class="crumbs"><a href="#home">${t("home")}</a><span>/</span><a href="#cat-${esc(p.cat)}">${esc(catById(p.cat)?.name || "")}</a><span>/</span><b dir="ltr">${esc(p.sku)}</b></div>
  <div class="pdp">
    <div class="gal">
      <div class="gal__thumbs">${images.map((src, i) => `<button type="button" class="${i ? "" : "is-on"}" data-thumb="${i}"><img src="${esc(src)}" alt=""></button>`).join("")}</div>
      <div class="gal__main" id="galMain">
        <img src="${esc(images[0])}" class="${isCutout(images[0]) ? "is-cut" : ""}" alt="${esc(p.name)}">
        <button class="fav ${favs.has(p.id) ? "is-on" : ""}" type="button" data-fav="${esc(p.id)}" aria-label="favourite">${heart}</button>
      </div>
    </div>
    <div class="info">
      <span class="info__series">${series}</span>
      <h1>${esc(p.name)}</h1>
      <p class="info__short">${esc(p.short || "")}</p>
      <div class="info__price"><div><span class="price">${money(p.price)}</span><small class="vat">${t("vat")}</small></div><span style="margin-inline-start:auto">${stockBadge(p)}</span></div>
      <div class="buy">
        <div class="qty" data-qty-wrap><button type="button" data-qd="-1" aria-label="-">−</button><input id="pdpQty" type="number" min="1" max="${Math.max(1, p.stock)}" value="1" inputmode="numeric"><button type="button" data-qd="1" aria-label="+">+</button></div>
        <button class="btn btn--primary btn--lg" type="button" data-add="${esc(p.id)}" data-from-pdp ${p.stock <= 0 ? "disabled" : ""}>${t("add")}</button>
        <button class="btn btn--dark btn--lg" type="button" data-buynow="${esc(p.id)}" ${p.stock <= 0 ? "disabled" : ""}>${t("buy_now")}</button>
      </div>
      ${ASSURANCES.length ? `<div class="assure">${ASSURANCES.map(a => `<div><b>${esc(a.title)}</b>${esc(a.text || "")}</div>`).join("")}</div>` : ""}
      <div class="specs"><h3>${t("specs_t")}</h3><table>${specs.map(([label, value, ltr]) => `<tr><th>${esc(label)}</th><td>${ltr ? `<span dir="ltr">${esc(value)}</span>` : esc(value)}</td></tr>`).join("")}</table></div>
      ${pairs.length ? `<div class="pairs"><h4>${t("pairs_t")}</h4>${pairs.map(x => `<div class="pair"><img src="${esc(imgOf(x))}" alt=""><div><b>${esc(x.name)}</b><small>${esc(x.short || "")}</small></div><button class="add" type="button" data-add="${esc(x.id)}" ${x.stock <= 0 ? "disabled" : ""}>+ ${money(x.price)}</button></div>`).join('<div style="height:10px"></div>')}</div>` : ""}
      <p style="margin-top:22px"><a class="link-arrow" target="_blank" rel="noopener" href="${esc(waUrl(t("wa_ask_product", { sku: p.sku })))}">${t("ask_wa")}</a></p>
    </div>
  </div>
  ${related.length ? `<section class="related"><h2>${t("related_t")}</h2><div class="grid">${related.map(card).join("")}</div></section>` : ""}`;
  observe();
}

/* ---------- cart ---------- */
function addToCart(id, qty = 1, srcImg) {
  const p = byId(id); if (!p || p.stock <= 0) return;
  const line = cart.find(i => i.id === id);
  if (line) line.qty = Math.min(p.stock, line.qty + qty); else cart.push({ id, qty: Math.min(p.stock, qty) });
  saveCart(); renderCart();
  if (srcImg) flyToCart(srcImg);
  toast(`<img src="${esc(imgOf(p))}" alt=""><span>${t("added_toast")}</span><a href="#" data-open-cart>${t("view_cart")}</a>`);
}
function setQty(id, q) {
  const p = byId(id), line = cart.find(i => i.id === id); if (!line) return;
  line.qty = Math.max(1, Math.min(p.stock, q)); saveCart(); renderCart(); if (current === "checkout") renderCheckout();
}
function removeLine(id) { cart = cart.filter(i => i.id !== id); saveCart(); renderCart(); if (current === "checkout") renderCheckout(); }
function renderCart() {
  const sub = cartSub(), empty = !cart.length;
  $("#cartEmpty").hidden = !empty; $("#cartFoot").hidden = empty; $("#shipBar").hidden = empty || S.freeDeliveryFrom <= 0;
  $("#cartItems").innerHTML = cart.map(i => { const p = byId(i.id); return `<li class="ci" data-line="${esc(p.id)}">
    <img src="${esc(imgOf(p))}" alt=""><div><b>${esc(p.name)}</b><small dir="ltr">${esc(p.sku)}</small>
    <div class="ci__row"><div class="qty qty--sm"><button type="button" data-cq="${esc(p.id)}" data-d="-1">−</button><input value="${i.qty}" data-cqi="${esc(p.id)}" inputmode="numeric" aria-label="qty"><button type="button" data-cq="${esc(p.id)}" data-d="1">+</button></div>
    <button class="ci__rm" type="button" data-rm="${esc(p.id)}">${t("remove")}</button></div></div>
    <span class="price">${money(p.price * i.qty)}</span></li>`; }).join("");
  $$("[data-cart-sub]").forEach(e => e.innerHTML = money(sub));
  if (S.freeDeliveryFrom > 0) {
    const left = S.freeDeliveryFrom - sub;
    $("#shipBar").innerHTML = `${left > 0 ? t("ship_left", { amount: esc(moneyTxt(left)) }) : t("ship_free")}<div class="ship__bar"><i style="width:${Math.min(100, sub / S.freeDeliveryFrom * 100)}%"></i></div>`;
  }
}
function paintCounts() {
  const n = cartQty();
  $$("[data-cart-count]").forEach(e => e.textContent = n);
  $$("[data-fav-count]").forEach(e => { e.textContent = favs.size; e.toggleAttribute("data-zero", !favs.size); });
}
function openCart() { renderCart(); $("#drawer").classList.add("is-open"); $("#drawer").setAttribute("aria-hidden", "false"); showScrim(true); }
function closeCart() { $("#drawer").classList.remove("is-open"); $("#drawer").setAttribute("aria-hidden", "true"); showScrim(false); }
function showScrim(on) {
  const s = $("#scrim");
  if (on) { s.hidden = false; requestAnimationFrame(() => s.classList.add("is-on")); document.body.style.overflow = "hidden"; }
  else { s.classList.remove("is-on"); setTimeout(() => s.hidden = true, 350); document.body.style.overflow = ""; }
}
function flyToCart(img) {
  const target = (innerWidth <= 720 ? $(".mbar button") : $("#cartBtn")); if (!target || !img.getBoundingClientRect) return;
  const a = img.getBoundingClientRect(), b = target.getBoundingClientRect();
  const f = img.cloneNode(); f.className = "fly"; f.removeAttribute("loading");
  Object.assign(f.style, { left: a.left + "px", top: a.top + "px", width: a.width + "px", height: a.height + "px" });
  document.body.appendChild(f);
  const dx = b.left + b.width / 2 - (a.left + a.width / 2), dy = b.top + b.height / 2 - (a.top + a.height / 2);
  const anim = f.animate([
    { transform: "translate(0,0) scale(1)", opacity: 1 },
    { transform: `translate(${dx * .45}px, ${dy * .45 - 120}px) scale(.55) rotate(-8deg)`, opacity: 1, offset: .5 },
    { transform: `translate(${dx}px, ${dy}px) scale(.08)`, opacity: .4 }
  ], { duration: 820, easing: "cubic-bezier(.5,0,.3,1)" });
  anim.onfinish = () => { f.remove(); target.classList.remove("bump"); void target.offsetWidth; target.classList.add("bump"); };
}

/* ---------- checkout ---------- */
let area = S.areas.some(a => a.id === store.get("area", null)) ? store.get("area", null) : S.areas[0]?.id;
const currentArea = () => S.areas.find(a => a.id === area) || S.areas[0];
const fee = () => { const a = currentArea(); return !a || freeDelivery(cartSub()) ? 0 : a.fee; };
function renderCheckout() {
  const sub = cartSub(), d = fee(), free = freeDelivery(sub);
  $("#coItems").innerHTML = !cart.length
    ? `<li style="display:block;color:var(--muted)">${t("co_empty")}</li>`
    : cart.map(i => { const p = byId(i.id); return `<li><span class="th"><img src="${esc(imgOf(p))}" alt=""><i>${i.qty}</i></span><b>${esc(p.name)}</b><span class="price">${money(p.price * i.qty)}</span></li>`; }).join("");
  $("#areas").innerHTML = S.areas.map(a => `<label class="area"><input type="radio" name="area" value="${a.id}" ${a.id === area ? "checked" : ""}><span><b>${esc(a.name)}</b><i>${free ? t("free") : esc(moneyTxt(a.fee))}</i></span></label>`).join("");
  $$("[data-co-sub]").forEach(e => e.innerHTML = money(sub));
  $$("[data-co-del]").forEach(e => e.innerHTML = d ? money(d) : `<span style="color:var(--ok)">${t("free")}</span>`);
  $$("[data-co-total]").forEach(e => e.innerHTML = money(sub + d));
  $("#placeBtn").disabled = !cart.length || !S.areas.length;
  renderCheckoutAccount();
}

/* Checkout parts that depend on the account: prefill, saved addresses, "open an account". */
let savedPick = null;
function renderCheckoutAccount() {
  const f = $("#coForm");
  $("#coAccount").innerHTML = me
    ? t("co_logged_as", { name: esc(me.customer.name) })
    : `${t("co_have_account")} <a href="#account" data-login-return="checkout">${t("co_login_link")}</a>`;
  $("#coSignup").hidden = !!me;

  const list = me?.addresses || [];
  const box = $("#coSaved");
  box.hidden = !list.length;
  box.innerHTML = list.length ? `<p>${t("co_saved_addresses")}</p>` + list.map(a => `<label class="area"><input type="radio" name="saved" value="${a.id}" ${a.id === savedPick ? "checked" : ""}><span><b>${esc(a.area || "")}</b><i>${esc(a.address)}</i></span></label>`).join("") : "";

  if (f.dataset.filled) return;
  const saved = me ? { ...me.customer } : store.get("customer", null);
  if (saved) ["name", "phone", "address"].forEach(k => { if (saved[k] && !fe(f, k).value) fe(f, k).value = saved[k]; });
  const preferred = list.find(a => a.default) || list[0];
  if (preferred) useSavedAddress(preferred.id);
  f.dataset.filled = 1;
}
function useSavedAddress(id) {
  const a = (me?.addresses || []).find(x => x.id === id); if (!a) return;
  savedPick = id;
  fe($("#coForm"), "address").value = a.address;
  if (S.areas.some(x => x.id === a.area_id)) { area = a.area_id; store.set("area", area); }
  renderCheckout();
}

const phoneOk = v => /^07[789]\d{7}$/.test(v.replace(/[\s-]/g, "").replace(/^(\+|00)?962/, "0"));

/* Show server validation errors next to their inputs; unmatched ones go to the form message. */
function formErrors(form, err) {
  $$("label.bad", form).forEach(l => l.classList.remove("bad"));
  const msg = $("[data-form-msg]", form);
  const leftovers = [];
  Object.entries(err?.errors || {}).forEach(([name, messages]) => {
    const label = form.elements.namedItem(name)?.closest?.("label");
    if (!label) { leftovers.push(messages[0]); return; }
    let em = label.querySelector(".err");
    if (!em) { em = document.createElement("em"); em.className = "err"; label.appendChild(em); }
    em.textContent = messages[0]; label.classList.add("bad");
  });
  const general = err?.status ? err.message : t("order_failed");    // no status = network failure
  const text = err ? (leftovers[0] || (Object.keys(err.errors || {}).length ? "" : general)) : "";
  if (msg) msg.textContent = text; else if (text) toast(`<span>${esc(text)}</span>`);
}

async function submitOrder(e) {
  e.preventDefault();
  const f = e.target; let ok = true;
  const v = k => fe(f, k).value.trim();
  const signup = !me && fe(f, "create_account").checked;
  const check = (name, valid) => { const lab = fe(f, name).closest("label"); lab.classList.toggle("bad", !valid); if (!valid && ok) { fe(f, name).focus(); ok = false; } };
  check("name", v("name").length > 1);
  check("phone", phoneOk(v("phone")));
  check("address", v("address").length > 3);
  if (signup) check("password", fe(f, "password").value.length >= 6);
  if (!ok || !cart.length || !currentArea()) return;

  const btn = $("#placeBtn"); btn.disabled = true;
  try {
    const data = await api("POST", S.orderUrl, {
      name: v("name"), phone: v("phone"), address: v("address"), notes: v("notes"),
      area_id: currentArea().id, payment_method: fe(f, "pay").value,
      items: cart.map(i => ({ id: i.id, qty: i.qty })),
      create_account: signup, password: signup ? fe(f, "password").value : null
    });
    const { account, ...order } = data;
    if (account) setAccount(account);
    store.set("customer", { name: order.name, phone: order.phone, address: order.address });
    store.set("lastOrder", order);
    fe(f, "password").value = "";
    cart = []; saveCart(); renderCart();
    location.hash = "done";
  } catch (err) {
    formErrors(f, err);
  } finally {
    btn.disabled = !cart.length;
  }
}
function orderText(o) {
  return [`*${t("wa_order")}* — ${o.no}`, "",
    ...o.items.map(i => `• ${i.qty} × ${i.sku} — ${moneyTxt(i.total)}`), "",
    `${t("subtotal")}: ${moneyTxt(o.sub)}`, `${t("delivery")}: ${o.fee ? moneyTxt(o.fee) : t("free")}`, `*${t("total")}: ${moneyTxt(o.total)}*`, "",
    `${t("wa_name")}: ${o.name}`, `${t("wa_phone")}: ${o.phone}`, `${t("wa_area")}: ${o.area}`, `${t("wa_addr")}: ${o.address}`,
    `${t("wa_pay")}: ${payLabel(o.pay)}`, o.notes ? `${t("wa_notes")}: ${o.notes}` : ""].filter((x, i, arr) => x !== "" || arr[i - 1] !== "").join("\n");
}
function renderDone() {
  const o = store.get("lastOrder", null);
  if (!o || !Array.isArray(o.items)) { location.hash = "shop"; return; }
  $("#done").innerHTML = `
    <svg class="done__check" viewBox="0 0 100 100"><circle cx="50" cy="50" r="46"/><path d="M30 52l13 13 27-29"/></svg>
    <h1>${t("done_t")}</h1><p>${t("done_p", { name: esc(o.name) })}</p>
    <span class="done__no">${t("order_no")}: <b dir="ltr">${esc(o.no)}</b></span>
    <div class="done__box">${orderLines(o.items)}
      <dl class="totals"><div><dt>${t("delivery")}</dt><dd>${o.fee ? money(o.fee) : t("free")}</dd></div><div class="grand"><dt>${t("total")}</dt><dd>${money(o.total)}</dd></div></dl>
      ${o.pay === "cliq" && S.cliqAlias ? `<p class="cliq">${t("cliq_note", { alias: esc(S.cliqAlias) })}</p>` : ""}</div>
    <div class="done__btns"><a class="btn btn--primary" target="_blank" rel="noopener" href="${esc(waUrl(orderText(o)))}">${t("send_wa")}</a>${me ? `<a class="btn btn--dark" href="#account">${t("acc_track")}</a>` : ""}<a class="btn btn--line" href="#shop">${t("continue")}</a></div>`;
}
const orderLines = items => `<ul class="sumlist">${items.map(i => `<li><span class="th"><img src="${esc(i.image || S.placeholder || "")}" alt=""><i>${i.qty}</i></span><b>${esc(i.name)}</b><span class="price">${money(i.total)}</span></li>`).join("")}</ul>`;

/* ---------- account ---------- */
let accTab = "orders";
function setAccount(state) {
  me = state;
  if (state) {
    favs = new Set(state.favourites.filter(byId));
    store.set("favs", [...favs]);
  }
  paintCounts(); paintAccountButton();
}
function paintAccountButton() { $("#accountBtn")?.classList.toggle("is-on", !!me); }
function renderAccount() {
  $("#accGuest").hidden = !!me; $("#accUser").hidden = !me;
  if (!me) {
    const phone = fe($("#loginForm"), "phone").value.trim();
    $("[data-forgot]").href = waUrl(t("acc_forgot_wa", { phone: phone || "—" }));
    return;
  }
  $("#accHello").textContent = t("acc_hello", { name: me.customer.name });
  $("#accPhone").textContent = me.customer.phone;
  $$("[data-acc-tab]").forEach(b => { b.classList.toggle("is-on", b.dataset.accTab === accTab); b.setAttribute("aria-selected", b.dataset.accTab === accTab); });
  $$("[data-acc-panel]").forEach(p => p.hidden = p.dataset.accPanel !== accTab);

  $("#accOrders").innerHTML = me.orders.length ? me.orders.map(o => `<article class="order">
      <header class="order__head"><span class="order__no" dir="ltr">${esc(o.no)}</span><span class="order__date" dir="ltr">${esc(o.date)}</span><span class="order__status order__status--${esc(o.status)}">${esc(o.status_label)}</span><span class="price">${money(o.total)}</span></header>
      ${orderLines(o.items)}
    </article>`).join("") : `<p class="acc__empty">${t("acc_no_orders")}</p>`;

  $("#accAddresses").innerHTML = me.addresses.length ? me.addresses.map(a => `<div class="addr">
      <div><b>${esc(a.area || "")}${a.default ? `<span class="tag">${t("acc_default")}</span>` : ""}</b><small>${esc(a.address)}</small></div>
      <div class="addr__act">
        ${a.default ? "" : `<button class="btn btn--line btn--sm" type="button" data-addr-default="${a.id}">${t("acc_make_default")}</button>`}
        <button class="btn btn--line btn--sm" type="button" data-addr-edit="${a.id}">${t("acc_edit")}</button>
        <button class="btn btn--line btn--sm" type="button" data-addr-del="${a.id}">${t("acc_delete")}</button>
      </div>
    </div>`).join("") : `<p class="acc__empty">${t("acc_no_addresses")}</p>`;
  if (!$("#addrAreas").children.length) {
    $("#addrAreas").innerHTML = S.areas.map((a, i) => `<label class="area"><input type="radio" name="area_id" value="${a.id}" ${i ? "" : "checked"}><span><b>${esc(a.name)}</b></span></label>`).join("");
  }

  const favList = P.filter(p => favs.has(p.id));
  $("#accFavourites").innerHTML = favList.length ? favList.map(card).join("") : `<p class="acc__empty">${t("acc_no_favourites")}</p>`;

  const profile = $("#profileForm");
  if (!profile.dataset.filled) { fe(profile, "name").value = me.customer.name; profile.dataset.filled = 1; }
  observe();
}
function editAddress(id) {
  const f = $("#addressForm"), a = me.addresses.find(x => x.id === id);
  f.reset();
  fe(f, "id").value = a ? a.id : "";
  if (a) {
    fe(f, "address").value = a.address; fe(f, "is_default").checked = a.default;
    const radio = $(`#addrAreas input[value="${a.area_id}"]`); if (radio) radio.checked = true;
  }
  $("[data-address-title]", f).textContent = t(a ? "acc_edit_address" : "acc_add_address");
  formErrors(f, null);
  if (a) f.scrollIntoView({ behavior: "smooth", block: "center" });
}
async function accountSubmit(form, run, after) {
  const btn = $("button[type=submit]", form); btn.disabled = true;
  try {
    formErrors(form, null);
    setAccount(await run());
    if (after) after();
    renderAccount();
  } catch (err) {
    formErrors(form, err);
  } finally {
    btn.disabled = false;
  }
}
function afterSignIn(form) {
  form.reset();
  toast(`<span>${t("acc_welcome")}</span>`);
  $("#coForm").dataset.filled = "";
  if (returnTo) { const to = returnTo; returnTo = null; location.hash = to; }
}
async function logout() {
  try { await api("POST", ACC.urls.logout); } catch { /* session already gone — sign out locally anyway */ }
  me = null; favs = new Set();
  store.set("favs", []); store.set("customer", null); store.set("lastOrder", null);
  $$("#profileForm, #coForm").forEach(f => { f.reset(); f.dataset.filled = ""; });
  accTab = "orders";
  paintCounts(); paintAccountButton(); renderAccount();
  toast(`<span>${t("acc_bye")}</span>`);
}

/* ---------- search ---------- */
let sugIdx = -1;
function suggest(q) {
  const d = $("#searchDrop"); q = q.trim().toLowerCase();
  if (!q) { d.classList.remove("is-open"); return; }
  const hits = P.filter(p => p.search.includes(q)).slice(0, 5);
  sugIdx = -1;
  d.innerHTML = hits.length
    ? hits.map(p => `<a class="sug" href="#p-${esc(p.id)}"><img src="${esc(imgOf(p))}" alt=""><span><b>${esc(p.name)}</b><small>${[p.sku, p.series].filter(Boolean).map(esc).join(" · ")}</small></span><em>${esc(moneyTxt(p.price))}</em></a>`).join("")
    : `<div class="sug-none">${t("search_none")}</div>`;
  d.classList.add("is-open");
}

/* ---------- routing ---------- */
let current = "home";
function route() {
  const h = decodeURIComponent(location.hash.slice(1)) || "home";
  let view = "home";
  closeCart(); closeFilters(); $("#searchDrop").classList.remove("is-open");
  if (h === "shop" || h === "favourites" || h.startsWith("cat-") || h.startsWith("brand-")) {
    view = "shop";
    if (h.startsWith("cat-")) { F.cats = new Set([h.slice(4)]); F.brands.clear(); F.fav = false; }
    if (h.startsWith("brand-")) { F.brands = new Set([h.slice(6)]); F.cats.clear(); F.fav = false; }
    if (h === "favourites") { F.fav = true; F.cats.clear(); }
    renderFilters(); renderShop();
  } else if (h.startsWith("p-") && byId(h.slice(2))) { view = "product"; renderPDP(byId(h.slice(2))); }
  else if (h === "checkout") { view = "checkout"; renderCheckout(); }
  else if (h === "done") { view = "done"; renderDone(); }
  else if (h === "account") { view = "account"; renderAccount(); }
  $$(".view").forEach(v => v.hidden = v.dataset.view !== view);
  if (view !== current || view === "product") scrollTo({ top: 0, behavior: "instant" });
  current = view;
  $$(".nav a, .mbar a").forEach(a => a.classList.toggle("is-on", (a.getAttribute("href") === "#shop" && view === "shop") || (a.getAttribute("href") === "#home" && view === "home" && !a.dataset.scroll)));
  observe();
}

/* ---------- utils ---------- */
function fe(f, n) { return f.elements.namedItem(n); }
function esc(s) { return String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c])); }
function waUrl(text) { return `https://wa.me/${S.whatsapp}?text=${encodeURIComponent(text)}`; }
function toast(html) {
  const el = document.createElement("div"); el.className = "toast"; el.innerHTML = html;
  $("#toasts").appendChild(el); setTimeout(() => el.remove(), 3300);
}
function openFilters() { $("#filters").classList.add("is-open"); showScrim(true); }
function closeFilters() { if ($("#filters").classList.contains("is-open")) { $("#filters").classList.remove("is-open"); showScrim(false); } }

/* ---------- motion ---------- */
let io;
function observe() {
  if (!("IntersectionObserver" in window)) { $$(".reveal").forEach(e => e.classList.add("in")); return; }
  io = io || new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add("in"); io.unobserve(e.target); } }), { rootMargin: "0px 0px -8% 0px", threshold: .08 });
  $$(".reveal:not(.in)").forEach(e => io.observe(e));
}
function countUp(el) {
  const to = +el.dataset.count, t0 = performance.now();
  const step = now => { const k = Math.min(1, (now - t0) / 1200); el.textContent = Math.round(to * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(step); };
  requestAnimationFrame(step);
}
function heroMotion() {
  const hero = $(".hero"), stage = $("#heroStage"), obj = $("#heroObj");
  if (!hero || !stage || !obj) return;
  requestAnimationFrame(() => setTimeout(() => { hero.classList.add("in"); $(".hero__title").classList.add("in"); $$("[data-count]").forEach(countUp); }, 120));
  stage.addEventListener("pointerenter", () => clearTimeout(htimer));
  stage.addEventListener("pointerleave", () => { htimer = setTimeout(() => setHero(hi + 1), 2500); });
  if (matchMedia("(hover:hover) and (pointer:fine)").matches && !matchMedia("(prefers-reduced-motion:reduce)").matches) {
    stage.addEventListener("pointermove", e => {
      const r = stage.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      obj.style.transform = `rotateY(${x * 14}deg) rotateX(${-y * 10}deg) translateZ(0)`;
    });
    stage.addEventListener("pointerleave", () => { obj.style.transform = ""; });
  }
}

/* ---------- events ---------- */
document.addEventListener("click", e => {
  const el = e.target.closest("[data-add],[data-buynow],[data-fav],[data-open-cart],[data-close-cart],[data-cq],[data-rm],[data-fcat],[data-chip],[data-thumb],[data-qd],[data-scroll],[data-lang-link],[data-go-fav],[data-open-filters],[data-close-filters],[data-fbrand],[data-hero],[data-acc-tab],[data-logout],[data-addr-edit],[data-addr-del],[data-addr-default],[data-login-return],#galMain,.lightbox");
  if (!el) return;
  const d = el.dataset;
  if (d.add) {
    const qty = d.fromPdp != null ? Math.max(1, parseInt($("#pdpQty").value) || 1) : 1;
    const img = el.closest(".card,.pdp,.pair")?.querySelector(".card__media img,#galMain img,.pair img");
    addToCart(d.add, qty, img);
    if (el.classList.contains("add")) { const txt = el.innerHTML; el.classList.add("ok"); el.textContent = t("added"); setTimeout(() => { el.classList.remove("ok"); el.innerHTML = txt; }, 1300); }
  } else if (d.buynow) {
    const qty = Math.max(1, parseInt($("#pdpQty")?.value) || 1);
    if (!cart.some(i => i.id === d.buynow)) { cart.push({ id: d.buynow, qty }); saveCart(); renderCart(); }
    location.hash = "checkout";
  } else if (d.fav) {
    e.preventDefault();
    const on = !favs.has(d.fav); on ? favs.add(d.fav) : favs.delete(d.fav); saveFavs();
    $$(`[data-fav="${CSS.escape(d.fav)}"]`).forEach(b => { b.classList.toggle("is-on", on); b.classList.remove("pop"); void b.offsetWidth; b.classList.add("pop"); });
    toast(`<img src="${esc(imgOf(byId(d.fav)))}" alt=""><span>${t(on ? "fav_added" : "fav_removed")}</span>`);
    if (current === "shop" && F.fav) renderShop();
    if (current === "account") renderAccount();
  } else if (d.openCart != null) { e.preventDefault(); openCart(); }
  else if (d.closeCart != null) { closeCart(); }
  else if (d.cq) { const l = cart.find(i => i.id === d.cq); setQty(d.cq, l.qty + +d.d); }
  else if (d.rm) { const li = el.closest(".ci"); li.classList.add("out"); setTimeout(() => removeLine(d.rm), 300); }
  else if (d.hero != null) { setHero(+d.hero); }
  else if (d.fbrand) { F.brands.has(d.fbrand) ? F.brands.delete(d.fbrand) : F.brands.add(d.fbrand); renderFilters(); renderShop(); }
  else if (d.fcat) { F.cats.has(d.fcat) ? F.cats.delete(d.fcat) : F.cats.add(d.fcat); renderFilters(); renderShop(); }
  else if (d.chip) {
    const [k, v] = d.chip.split(":");
    if (k === "cat") F.cats.delete(v); if (k === "brand") F.brands.delete(v); if (k === "q") F.q = ""; if (k === "price") F.price = maxPrice(); if (k === "stock") F.stock = false; if (k === "fav") F.fav = false;
    renderFilters(); renderShop();
  }
  else if (d.thumb != null) {
    const p = byId(location.hash.slice(3)); if (!p) return;
    const src = p.images[+d.thumb], img = $("#galMain img"); if (!src) return;
    $$("[data-thumb]").forEach(b => b.classList.toggle("is-on", b === el));
    img.style.opacity = 0; setTimeout(() => { img.src = src; img.classList.toggle("is-cut", isCutout(src)); img.style.opacity = 1; }, 180);
  }
  else if (d.qd) { const i = $("#pdpQty"); i.value = Math.max(1, Math.min(+i.max, (+i.value || 1) + +d.qd)); }
  else if (d.scroll) {
    e.preventDefault();
    const go = () => $("#" + CSS.escape(d.scroll))?.scrollIntoView({ behavior: "smooth", block: "start" });
    if (current !== "home") { location.hash = "home"; setTimeout(go, 80); } else go();
  }
  else if (d.langLink != null) { el.href = el.href.split("#")[0] + location.hash; }
  else if (d.goFav != null) { location.hash = "favourites"; }
  else if (d.accTab) { accTab = d.accTab; renderAccount(); }
  else if (d.logout != null) { logout(); }
  else if (d.loginReturn) { returnTo = d.loginReturn; }
  else if (d.addrEdit) { editAddress(+d.addrEdit); }
  else if (d.addrDel) {
    if (!confirm(t("acc_confirm_delete"))) return;
    api("DELETE", `${ACC.urls.addresses}/${+d.addrDel}`).then(s => { setAccount(s); renderAccount(); }).catch(err => toast(`<span>${esc(err.message)}</span>`));
  }
  else if (d.addrDefault) {
    const a = me.addresses.find(x => x.id === +d.addrDefault);
    api("PUT", `${ACC.urls.addresses}/${a.id}`, { area_id: a.area_id, address: a.address, is_default: true })
      .then(s => { setAccount(s); renderAccount(); }).catch(err => toast(`<span>${esc(err.message)}</span>`));
  }
  else if (d.openFilters != null) openFilters();
  else if (d.closeFilters != null) closeFilters();
  else if (el.id === "galMain" && !e.target.closest(".fav")) {
    const lb = document.createElement("div"); lb.className = "lightbox";
    lb.innerHTML = `<img src="${esc($("#galMain img").src)}" alt=""><button class="x" type="button" aria-label="close">×</button>`;
    document.body.appendChild(lb);
  }
  else if (el.classList.contains("lightbox")) el.remove();
});
$("#scrim").addEventListener("click", () => { closeCart(); closeFilters(); });
document.addEventListener("keydown", e => { if (e.key === "Escape") { closeCart(); closeFilters(); $(".lightbox")?.remove(); $("#searchDrop").classList.remove("is-open"); } });
document.addEventListener("input", e => {
  const el = e.target;
  if (el.id === "fPrice") { F.price = +el.value; $("#fPriceOut").innerHTML = money(F.price); renderShop(); }
  else if (el.id === "shopQ") { F.q = el.value; renderShop(); }
  else if (el.id === "q") suggest(el.value);
  else if (el.closest("#loginForm") && el.name === "phone") $("[data-forgot]").href = waUrl(t("acc_forgot_wa", { phone: el.value.trim() || "—" }));
  else if (el.dataset.cqi) { const v = parseInt(el.value); if (v > 0) setQty(el.dataset.cqi, v); }
  else if (el.closest("#coForm label.bad")) el.closest("label").classList.remove("bad");
});
document.addEventListener("change", e => {
  const el = e.target;
  if (el.id === "fStock") { F.stock = el.checked; renderShop(); }
  else if (el.id === "fFav") { F.fav = el.checked; renderShop(); }
  else if (el.id === "sort") { F.sort = el.value; renderShop(); }
  else if (el.name === "area") { area = +el.value; savedPick = null; store.set("area", area); renderCheckout(); }
  else if (el.name === "saved") { useSavedAddress(+el.value); }
  else if (el.name === "create_account") { $("[data-signup-fields]").hidden = !el.checked; }
});
$("#q").addEventListener("keydown", e => {
  const items = $$("#searchDrop .sug");
  if (e.key === "ArrowDown" || e.key === "ArrowUp") {
    e.preventDefault(); if (!items.length) return;
    sugIdx = (sugIdx + (e.key === "ArrowDown" ? 1 : -1) + items.length) % items.length;
    items.forEach((it, i) => it.classList.toggle("is-act", i === sugIdx));
  } else if (e.key === "Enter") {
    e.preventDefault();
    if (sugIdx >= 0 && items[sugIdx]) location.hash = items[sugIdx].getAttribute("href").slice(1);
    else { F.q = e.target.value; F.cats.clear(); F.fav = false; if (location.hash === "#shop") { renderFilters(); renderShop(); } else location.hash = "shop"; }
    e.target.blur(); $("#searchDrop").classList.remove("is-open");
  }
});
$("#q").addEventListener("focus", e => e.target.value && suggest(e.target.value));
document.addEventListener("pointerdown", e => { if (!e.target.closest("#search")) $("#searchDrop").classList.remove("is-open"); });
$("#coForm").addEventListener("submit", submitOrder);

/* account forms */
const formData = form => Object.fromEntries(new FormData(form));
const submitWith = (selector, handler) => $(selector).addEventListener("submit", e => { e.preventDefault(); handler(e.target); });
submitWith("#loginForm", f => accountSubmit(f,
  () => api("POST", ACC.urls.login, { ...formData(f), remember: fe(f, "remember").checked, favourites: [...favs] }),
  () => afterSignIn(f)));
submitWith("#registerForm", f => accountSubmit(f,
  () => api("POST", ACC.urls.register, { ...formData(f), favourites: [...favs] }),
  () => afterSignIn(f)));
submitWith("#profileForm", f => accountSubmit(f,
  () => api("PUT", ACC.urls.profile, formData(f)),
  () => { ["current_password", "password", "password_confirmation"].forEach(k => fe(f, k).value = ""); toast(`<span>${t("acc_saved")}</span>`); }));
submitWith("#addressForm", f => {
  const { id, ...data } = formData(f);
  const body = { ...data, area_id: +data.area_id, is_default: fe(f, "is_default").checked };
  accountSubmit(f,
    () => id ? api("PUT", `${ACC.urls.addresses}/${+id}`, body) : api("POST", ACC.urls.addresses, body),
    () => { editAddress(null); toast(`<span>${t("acc_saved")}</span>`); });
});
$("#addressForm").addEventListener("reset", e => {
  fe(e.target, "id").value = "";
  $("[data-address-title]", e.target).textContent = t("acc_add_address");
  formErrors(e.target, null);
});
$("#emptyReset").addEventListener("click", resetFilters);
$("#fReset").addEventListener("click", resetFilters);
$("#bulkForm")?.addEventListener("submit", e => {
  e.preventDefault();
  const f = e.target, nm = fe(f, "name"), ph = fe(f, "phone"), okName = nm.value.trim().length > 1, okPhone = phoneOk(ph.value);
  nm.classList.toggle("bad", !okName); ph.classList.toggle("bad", !okPhone);
  if (!okName || !okPhone) { $("#bulkMsg").textContent = t("bulk_err"); return; }
  const url = waUrl(`*${t("wa_bulk")}*\n${t("wa_name")}: ${nm.value.trim()}\n${t("wa_phone")}: ${ph.value.trim()}\n\n${fe(f, "list").value.trim()}`);
  $("#bulkMsg").innerHTML = `${t("bulk_ok")} <a href="${esc(url)}" target="_blank" rel="noopener">${t("bulk_open")}</a>.`;
  window.open(url, "_blank", "noopener");
});
addEventListener("scroll", () => $("#hdr").classList.toggle("is-stuck", scrollY > 8), { passive: true });
addEventListener("hashchange", route);

/* ---------- boot ---------- */
F.price = maxPrice();
renderHome(); renderCart(); paintCounts(); paintAccountButton();
if (me && favs.size !== me.favourites.length) saveFavs();   // push favourites collected before signing in
route(); heroMotion(); observe();
})();
