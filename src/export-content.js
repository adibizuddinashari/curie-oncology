// Extracts Doctors, Locations and Articles from the hand-built static pages
// into wp-seed/content.json, which wp-seed/import.php loads into WordPress.
// Run once when setting up an install: `node src/export-content.js`.
//
// It reads the exact text already approved on the static site; it never
// invents content. Any value it can't find is left empty and reported.

const fs = require("fs");
const path = require("path");

const SRC = path.join(__dirname, "pages");
const OUT = path.join(__dirname, "..", "wp-seed", "content.json");
const read = (f) => fs.readFileSync(path.join(SRC, f), "utf8");
const warnings = [];

const decode = (s) =>
  String(s)
    .replace(/<br\s*\/?>/g, "\n")
    .replace(/<wbr\s*\/?>/g, "")
    .replace(/<[^>]+>/g, "")
    .replace(/&amp;/g, "&")
    .replace(/&rarr;/g, "→")
    .replace(/&nbsp;/g, " ")
    .replace(/&#39;|&rsquo;/g, "’")
    .replace(/&quot;/g, '"')
    .replace(/[ \t]+\n/g, "\n")
    .trim();

// Like decode(), but keeps <em>/<strong> (e.g. book titles in recognition lists).
const decodeRich = (s) =>
  decode(String(s).replace(/<(\/?)(em|strong)(?:\s[^>]*)?>/g, "[[$1$2]]")).replace(/\[\[(\/?)(em|strong)\]\]/g, "<$1$2>");

const one = (html, re, label) => {
  const m = html.match(re);
  if (!m) warnings.push(`missing: ${label}`);
  return m ? m[1] : "";
};
const all = (html, re) => [...html.matchAll(re)].map((m) => m[1]);
const section = (html, start, end) => {
  const a = html.indexOf(start);
  if (a < 0) return "";
  const b = end ? html.indexOf(end, a + start.length) : html.length;
  return html.slice(a, b < 0 ? html.length : b);
};
const asset = (url) => url.replace(/^assets\//, "");

// Card style → [image path, crop box without the image].
function cardStyle(style) {
  const img = one(style, /background-image:url\('([^']+)'\)/, "card image");
  const crop = style.replace(/background-image:url\('[^']+'\);?/, "");
  return [asset(img), crop];
}

/* ------------------------------------------------------------- Locations */
const contact = read("contact-us.html");
const locationCards = all(contact, /(<!-- Location card:[\s\S]*?<\/article>)/g);
const LOCATION_KEYS = ["kl", "penang"];
const locations = locationCards.map((card, i) => {
  const hoursHtml = one(card, /<span class="pt-1\.5">([\s\S]*?)<\/span><\/li>/, "location hours");
  const rows = hoursHtml.split(/<br\s*\/?>/).map((r) => r.trim()).filter(Boolean);
  let heading = "";
  let closed = "";
  const hours = [];
  rows.forEach((row, r) => {
    const bold = row.match(/^<strong[^>]*>([^<]*)<\/strong>(.*)$/);
    if (bold && !bold[2]) {
      // A whole-line bold row: the heading (first) or the closed note (last).
      if (r === 0 && rows.length > 1 && !/closed/i.test(bold[1])) heading = decode(bold[1]);
      else closed = decode(bold[1]);
      return;
    }
    const text = decode(row);
    const [days, ...time] = text.split(": ");
    hours.push({ days: days.trim(), time: time.join(": ").trim() });
  });
  const style = one(card, /style="background-image: url\('([^']+)'\);?\s*(?:background-position:\s*([^;"]+);?)?"/, "location photo");
  const position = (card.match(/background-position:\s*([^;"]+)/) || [, "50% 50%"])[1].trim();
  const footerLabels = ["Kuala Lumpur", "Penang"];
  return {
    key: LOCATION_KEYS[i],
    title: decode(one(card, /<h3[^>]*>([\s\S]*?)<\/h3>/, "location name")),
    show_on_contact: true,
    menu_order: i,
    eyebrow: decode(one(card, /<p class="section-eyebrow">([\s\S]*?)<\/p>/, "eyebrow")),
    footer_label: footerLabels[i],
    address: decode(one(card, /<\/svg><\/span>([^<]+)<\/li>/, "address")),
    phone: decode(one(card, /<a href="tel:[^"]*" class="hover:text-forest">([^<]+)<\/a>/, "phone")),
    email: decode((card.match(/href="mailto:([^"]+)"/) || [, ""])[1]),
    hours_heading: heading,
    hours,
    closed_note: closed,
    map_url: one(card, /<a href="(https:\/\/maps[^"]+)"/, "map"),
    website: "",
    photo: asset(style),
    photo_position: card.includes("bg-center") ? "50% 50%" : position,
    photo_alt: decode(one(card, /role="img" aria-label="([^"]+)"/, "photo alt")),
  };
});

/* --------------------------------------------------------------- Doctors */
const meet = read("meet-our-doctors.html");
const home = read("home.html");
const regionOf = {};
for (const [region, sectionHtml] of [
  ["klang-valley", section(meet, "<!-- Klang Valley -->", "<!-- Penang -->")],
  ["penang", section(meet, "<!-- Penang -->", "<!-- CTA Banner -->")],
]) {
  for (const slug of all(sectionHtml, /<a href="(dr-[a-z-]+)\.html"/g)) regionOf[slug] = region;
}
const order = all(meet, /<a href="(dr-[a-z-]+)\.html"/g);

// "Other doctors" crops: one crop per doctor. A few profiles framed the same
// doctor slightly differently, so take the crop used on the most profiles.
const otherCrops = {};
const cropVotes = {};
for (const f of fs.readdirSync(SRC).filter((f) => /^dr-.*\.html$/.test(f))) {
  for (const m of read(f).matchAll(/<a href="(dr-[a-z-]+)\.html" class="doctor-card other-doctor[\s\S]*?style="([^"]+)"/g)) {
    const votes = (cropVotes[m[1]] = cropVotes[m[1]] || {});
    votes[m[2]] = (votes[m[2]] || 0) + 1;
  }
}
for (const [slug, votes] of Object.entries(cropVotes)) {
  const ranked = Object.entries(votes).sort((a, b) => b[1] - a[1]);
  otherCrops[slug] = cardStyle(ranked[0][0]);
  if (ranked.length > 1) warnings.push(`${slug}: "other doctors" crop differed on ${ranked.slice(1).reduce((n, r) => n + r[1], 0)} profile(s); using the common one`);
}

const partners = {};
const doctors = order.map((slug, menu_order) => {
  const html = read(`${slug}.html`);
  const heroHtml = section(html, "<!-- Profile hero", "<!-- About + Schedule");
  const aboutHtml = section(html, "<!-- About + Schedule", "<!-- Education & Recognition");
  const eduHtml = section(html, "<!-- Education & Recognition", "<!-- Other doctors");
  const [aboutCol, sideCol] = aboutHtml.split('<div class="space-y-6">');

  const homeCard = home.match(new RegExp(`<a href="${slug}\\.html"[\\s\\S]*?style="([^"]+)"`));
  const gridCard = meet.match(new RegExp(`<a href="${slug}\\.html"[\\s\\S]*?style="([^"]+)"[\\s\\S]*?doctor-card__role">([^<]+)<`));
  const [portrait, cropHome] = homeCard ? cardStyle(homeCard[1]) : ["", ""];
  const [card, cropGrid] = gridCard ? cardStyle(gridCard[1]) : ["", ""];
  const [, cropOther] = otherCrops[slug] || ["", ""];

  const infobox = (label) => decode(one(heroHtml, new RegExp(`>${label}</p><p class="text-\\[15px\\] leading-\\[22px\\]">([\\s\\S]*?)</p>`), `${slug} ${label}`));

  // Specialties: each <h3> block under About.
  const details = all(aboutCol, /(<h3[\s\S]*?)(?=<div>\s*<h3|<\/div>\s*<\/div>\s*$)/g).map((block) => {
    const items = all(block, /<li[^>]*>[\s\S]*?<span>([\s\S]*?)<\/span><\/li>/g).map(decodeRich);
    return {
      heading: decode(one(block, /<h3[^>]*>([\s\S]*?)<\/h3>/, `${slug} detail heading`)),
      text: items.length ? items.join("\n") : decodeRich(one(block, /<p[^>]*>([\s\S]*?)<\/p>/, `${slug} detail text`)),
    };
  });

  // Visiting locations: partner hospitals become Location entries.
  const visitingHtml = section(sideCol || "", "Visiting Locations");
  const visiting = [];
  if (visitingHtml) {
    const names = all(visitingHtml, /<p class="(?:mt-3 )?text-sm (?:font-medium )?leading-relaxed17 text-body">([^<]+)<\/p>/g).map(decode);
    const sites = all(visitingHtml, /<a href="(https?:[^"]+)" target="_blank"/g);
    names.forEach((name, i) => {
      const key = name.toLowerCase().replace(/[^a-z]+/g, "-").replace(/^-|-$/g, "");
      partners[key] = partners[key] || { key, title: name, show_on_contact: false, menu_order: 10 + Object.keys(partners).length, website: sites[i] || "" };
      visiting.push(key);
    });
  }

  const schedule = all(sideCol || "", /<div class="flex justify-between[^"]*"><span>([\s\S]*?)<\/span><\/div>/g).map((row) => {
    const [day, time] = row.split(/<\/span><span[^>]*>/);
    return { day: decode(day), time: decode(time) };
  });

  const cutoutTag = one(heroHtml, /(<img src="assets\/img\/doctors\/cutouts[^>]+>)/, `${slug} cutout`);
  const region = regionOf[slug];

  return {
    slug,
    title: decode(one(html, /<h1[^>]*>([\s\S]*?)<\/h1>/, `${slug} name`)),
    menu_order,
    role: gridCard ? decode(gridCard[2]) : "",
    title_line: decode(one(heroHtml, /<\/h1>\s*<p[^>]*>([\s\S]*?)<\/p>/, `${slug} title line`)),
    region,
    show_on_home: Boolean(homeCard),
    clinic: region === "penang" ? "penang" : "kl",
    clinic_address_profile: infobox("Clinic"),
    summary: decodeRich(one(heroHtml, /<p class="text-\[17px\] leading-\[28px\][^"]*">([\s\S]*?)<\/p>/, `${slug} summary`)),
    highlights: all(heroHtml, /<li class="flex gap-2\.5[^"]*">[\s\S]*?<span>([\s\S]*?)<\/span><\/li>/g).map(decodeRich),
    hours_summary: infobox("Clinic hours"),
    languages: infobox("Languages"),
    schedule,
    schedule_note: all(sideCol || "", /<p class="pt-3 text-xs text-eucalyptus-text">([\s\S]*?)<\/p>/g).map(decode).join("\n"),
    visiting_locations: visiting,
    about: all(aboutCol, /<div class="mt-6 space-y-5[^"]*">([\s\S]*?)<\/div>/g)
      .flatMap((block) => all(block, /<p>([\s\S]*?)<\/p>/g))
      .map(decodeRich)
      .join("\n\n"),
    details,
    education: all(eduHtml, /<p class="text-sm font-medium text-clay-text">([\s\S]*?)<\/p>\s*<p class="mt-1 text-body">([\s\S]*?)<\/p>/g).length
      ? [...eduHtml.matchAll(/<p class="text-sm font-medium text-clay-text">([\s\S]*?)<\/p>\s*<p class="mt-1 text-body">([\s\S]*?)<\/p>/g)].map((m) => ({ label: decode(m[1]), text: decode(m[2]) }))
      : [],
    recognition_title: decode(all(eduHtml, /<h2[^>]*>([\s\S]*?)<\/h2>/g)[1] || ""),
    recognition: all(section(eduHtml, "<ul"), /<li class="flex gap-3 text-body">[\s\S]*?<span>([\s\S]*?)<\/span><\/li>/g).map((t) => ({ text: decodeRich(t) })),
    photo_portrait: portrait,
    photo_card: card,
    photo_cutout: asset(one(cutoutTag, /src="([^"]+)"/, `${slug} cutout src`)),
    cutout_flip: cutoutTag.includes("-scale-x-100"),
    crop_home: cropHome,
    crop_grid: cropGrid,
    crop_other: cropOther,
    seo_description: "",
  };
});

// Profile "Clinic" text is the location's long-form address.
for (const loc of locations) {
  const texts = [...new Set(doctors.filter((d) => d.clinic === loc.key).map((d) => d.clinic_address_profile))];
  if (texts.length > 1) warnings.push(`${loc.key}: doctors show different clinic addresses: ${texts.join(" | ")}`);
  loc.address_profile = texts[0] && texts[0] !== loc.address ? texts[0] : "";
}

// SEO descriptions come from src/build.js.
const { pages } = require("./build");
for (const d of doctors) d.seo_description = (pages.find((p) => p.file === `${d.slug}.html`) || {}).description || "";

/* -------------------------------------------------------------- Articles */
const articlesHtml = read("health-education.html");
const topics = {};
const articles = all(articlesHtml, /(<article[\s\S]*?<\/article>)/g).map((a, menu_order) => {
  const labels = [...a.matchAll(/<span class="[^"]*text-xs font-medium uppercase tracking-wide (text-[a-z-]+)">([\s\S]*?)<\/span>/g)].map((m) => {
    const name = decode(m[2]);
    topics[name] = { "text-honey-text": "honey", "text-clay-text": "clay" }[m[1]] || "forest";
    return name;
  });
  return {
    title: decode(one(a, /<h3[^>]*>([\s\S]*?)<\/h3>/, "article title")),
    menu_order,
    topics: labels,
    image: asset(one(a, /<img src="([^"]+)"/, "article image")),
    image_position: ((a.match(/object-\[([0-9%_]+)\]/) || [, ""])[1] || "").replace("_", " "),
    excerpt: decode(one(a, /<p class="mt-3[^"]*">([\s\S]*?)<\/p>/, "article excerpt")),
    url: one(a, /<a href="([^"]+)" target="_blank"/, "article url"),
    link_label: decode(one(a, /class="mt-5[^"]*">([^<]+?)\s*<svg/, "article link label")),
  };
});

const content = {
  generated: new Date().toISOString(),
  locations: [...locations, ...Object.values(partners)],
  doctors,
  topics: Object.entries(topics).map(([name, colour]) => ({ name, colour })),
  articles,
};

fs.mkdirSync(path.dirname(OUT), { recursive: true });
fs.writeFileSync(OUT, JSON.stringify(content, null, 2) + "\n");
console.log(`wrote ${path.relative(process.cwd(), OUT)}: ${content.locations.length} locations, ${doctors.length} doctors, ${content.topics.length} topics, ${articles.length} articles`);
if (warnings.length) console.log("warnings:\n  " + warnings.join("\n  "));
